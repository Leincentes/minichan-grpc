<?php

declare(strict_types=1);
namespace Minichan\Cli;

class ServicesCheckCommand extends Command
{
    public function __construct()
    {
        parent::__construct('check:services', 'check all service files in the Services folder');
    }

    public function handle(array $args)
    {
        $directory = 'Services';
        if (!is_dir($directory)) {
            echo "Error: The directory $directory does not exist.\n";
            exit(1);
        }

        $serviceFiles = glob($directory . '/*.php');
        if (empty($serviceFiles)) {
            echo "No service files found in $directory.\n";
            return;
        }

        $this->printBorder();
        printf("| %-30s | %-15s |\n", "Service Name", "Status");
        $this->printBorder();

        foreach ($serviceFiles as $file) {
            $this->checkServiceFile($file);
        }

        $this->printBorder();
    }

    protected function checkServiceFile(string $file)
    {
        $serviceName = basename($file, '.php');
        if (!is_readable($file) || filesize($file) === 0) {
            $status = "Not Available";
        } else {
            $status = "Available";
        }
        printf("| %-30s | %-15s |\n", $serviceName, $status);
    }

    protected function printBorder()
    {
        printf("+-%-30s-+-%-15s-+\n", str_repeat('-', 30), str_repeat('-', 15));
    }
}
