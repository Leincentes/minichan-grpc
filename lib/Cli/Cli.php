#!/usr/bin/php
<?php
declare(strict_types=1);

namespace Minichan\Cli;

class Cli
{
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
        echo "Available commands:\n";
        foreach ($this->commands as $command) {
            echo "  " . $command->signature . "\t" . $command->description . "\n";
        }
        echo "\nExample:\n \nphp minichan generate:config MyClass Class\nphp minichan generate:config MyInterface Interface\n";
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
