#!/usr/bin/php
<?php
declare(strict_types=1);

! defined('BASE_PATH') && define('BASE_PATH', dirname(__DIR__, 1));

require_once BASE_PATH . '/vendor/autoload.php';

use Minichan\Cli\Cli;
use Minichan\Cli\CliCommand;
use Minichan\Cli\ConfigCommand;
use Minichan\Cli\DatabaseCommand;
use Minichan\Cli\ExceptionCommand;
use Minichan\Cli\GrpcCommand;
use Minichan\Cli\MiddlewareCommand;
use Minichan\Cli\ServicesCommand;

$cli = new Cli();

$cli->addCommand(new CliCommand());
$cli->addCommand(new GrpcCommand());
$cli->addCommand(new ConfigCommand());
$cli->addCommand(new DatabaseCommand());
$cli->addCommand(new ExceptionCommand());
$cli->addCommand(new MiddlewareCommand());
$cli->addCommand(new ServicesCommand());

$cli->run($argv);
