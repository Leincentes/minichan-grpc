<?php

declare(strict_types=1);

namespace Services\Config;

/**
 * Configuration class for defining gRPC services.
 */
class Config
{
    private static $configFilePath = __DIR__ . '/services.php';

    /**
     * Register gRPC services.
     *
     * @return array
     */
    public static function registerServices(): array
    {
        if (file_exists(self::$configFilePath)) {
            return include self::$configFilePath;
        }
        return [];
    }

    /**
     * Update the registered gRPC services.
     *
     * @param array $services
     * @return void
     */
    public static function updateRegisteredServices(array $services): void
    {
        $content = "<?php\n\nreturn [\n";
        foreach ($services as $service) {
            $content .= "    '$service',\n";
        }
        $content .= "];\n";
        file_put_contents(self::$configFilePath, $content);
    }

    /**
     * Register gRPC middlewares.
     * 
     * @return array
     */
    public static function registerMiddlewares(): array 
    {
        return [];
    }
}
