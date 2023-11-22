#!/usr/bin/php
<?php
declare(strict_types=1);

namespace Minichan\Cli;

class GrpcCommand extends Command
{
    public function __construct()
    {
        parent::__construct('generate:grpc  ', 'Generate a new grpc-related class or interface');
    }

    public function handle($args)
    {
        // Validate the number of arguments
        if (count($args) !== 2) {
            $this->showUsage();
            exit(1);
        }

        // Extract arguments
        [$grpcName, $type] = $args;

        // Set up file and directory paths
        $directory = 'lib/Grpc';
        $filename = $directory . '/' . $grpcName . '.php';

        // Create the directory if it doesn't exist
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        // Check if the file already exists
        if (file_exists($filename)) {
            echo "Error: The file $filename already exists.\n";
            exit(1);
        }

        // Generate the grpc class content
        $classContent = $this->generateClassContent($grpcName, $type);

        // Save the file
        file_put_contents($filename, $classContent);

        echo ucfirst($type) . " '$grpcName' generated successfully in '$filename'.\n";
    }

    /**
     * Display the usage information.
     */
    protected function showUsage()
    {
        echo "Usage: generate:grpc <GrpcName> <Interface|Class>\n";
    }

    /**
     * Generate the content for the grpc class.
     *
     * @param string $grpcName
     * @param string $type
     * @return string
     */
    protected function generateClassContent(string $grpcName, string $type): string
    {
        $classContent = "<?php\n\n";
        $classContent .= "declare(strict_types=1);\n";
        $classContent .= "namespace Minichan\\Grpc;\n\n";

        if (strtolower($type) === 'interface') {
            $classContent .= "interface $grpcName\n";
        } elseif (strtolower($type) === 'class') {
            $classContent .= "class $grpcName\n";
        } else {
            echo "Error: Invalid class type specified. Use 'Interface' or 'Class'.\n";
            exit(1);
        }

        $classContent .= "{\n";
        $classContent .= "    // Your grpc-related class code here\n";
        $classContent .= "}\n";

        return $classContent;
    }
}
