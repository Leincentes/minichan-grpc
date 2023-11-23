#!/usr/bin/php
<?php
declare(strict_types=1);

namespace Minichan\Cli;

class TestsCommand extends Command
{
    public function __construct()
    {
        parent::__construct('generate:tests', 'Generate a new test-related class or interface');
    }

    public function handle($args)
    {
        if (count($args) !== 3) {
            echo "Usage: generate:tests <TestName> <Interface|Class> <Folder>\n";
            exit(1);
        }

        $testName = $args[0];
        $type = $args[1];
        $folder = $args[2];
        $directory = "tests/$folder";
        $filename = $directory . '/' . $testName . '.php';

        // Check if the directory exists
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        // Check if the file already exists
        if (file_exists($filename)) {
            echo "Error: The file $filename already exists.\n";
            exit(1);
        }

        // Generate the test class content
        $classContent = "<?php\n\n";
        $classContent .= "declare(strict_types=1);\n";
        $classContent .= "namespace Tests\\$folder;\n\n";

        if (strtolower($type) === 'interface') {
            $classContent .= "interface $testName\n";
        } elseif (strtolower($type) === 'class') {
            $classContent .= "class $testName extends \PHPUnit\Framework\TestCase\n";
        } else {
            echo "Error: Invalid class type specified. Use 'Interface' or 'Class'.\n";
            exit(1);
        }

        $classContent .= "{\n";
        $classContent .= "    // Your test class code here\n";
        $classContent .= "}\n";

        // Save the file
        file_put_contents($filename, $classContent);

        echo ucfirst($type) . " '$testName' generated successfully in '$filename'.\n";
    }
}
