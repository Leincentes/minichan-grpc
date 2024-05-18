<?php

declare(strict_types=1);

namespace Minichan\Config;

use Minichan\Middleware\LoggingMiddleware;
use Minichan\Middleware\TraceMiddleware;

/**
 * 
 * Configuration class for defining gRPC services and middlewares.
 */
class Config
{
    /**
     * Get an array of gRPC services to be registered.
     *
     * @return array
     */
    public static function getServices(): array
    {
        return [
            ...\Services\Config\Config::registerServices(),
        ];
    }

    /**
     * Get an array of middlewares to be added.
     *
     * @return array
     */
    public static function getMiddlewares(): array
    {
        return [
            new LoggingMiddleware(),
            new TraceMiddleware(),
            ...\Services\Config\Config::registerMiddlewares(),
        ];
    }

    /**
     * Set additional server options.
     *
     * @param \Minichan\Grpc\Server $server
     */
    public static function setOptions(\Minichan\Grpc\Server $server): void
    {
        $server->set([
            'worker_num' => 4,                  // Number of worker processes
            'open_http2_protocol' => 1,         // Enable HTTP2 protocol
            'enable_coroutine' => true,         // Enable coroutine support
            'task_ipc_mode' => 1,               // Use message queue for task worker communication
            'max_request' => 10000,             // Maximum number of requests handled by each worker process
            'max_conn' => 1000,                 // Maximum number of simultaneous connections
            'dispatch_mode' => 2,               // Use IP dispatch mode for better performance
            'open_tcp_nodelay' => true,         // Enable TCP_NODELAY
            // 'log_file' => BASE_PATH . '/output/log/swoole.log',// Specify the log file
        ]);
    }
}
