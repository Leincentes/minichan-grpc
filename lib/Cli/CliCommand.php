#!/usr/bin/php
<?php
declare(strict_types=1);

namespace Minichan\Cli;

class CliCommand extends Command
{
    public function __construct()
    {
        parent::__construct('generate:cli  ', 'Generate a new CLI class or interface');
    }

    public function handle($args)
    {
        // Validate the number of arguments
        if (count($args) !== 2) {
            $this->showUsage();
            exit(1);
        }

        // Extract arguments
        [$cliName, $type] = $args;

        // Set up file and directory paths
        $directory = 'lib/Cli';
        $filename = $directory . '/' . $cliName . '.php';

        // Sanitize CLI name
        $cliClean = str_replace("command", "", strtolower($cliName));

        // Create the directory if it doesn't exist
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        // Check if the file already exists
        if (file_exists($filename)) {
            echo "Error: The file $filename already exists.\n";
            exit(1);
        }

        // Generate the CLI class content
        $classContent = $this->generateClassContent($cliName, $type, $cliClean);

        // Save the file
        file_put_contents($filename, $classContent);

        echo "CLI $type '$cliName' generated successfully in '$filename'.\n";
    }

    /**
     * Display the usage information.
     */
    protected function showUsage()
    {
        echo "Usage: generate:cli <CliName> <Interface|Class>\n";
    }

    /**
     * Generate the content for the CLI class.
     *
     * @param string $cliName
     * @param string $type
     * @param string $cliClean
     * @return string
     */
    protected function generateClassContent(string $cliName, string $type, string $cliClean): string
    {
        $classContent = "<?php\n\n";
        $classContent .= "declare(strict_types=1);\n";
        $classContent .= "namespace Minichan\\Cli;\n\n";

        if (strtolower($type) === 'interface') {
            $classContent .= "interface $cliName\n";
        } elseif (strtolower($type) === 'class') {
            $classContent .= "class $cliName extends Command\n";
        } else {
            echo "Error: Invalid class type specified. Use 'Interface' or 'Class'.\n";
            exit(1);
        }

        $classContent .= "{\n";
        $classContent .= "    public function __construct()\n";
        $classContent .= "    {\n";
        $classContent .= "        parent::__construct('generate:$cliClean', 'Generate a new $cliClean-related class or interface');\n";
        $classContent .= "    }\n";
        $classContent .= "    public function handle(\$args)\n";
        $classContent .= "    {\n";
        $classContent .= "        // Your configuration class code here\n";
        $classContent .= "    }\n";
        $classContent .= "}\n";

        return $classContent;
    }
}
