<?php

declare(strict_types=1);

namespace Minichan\Cli;

class ServicesGenerateCommand extends Command
{
    public function __construct()
    {
        parent::__construct('generate:services', 'Generate a new services-related class or interface');
    }

    public function handle($args)
    {
        // Validate the number of arguments
        if (count($args) !== 2) {
            $this->showUsage();
            exit(1);
        }

        // Extract arguments
        [$serviceName, $type] = $args;

        // Set up file and directory paths
        $directory = 'Services';
        $filename = $directory . '/' . $serviceName . '.php';

        // Create the directory if it doesn't exist
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        // Check if the file already exists
        if (file_exists($filename)) {
            echo "Error: The file $filename already exists.\n";
            exit(1);
        }

        // Generate the services class content
        $classContent = $this->generateClassContent($serviceName, $type);

        // Save the file
        file_put_contents($filename, $classContent);

        echo ucfirst($type) . " '$serviceName' generated successfully in '$filename'.\n";
    }

    /**
     * Display the usage information.
     */
    protected function showUsage()
    {
        echo "Usage: generate:services <ServicesName> <Interface|Class>\n";
        echo "Sample:\n";
        echo "--------\n";
        echo "php minichan generate:services SampleService class\n";
    }

    /**
     * Generate the content for the services class.
     *
     * @param string $serviceName
     * @param string $type
     * @return string
     */
    protected function generateClassContent(string $serviceName, string $type): string
    {
        $classContent = "<?php\n\n";
        $classContent .= "declare(strict_types=1);\n";
        $classContent .= "namespace Services;\n\n";
    
        if (strtolower($type) === 'interface') {
            $classContent .= "interface $serviceName\n";
        } elseif (strtolower($type) === 'class') {
            $classContent .= "class $serviceName\n";
        } else {
            echo "Error: Invalid class type specified. Use 'Interface' or 'Class'.\n";
            exit(1);
        }
    
        $classContent .= "{\n";
        $classContent .= "    // Your services class code here\n";
        
        // Add a static method 'getName' to return the service name
        $classContent .= "\n";
        $classContent .= "    // This is needed on the the server side\n";
        $classContent .= "    // Don't modify\n";
        $classContent .= "    public static function getName(): string\n";
        $classContent .= "    {\n";
        $classContent .= "        return '$serviceName';\n";
        $classContent .= "    }\n";
    
        $classContent .= "}\n";
    
        return $classContent;
    }
    
}
