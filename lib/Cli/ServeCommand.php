<?php

declare(strict_types=1);
namespace Minichan\Cli;

class ServeCommand extends Command
{
    public function __construct()
    {
        parent::__construct('serve', ' ---------------- run the server');
    }
    public function handle($args)
    {
        $scriptPath = 'Services/Bootstrap/serve.php';

        if (!file_exists($scriptPath)) {
            echo "Error: The script $scriptPath does not exist.\n";
            exit(1);
        }

        system("php $scriptPath");
    }
}
