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
        ];
    }
}
