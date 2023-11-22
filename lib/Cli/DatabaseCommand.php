#!/usr/bin/php
<?php
declare(strict_types=1);

namespace Minichan\Cli;

class DatabaseCommand extends Command
{
    public function __construct()
    {
        parent::__construct('generate:database', 'Generate a new database-related class or interface');
    }

    public function handle($args)
    {
        // Validate the number of arguments
        if (count($args) !== 2) {
            $this->showUsage();
            exit(1);
        }

        // Extract arguments
        [$databaseName, $type] = $args;

        // Set up file and directory paths
        $directory = 'Database';
        $filename = $directory . '/' . $databaseName . '.php';

        // Create the directory if it doesn't exist
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        // Check if the file already exists
        if (file_exists($filename)) {
            echo "Error: The file $filename already exists.\n";
            exit(1);
        }

        // Generate the database class content
        $classContent = $this->generateClassContent($databaseName, $type);

        // Save the file
        file_put_contents($filename, $classContent);

        echo "Database class '{$this->getSignature()}' generated successfully in '$filename'.\n";
    }

    /**
     * Display the usage information.
     */
    protected function showUsage()
    {
        echo "Usage: generate:database <DatabaseName> <Interface|Class>\n";
    }

    /**
     * Generate the content for the database class.
     *
     * @param string $databaseName
     * @param string $type
     * @return string
     */
    protected function generateClassContent(string $databaseName, string $type): string
    {
        $classContent = "<?php\n\n";
        $classContent .= "declare(strict_types=1);\n";
        $classContent .= "namespace Minichan\\Database;\n\n";

        if (strtolower($type) === 'interface') {
            $classContent .= "interface $databaseName\n";
        } elseif (strtolower($type) === 'class') {
            $classContent .= "class $databaseName\n";
        } else {
            echo "Error: Invalid class type specified. Use 'Interface' or 'Class'.\n";
            exit(1);
        }

        $classContent .= "{\n";
        $classContent .= "    // Your database-related class code here\n";
        $classContent .= "}\n";

        return $classContent;
    }
}
