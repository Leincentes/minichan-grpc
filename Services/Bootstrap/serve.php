<?php
declare(strict_types= 1);

use Minichan\Config\Config;
use Minichan\Config\Constant;

! defined('BASE_PATH') && define('BASE_PATH', dirname(__DIR__, 2));

require_once BASE_PATH . '/vendor/autoload.php';

// Create a gRPC server instance with the specified host, port, and Swoole mode
$server = (new \Minichan\Grpc\Server(Constant::SERVER_HOST, Constant::SERVER_PORT, SWOOLE_BASE))

    // Register gRPC services defined in the configuration
    ->registerServices(Config::getServices())

    // Define worker context data with a callback to set 'worker_start_time' to the current time
    ->withWorkerContext('worker_start_time', function () {
        return time();
    })
    
    // Add middlewares defined in the configuration
    ->addMiddlewares(Config::getMiddlewares());

// Set additional server options
Config::setOptions($server);

$server->start();
