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
        $requestHeaders = $rawRequest->header;
        $requestPayload = $rawRequest->getContent();

        // Log timestamp
        $timestamp = date('Y-m-d H:i:s');

        // Continue handling the request in the middleware stack
        $startTime = microtime(true);
        $response = $handler->handle($request);
        $executionTime = microtime(true) - $startTime;
        
        // Log GRPC request details with additional information
        Util::log(
            Status::LOG,
            "{$timestamp} - GRPC request: {$client}->{$server}, stream({$streamId}), {$httpMethod} {$requestUri}, User-Agent: {$ua}, Execution Time: " . number_format($executionTime, 4) . " seconds");
        
        // print("=======================================================\n");
        // // Log request headers without backslashes
        // Util::log(Status::LOG, "Request Headers: " . stripslashes(json_encode($requestHeaders, JSON_PRETTY_PRINT)));
        // print("=======================================================\n");

        // // Log encrypted request payload if present
        // if (!empty($requestPayload)) {
        //     $encryptedPayload = $this->encryptPayload($requestPayload);
        //     Util::log(Status::LOG, "Encrypted Request Payload: " . base64_encode($encryptedPayload));
        // }
        // print("=======================================================\n");



        // // Log response status
        // $responseStatus = $context->getValue(Constant::GRPC_STATUS);
        // Util::log(Status::LOG, "Response Status: {$responseStatus}");
        // print("=======================================================\n");

        // // Log execution time
        // Util::log(Status::LOG, "Execution Time: " . number_format($executionTime, 4) . " seconds");
        // print("=======================================================\n");

        return $response;
    }

    /**
     * Encrypt the payload.
     *
     * @param string $payload
     *
     * @return string
     */
    private function encryptPayload(string $payload): string
    {
        // use a secure encryption algorithm and key management in a real-world scenario
        $encryptionKey = 'test-encrypt';
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));

        $encryptedPayload = openssl_encrypt($payload, 'aes-256-cbc', $encryptionKey, 0, $iv);

        return $iv . $encryptedPayload;
    }
}
