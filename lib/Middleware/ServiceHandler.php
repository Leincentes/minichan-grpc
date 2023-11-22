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
 * 
 *
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
    public function process(\Minichan\Grpc\Request $request, \Minichan\Grpc\RequestHandlerInterface $handler): \Minichan\Grpc\MessageInterface
    {
        $result = null;
        try {
            // Extracting information from the request
            $service = $request->getService();
            $method = $request->getMethod();
            $context = $request->getContext();

            // Check if the requested service is available
            if (!isset($context->getValue('SERVICES')[$service])) {
                throw NotFoundException::create("{$service}::{$method} not found");
            }

            // Call the service to handle the request and get the output
            $output = $context->getValue('SERVICES')[$service]->handle($request);

            // Update the context with a successful status
            $context = $context->withValue(Constant::GRPC_STATUS, Status::OK);
        } catch (GRPCException $e) {
            // Log and handle GRPC-specific exceptions
            Util::log(SWOOLE_LOG_ERROR, $e->getMessage() . ', error code: ' . $e->getCode() . "\n" . $e->getTraceAsString());
            $output = '';
            $context = $context->withValue(Constant::GRPC_STATUS, $e->getCode());
            $context = $context->withValue(Constant::GRPC_MESSAGE, $e->getMessage());
        } catch (\Swoole\Exception $e) {
            // Log and handle Swoole-specific exceptions
            Util::log(SWOOLE_LOG_WARNING, $e->getMessage() . ', error code: ' . $e->getCode() . "\n" . $e->getTraceAsString());
            $output = '';
            $context = $context->withValue(Constant::GRPC_STATUS, $e->getCode());
            $context = $context->withValue(Constant::GRPC_MESSAGE, $e->getMessage());
        } catch (Throwable $e) {
            // Log and handle other exceptions
            throw InvokeException::create($e->getMessage(), Status::INTERNAL, $e);
        }

        // Return the response with the updated context and output
        return new Response($context, $output);
    }
}