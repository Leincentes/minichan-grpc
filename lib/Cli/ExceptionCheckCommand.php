<?php
declare(strict_types=1);
namespace Minichan\Cli;

class ExceptionCheckCommand extends Command
{
    public function __construct()
    {
        parent::__construct('check:exceptions', 'check all exception files in the Exception folder');
    }

    public function handle(array $args)
    {
        $directory = 'lib/Exception';
        if (!is_dir($directory)) {
            echo "Error: The directory $directory does not exist.\n";
            exit(1);
        }

        $exceptionFiles = glob($directory . '/*.php');
        if (empty($exceptionFiles)) {
            echo "No exception files found in $directory.\n";
            return;
        }

        $this->printBorder();
        printf("| %-30s | %-15s |\n", "Exception Name", "Status");
        $this->printBorder();

        foreach ($exceptionFiles as $file) {
            $this->checkExceptionFiles($file);
        }

        $this->printBorder();
    }

    protected function checkExceptionFiles(string $file)
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


