<?php

declare(strict_types=1);

namespace Minichan\Cli;

class Cli
{
    const VERSION = '2.0';

    /** @var Command[] */
    protected $commands = [];

    /**
     * Add a command to the CLI application.
     *
     * @param Command $command
     */
    public function addCommand(Command $command)
    {
        $this->commands[] = $command;
    }

    /**
     * Run the CLI application.
     *
     * @param array $argv
     */
    public function run(array $argv)
    {
        if (count($argv) < 2 || $argv[1] === 'help') {
            $this->displayHelp();
            exit(0);
        }
    
        $commandName = $argv[1];
        $arguments = array_slice($argv, 2);
    
        $command = $this->findCommand($commandName);
    
        if ($command) {
            $command->handle($arguments);
        } else {
            echo "Command not found. Use 'help' for a list of commands.\n";
        }
    }

    /**
     * Display a list of available commands.
     */
    protected function displayHelp()
    {
        echo "Minichan CLI\n";
        echo "============\n";
        echo "Version: " . self::VERSION . "\n\n";

        // Commands organized by category
        $categories = [
            'Serve' => [],
            'Check' => [],
            'Generate' => [],
            'Proto' => [],
            'Register' => [],
        ];

        // Organize commands into categories
        foreach ($this->commands as $command) {
            $category = $this->categorizeCommand($command->signature);
            $categories[$category][] = $command;
        }

        // Display commands grouped by category
        foreach ($categories as $category => $commands) {
            if (!empty($commands)) {
                echo "\n$category:\n";
                echo "-------------------\n";
                foreach ($commands as $command) {
                    printf("  %-20s %s\n", $command->signature, $command->description);
                }
            }
        }

        echo "\nUsage Examples:\n";
        echo "---------------\n";
        echo "  php minichan serve\n";
        echo "  php minichan check:services\n";
        echo "\nUse 'php minichan help' to display this help message.\n";
    }

    /**
     * Categorize a command based on its signature.
     *
     * @param string $signature
     * @return string
     */
    protected function categorizeCommand(string $signature): string
    {
        if (strpos($signature, 'check') !== false) {
            return 'Check';
        } elseif (strpos($signature, 'generate') !== false) {
            return 'Generate';
        } elseif (strpos($signature, 'serve') !== false) {
            return 'Serve';
        } elseif (strpos($signature, 'proto') !== false) {
            return 'Proto';
        } elseif (strpos($signature, 'register') !== false) {
            return 'Register';
        } else {
            return 'Other';
        }
    }

    /**
     * Find a command by name.
     *
     * @param string $name
     * @return Command|null
     */
    protected function findCommand(string $name)
    {
        foreach ($this->commands as $command) {
            if ($command->signature === $name) {
                return $command;
            }
        }
        return null;
    }
}
