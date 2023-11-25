<?php

declare(strict_types=1);
namespace Minichan\Cli;

class ServeCommand extends Command
{
    public function __construct()
    {
        parent::__construct('serve', '                run the server');
    }
    public function handle($args)
    {
                // Assuming the script is located in the 'Service' folder
                $scriptPath = 'Services/Bootstrap/serve.php';

                // Check if the script exists
                if (!file_exists($scriptPath)) {
                    echo "Error: The script $scriptPath does not exist.\n";
                    exit(1);
                }
        
                // Run the script using the PHP CLI
                system("php $scriptPath");
    }
}
