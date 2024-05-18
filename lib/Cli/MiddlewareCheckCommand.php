<?php

declare(strict_types=1);
namespace Minichan\Cli;

class MiddlewareCheckCommand extends Command
{
    public function __construct()
    {
        parent::__construct('check:middlewares', 'check all middleware files in the Middleware folder');
    }

    public function handle(array $args)
    {
        $directory = 'lib/Middleware';
        if (!is_dir($directory)) {
            echo "Error: The directory $directory does not exist.\n";
            exit(1);
        }

        $middlewareFiles = glob($directory . '/*.php');
        if (empty($middlewareFiles)) {
            echo "No middleware files found in $directory.\n";
            return;
        }

        $this->printBorder();
        printf("| %-30s | %-15s |\n", "Middleware Name", "Status");
        $this->printBorder();

        foreach ($middlewareFiles as $file) {
            $this->checkMiddlewareFiles($file);
        }

        $this->printBorder();
    }

    protected function checkMiddlewareFiles(string $file)
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
