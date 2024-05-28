<?php

declare(strict_types=1);
namespace Minichan\Cli;

class ProtoCommand extends Command
{
    public function __construct()
    {
        parent::__construct('proto', 'Generate PHP and gRPC code from proto files');
    }

    public function handle(array $args)
    {
        $protoDir = 'proto';

        if (!is_dir($protoDir)) {
            echo "Error: The directory $protoDir does not exist.\n";
            exit(1);
        }

        $protoFiles = glob($protoDir . '/*.proto');

        if (empty($protoFiles)) {
            echo "No proto files found in $protoDir.\n";
            return;
        }

        $outputDir = '.';
        $grpcPlugin = 'lib/Bin/minichan-grpc';

        $generatedFiles = [];

        foreach ($protoFiles as $protoFile) {
            $command = "protoc --php_out=$outputDir --grpc_out=$outputDir --plugin=protoc-gen-grpc=$grpcPlugin $protoFile";

            $output = shell_exec($command);

            // Store the generated file in the array
            $generatedFiles[] = $protoFile;
        }

        $this->printGeneratedFilesTable($generatedFiles);
    }

    protected function printGeneratedFilesTable(array $files)
    {
        $this->printBorder();

        printf("| %-30s |\n", "Proto Files Executed");
        $this->printBorder();

        foreach ($files as $file) {
            printf("| %-30s |\n", $file);
        }

        $this->printBorder();
    }
    protected function printBorder() {
        printf("+-%-30s-+\n", str_repeat('-', 30), str_repeat('-', 15));
    }
}
