#!/usr/bin/php
<?php
declare(strict_types=1);

namespace Minichan\Cli;

class MiddlewareCommand extends Command
{
    public function __construct()
    {
        parent::__construct('generate:middleware', 'Generate a new middleware class or interface');
    }

    public function handle($args)
    {
        // Validate the number of arguments
        if (count($args) !== 2) {
            $this->showUsage();
            exit(1);
        }

        // Extract arguments
        [$middlewareName, $type] = $args;

        // Set up file and directory paths
        $directory = 'lib/Middleware';
        $filename = $directory . '/' . $middlewareName . '.php';

        // Create the directory if it doesn't exist
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        // Check if the file already exists
        if (file_exists($filename)) {
            echo "Error: The file $filename already exists.\n";
            exit(1);
        }

        // Generate the middleware class content
        $classContent = $this->generateClassContent($middlewareName, $type);

        // Save the file
        file_put_contents($filename, $classContent);

        echo ucfirst($type) . " '$middlewareName' generated successfully in '$filename'.\n";
    }

    /**
     * Display the usage information.
     */
    protected function showUsage()
    {
        echo "Usage: generate:middleware <MiddlewareName> <Interface|Class>\n";
    }

    /**
     * Generate the content for the middleware class.
     *
     * @param string $middlewareName
     * @param string $type
     * @return string
     */
    protected function generateClassContent(string $middlewareName, string $type): string
    {
        $classContent = "<?php\n\n";
        $classContent .= "declare(strict_types=1);\n";
        $classContent .= "namespace Minichan\\Middleware;\n\n";

        if (strtolower($type) === 'interface') {
            $classContent .= "interface $middlewareName\n";
        } elseif (strtolower($type) === 'class') {
            $classContent .= "class $middlewareName implements MiddlewareInterface\n";
        } else {
            echo "Error: Invalid class type specified. Use 'Interface' or 'Class'.\n";
            exit(1);
        }

        $classContent .= "{\n";
        $classContent .= "    // Your middleware class code here\n";
        $classContent .= "}\n";

        return $classContent;
    }
}
