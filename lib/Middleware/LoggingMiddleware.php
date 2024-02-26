<?php

declare(strict_types=1);

namespace Minichan\Middleware;

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
        $startTime = microtime(true);
        $response = $handler->handle($request);
        $executionTime = microtime(true) - $startTime;
        
        // Log GRPC request details with additional information
        Util::log(
            Status::LOG,
            "{$timestamp} - GRPC request: {$client}->{$server}, stream({$streamId}), {$httpMethod} {$requestUri}, User-Agent: {$ua}, Execution Time: " . number_format($executionTime, 4) . " seconds");

        // Clear terminal info after 1000 lines
        static $lineCount = 0;
        $lineCount++;
        $clearTerminalAfterLines = 1000;
        if ($lineCount >= $clearTerminalAfterLines) {
            echo "\033[2J\033[H";
            $lineCount = 0; 
        }
    
        return $response;
    }
}
