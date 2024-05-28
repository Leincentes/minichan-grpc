<?php

declare(strict_types=1);

namespace Minichan\Cli;

use Services\Config\Config;

class ServicesUnregisterCommand extends Command
{
    public function __construct()
    {
        parent::__construct('unregister:services', 'Unregister gRPC services.');
    }

    public function handle(array $args)
    {
        // Check if the number of arguments is correct
        if (count($args) !== 1) {
            echo "Error: Missing or invalid argument. Usage: php minichan unregister:services ServiceName\n";
            exit(1);
        }

        // Extract the service name from the arguments
        $serviceName = $args[0];

        // Prepend the namespace if it's not included
        if (strpos($serviceName, 'Services\\') !== 0) {
            $serviceName = 'Services\\' . $serviceName;
        }

        // Register and update services using Config class
        $services = Config::registerServices();

        // Check if the service is registered
        if (!in_array($serviceName, $services)) {
            echo "Error: Service '$serviceName' is not registered.\n";
            return;
        }

        // Remove the service from the array of registered services
        $services = array_filter($services, function ($service) use ($serviceName) {
            return $service !== $serviceName;
        });

        // Update the Config class with the new list of registered services
        Config::updateRegisteredServices($services);

        echo "Service '$serviceName' has been successfully unregistered.\n";
    }
}
