<?php

declare(strict_types=1);

namespace Minichan\Middleware;

use Minichan\Config\Constant;
use Minichan\Config\Status;
use Minichan\Grpc\Util;

/**
 * Middleware for logging GRPC requests.
 */
class LoggingMiddleware implements \Minichan\Middleware\MiddlewareInterface
{
    /**
     * Process the GRPC request and log relevant information.
     *
     * @param \Minichan\Grpc\Request | \Minichan\Grpc\MessageInterface $request
     * @param \Minichan\Middleware\StackHandler $handler
     *
     * @return \Minichan\Grpc\MessageInterface
     */
    public function process($request, \Minichan\Middleware\StackHandler $handler): \Minichan\Grpc\MessageInterface
    {
        // Extracting information from the request
        $context = $request->getContext();
        $rawRequest = $context->getValue(\Swoole\Http\Request::class);
        $client = $rawRequest->server['remote_addr'] . ':' . $rawRequest->server['remote_port'];
        $server = $rawRequest->header['host'];
        $streamId = $rawRequest->streamId;
        $ua = $rawRequest->header['user-agent'];
        $httpMethod = $rawRequest->server['request_method'];
        $requestUri = $rawRequest->server['request_uri'];

        $timestamp = date('Y-m-d H:i:s');
        // Log GRPC request details with additional information
        Util::log(
            Status::LOG,
            "{$timestamp} - GRPC request: {$client}->{$server}, stream({$streamId}), {$httpMethod} {$requestUri}, User-Agent: {$ua}"
        );

        $startTime = microtime(true);
        $response = $handler->handle($request);
        $executionTime = microtime(true) - $startTime;
        
        Util::log(Status::LOG, "Execution Time: " . number_format($executionTime, 4) . " seconds");

        return $response;
    }
}
