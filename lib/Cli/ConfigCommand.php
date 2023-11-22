#!/usr/bin/php
<?php
declare(strict_types=1);

namespace Minichan\Cli;

class ConfigCommand extends Command
{
    public function __construct()
    {
        parent::__construct('generate:config', 'Generate a new configuration class or interface');
    }

    public function handle($args)
    {
        // Validate the number of arguments
        if (count($args) !== 2) {
            $this->showUsage();
            exit(1);
        }

        // Extract arguments
        [$configName, $type] = $args;

        // Set up file and directory paths
        $directory = 'lib/Config';
        $filename = $directory . '/' . $configName . '.php';

        // Create the directory if it doesn't exist
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        // Check if the file already exists
        if (file_exists($filename)) {
            echo "Error: The file $filename already exists.\n";
            exit(1);
        }

        // Generate the configuration class content
        $classContent = $this->generateClassContent($configName, $type);

        // Save the file
        file_put_contents($filename, $classContent);

        echo "Configuration class '{$this->getSignature()}' generated successfully in '$filename'.\n";
    }

    /**
     * Display the usage information.
     */
    protected function showUsage()
    {
        echo "Usage: generate:config <ConfigName> <Interface|Class>\n";
    }

    /**
     * Generate the content for the configuration class.
     *
     * @param string $configName
     * @param string $type
     * @return string
     */
    protected function generateClassContent(string $configName, string $type): string
    {
        $classContent = "<?php\n\n";
        $classContent .= "declare(strict_types=1);\n";
        $classContent .= "namespace Minichan\\Config;\n\n";

        if (strtolower($type) === 'interface') {
            $classContent .= "interface $configName\n";
        } elseif (strtolower($type) === 'class') {
            $classContent .= "class $configName\n";
        } else {
            echo "Error: Invalid class type specified. Use 'Interface' or 'Class'.\n";
            exit(1);
        }

        $classContent .= "{\n";
        $classContent .= "    // Your configuration class code here\n";
        $classContent .= "}\n";

        return $classContent;
    }
}
