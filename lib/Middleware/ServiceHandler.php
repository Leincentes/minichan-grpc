<?php

declare(strict_types=1);

namespace Minichan\Middleware;

use Minichan\Config\Constant;
use Minichan\Config\Status;
use Minichan\Exception\GRPCException;
use Minichan\Exception\InvokeException;
use Minichan\Exception\NotFoundException;
use Minichan\Grpc\Response;
use Minichan\Grpc\Util;
use Throwable;

/**
 * Middleware for handling GRPC service requests.
 */
class ServiceHandler implements MiddlewareInterface
{
    /**
     * Process the GRPC service request.
     *
     * @param \Minichan\Grpc\Request $request
     * @param \Minichan\Grpc\RequestHandlerInterface $handler
     *
     * @return \Minichan\Grpc\MessageInterface
     */
    public function process(\Minichan\Grpc\Request | \Minichan\Grpc\MessageInterface $request, \Minichan\Grpc\RequestHandlerInterface $handler): \Minichan\Grpc\MessageInterface
    {
        try {
            // Extracting information from the request
            $service = $request->getService();
            $method = $request->getMethod();
            $context = $request->getContext();

            // Check if the requested service is available
            $serviceContainer = $context->getValue('SERVICES');
            if (!isset($serviceContainer[$service])) {
                throw NotFoundException::create("{$service}::{$method} not found");
            }

            // Call the service to handle the request and get the output
            $output = $serviceContainer[$service]->handle($request);

            // Update the context with a successful status
            $context = $context->withValue(Constant::GRPC_STATUS, Status::OK);
        } catch (GRPCException $e) {
            $this->handleException($e, $context);
            $output = '';
        } catch (\Swoole\Exception $e) {
            $this->handleException($e, $context);
            $output = '';
        } catch (Throwable $e) {
            throw InvokeException::create($e->getMessage(), Status::INTERNAL, $e);
        }

        return new Response($context, $output);
    }

    /**
     * Handle exceptions by logging relevant information.
     *
     * @param \Throwable $exception
     * @param \Minichan\Grpc\Context $context
     *
     * @return void
     */
    private function handleException(Throwable $exception, \Minichan\Grpc\Context $context): void
    {
        Util::log(
            SWOOLE_LOG_ERROR,
            $exception->getMessage() . ', error code: ' . $exception->getCode() . "\n" . $exception->getTraceAsString()
        );

        $context = $context->withValue(Constant::GRPC_STATUS, $exception->getCode());
        $context = $context->withValue(Constant::GRPC_MESSAGE, $exception->getMessage());
    }
}
