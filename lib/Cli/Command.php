#!/usr/bin/php
<?php
declare(strict_types=1);

namespace Minichan\Cli;

class Command
{
    /** @var string */
    public $signature;

    /** @var string */
    public $description;

    /**
     * Command constructor.
     *
     * @param string $signature
     * @param string $description
     */
    public function __construct(string $signature, string $description)
    {
        $this->signature = $signature;
        $this->description = $description;
    }

    /**
     * Get an array of available commands.
     *
     * @return array
     */
    public static function getCommands(): array
    {
        return [
            // Add your commands here as needed
        ];
    }

    /**
     * Get the command signature.
     *
     * @return string
     */
    public function getSignature(): string
    {
        return $this->signature;
    }

    /**
     * Get the command description.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }
}
