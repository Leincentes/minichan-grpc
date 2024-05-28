<?php

declare(strict_types=1);

namespace Minichan\Cli;

use Services\Config\Config;

class ServicesRegisterCommand extends Command
{
    public function __construct()
    {
        parent::__construct('register:services', 'Register gRPC services.');
    }

    public function handle(array $args)
    {
        // Check if the number of arguments is correct
        if (count($args) !== 1) {
            echo "Error: Missing or invalid argument. Usage: php minichan register:services ServiceName\n";
            exit(1);
        }

        // Extract the service name from the arguments
        $serviceName = $args[0];

        // Check if the service class exists
        if (!class_exists($serviceName)) {
            // Prepend the namespace if it's not included
            $fullServiceName = 'Services\\' . $serviceName;
            if (!class_exists($fullServiceName)) {
                echo "Error: Service class '$serviceName' not found.\n";
                exit(1);
            }
            $serviceName = $fullServiceName;
        }

        // Register and update services using Config class
        $services = Config::registerServices();

        // Check if the service is already registered
        if (in_array($serviceName, $services)) {
            echo "Service '$serviceName' is already registered.\n";
            return;
        }

        // Add the service to the array of registered services
        $services[] = $serviceName;

        // Update the Config class with the new list of registered services
        Config::updateRegisteredServices($services);

        echo "Service '$serviceName' has been successfully registered.\n";
    }
}
