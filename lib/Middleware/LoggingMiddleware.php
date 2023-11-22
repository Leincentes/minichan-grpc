<?php

declare(strict_types=1);
namespace Minichan\Middleware;

use Minichan\Grpc\Util;

/**
 * 
 *
 * Middleware for logging GRPC requests.
 */
class LoggingMiddleware implements \Minichan\Middleware\MiddlewareInterface
{   
    /**
     * Process the GRPC request and log relevant information.
     *
     * @param \Minichan\Grpc\Request $request
     * @param \Minichan\Middleware\StackHandler $handler
     *
     * @return \Minichan\Grpc\MessageInterface
     */
    public function process(\Minichan\Grpc\Request $request, \Minichan\Middleware\StackHandler $handler): \Minichan\Grpc\MessageInterface
    {
        // Extracting information from the request
        $service = $request->getService();
        $method = $request->getMethod();
        $context = $request->getContext();
        $rawRequest = $context->getValue(\Swoole\Http\Request::class);
        $client = $rawRequest->server['remote_addr'] . ':' . $rawRequest->server['remote_port'];
        $server = $rawRequest->header['host'];
        $streamId = $rawRequest->streamId;
        $ua = $rawRequest->header['user-agent'];

        // Logging the GRPC request details
        Util::log(
            SWOOLE_LOG_INFO,
            ("GRPC request: {$client}->{$server}, stream({$streamId}), " . $service . '/' . $method . ', ' . $ua)
        );

        // Continue handling the request in the middleware stack
        return $handler->handle($request);
    }
}
