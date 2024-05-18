<?php

declare(strict_types=1);

namespace Services\Config;

use Services\SampleService;

/**
 * Configuration class for defining gRPC services.
 */
class Config
{
    /**
     * Register gRPC services.
     *
     * @return array
     */
    public static function registerServices(): array
    {
        // Add your services here
        return [
            SampleService::class,
        ];
    }
    public static function registerMiddlewares(): array 
    {
        // Add your middlewares here
        return [

        ];
    }
}
