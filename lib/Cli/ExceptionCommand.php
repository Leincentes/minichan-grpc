#!/usr/bin/php
<?php
declare(strict_types=1);

namespace Minichan\Cli;

class ExceptionCommand extends Command
{
    public function __construct()
    {
        parent::__construct('generate:exception', 'Generate a new exception class or interface');
    }

    public function handle($args)
    {
        // Validate the number of arguments
        if (count($args) !== 2) {
            $this->showUsage();
            exit(1);
        }

        // Extract arguments
        [$exceptionName, $type] = $args;

        // Set up file and directory paths
        $directory = 'lib/Exception';
        $filename = $directory . '/' . $exceptionName . '.php';

        // Create the directory if it doesn't exist
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        // Check if the file already exists
        if (file_exists($filename)) {
            echo "Error: The file $filename already exists.\n";
            exit(1);
        }

        // Generate the exception class content
        $classContent = $this->generateClassContent($exceptionName, $type);

        // Save the file
        file_put_contents($filename, $classContent);

        echo "Exception class '{$this->getSignature()}' generated successfully in '$filename'.\n";
    }

    /**
     * Display the usage information.
     */
    protected function showUsage()
    {
        echo "Usage: generate:exception <ExceptionName> <Interface|Class>\n";
    }

    /**
     * Generate the content for the exception class.
     *
     * @param string $exceptionName
     * @param string $type
     * @return string
     */
    protected function generateClassContent(string $exceptionName, string $type): string
    {
        $classContent = "<?php\n\n";
        $classContent .= "declare(strict_types=1);\n";
        $classContent .= "namespace Minichan\\Exception;\n\n";

        if (strtolower($type) === 'interface') {
            $classContent .= "interface $exceptionName\n";
        } elseif (strtolower($type) === 'class') {
            $classContent .= "class $exceptionName extends GRPCException\n";
        } else {
            echo "Error: Invalid class type specified. Use 'Interface' or 'Class'.\n";
            exit(1);
        }

        $classContent .= "{\n";
        $classContent .= "    // Your exception class code here\n";
        $classContent .= "}\n";

        return $classContent;
    }
}
