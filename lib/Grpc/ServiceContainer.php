<?php

declare(strict_types=1);
namespace Minichan\Grpc;

use Exception;
use Minichan\Config\Status;
use Minichan\Exception\InvokeException;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionObject;
use Throwable;
use TypeError;

use function is_string;

final class ServiceContainer
{
    /** @var string The name of the service. */
    private string $name;

    /** @var ServiceInterface The service instance. */
    private ServiceInterface $service;

    /** @var array An array of discovered methods for the service. */
    private array $methods;

    /**
     * ServiceContainer constructor.
     *
     * @param string            $interface 
     * @param ServiceInterface  $service   
     *
     * @throws InvokeException If there is an issue with service discovery.
     */
    public function __construct(string $interface, ServiceInterface $service)
    {
        try {
            // Attempt to reflect on the service interface.
            $reflection = new ReflectionClass($interface);

            // Check if the NAME constant exists.
            if (!$reflection->hasConstant('NAME')) {
                throw new InvokeException("Can't find NAME of the service: {$interface}");
            }

            // Retrieve the NAME constant value.
            $name = $reflection->getConstant('NAME');

            // Check if the NAME is a string.
            if (!is_string($name)) {
                throw new InvokeException("Can't find NAME of the service: {$interface}");
            }

            $this->name = $name;
        } catch (ReflectionException $e) {
            // Handle reflection exception.
            throw new InvokeException($e->getMessage(), Status::INTERNAL, $e);
        }

        // Check if the provided service instance implements the specified interface.
        if (!$service instanceof $interface) {
            throw new InvokeException("The provided service instance does not implement the specified interface: {$interface}");
        }

        $this->service = $service;

        // Discover and store the methods for the service.
        try {
            $this->methods = $this->discoverMethods($service);
        } catch (Exception $e) {
            throw new InvokeException("Error discovering methods: {$e->getMessage()}", Status::INTERNAL, $e);
        }
    }

    /**
     * Get the name of the service.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the service instance.
     *
     * @return ServiceInterface
     */
    public function getService(): ServiceInterface
    {
        return $this->service;
    }

    /**
     * Get the discovered methods for the service.
     *
     * @return array
     */
    public function getMethods(): array
    {
        return array_values($this->methods);
    }

    /**
     * Handle a GRPC request for a specific method.
     *
     * @param Request $request The GRPC request.
     *
     * @return string The serialized output of the method execution.
     */
    public function handle(Request $request): string
    {
        $method  = $request->getMethod();
        $context = $request->getContext();
        $input   = $request->getPayload();

        // Check if the requested method exists in the discovered methods.
        if (!isset($this->methods[$method])) {
            throw new InvokeException("Method '{$method}' not found in service '{$this->name}'");
        }

        $callable = [$this->service, $method];

        $class = $this->methods[$method]['inputClass']->getName();

        $message = new $class();

        if ($input !== null) {
            // Merge input data into the message object.
            if ($context->getValue('content-type') !== 'application/grpc+json') {
                $message->mergeFromString($input);
            } else {
                $message->mergeFromJsonString($input);
            }
        }

        try {
            // Execute the method and get the result.
            $result = $callable($context, $message);
        } catch (TypeError $e) {
            throw InvokeException::create($e->getMessage(), Status::INTERNAL, $e);
        }

        try {
            // Serialize the result to a string.
            if ($context->getValue('content-type') !== 'application/grpc+json') {
                $output = $result->serializeToString();
            } else {
                $output = $result->serializeToJsonString();
            }
        } catch (Throwable $e) {
            throw InvokeException::create($e->getMessage(), Status::INTERNAL, $e);
        }

        return $output;
    }

    /**
     * Discover methods for the provided service interface.
     *
     * @param ServiceInterface $service The service instance.
     *
     * @return array
     */
    private function discoverMethods(ServiceInterface $service): array
    {
        $reflection = new ReflectionObject($service);

        $methods = [];
        foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if ($method->getNumberOfParameters() !== 2) {
                // Exclude methods that don't have exactly two parameters.
                continue;
            }

            [, $input] = $method->getParameters();

            $methods[$method->getName()] = [
                'name'         => $method->getName(),
                'inputClass'   => $input->getType(),
                'returnClass'  => $method->getReturnType(),
            ];
        }

        // Include the constructor explicitly.
        $constructor = $reflection->getConstructor();
        if ($constructor) {
            $methods['__construct'] = [
                'name'         => '__construct',
                'inputClass'   => null,  // No input for the constructor.
                'returnClass'  => null,  // No return type for the constructor.
            ];
        }

        return $methods;
    }
}