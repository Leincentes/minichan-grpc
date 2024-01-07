<?php

declare(strict_types=1);

namespace Services\Config;

use Services\AuthService;
use Services\TestAuthService;

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
            // AuthService::class,
            TestAuthService::class
        ];
    }
}
