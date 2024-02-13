<?php
declare(strict_types=1);

use Minichan\Config\Constant;
use Minichan\Grpc\Client;
use Swoole\Coroutine as Co;
use PHP\UserService\User;

require __DIR__ . '/vendor/autoload.php';

Co::set(['log_level' => SWOOLE_LOG_ERROR]);
Co::set(['log_level' => SWOOLE_LOG_DEBUG]);

Co::create(function () {
    $conn = (new Client(Constant::SERVER_HOST, Constant::SERVER_PORT, Constant::GRPC_CALL))
        ->set([
            'open_http2_protocol' => 1,
        ])
        ->connect();

    // Define an array of methods you want to test
    $methods = [
        'RegisterUser',
        // 'Login',
        // 'UpdateUser',
        // 'DeleteUser',
        // 'GetUser',
        // 'GetAllUser',
    ];

    foreach ($methods as $method) {
        // Create the full method name based on the loop variable
        $fullMethod = "/PHP.UserService.UserService/{$method}";

        // Create a new User message
        $message = new User();
        $message->setUsername('tester10');
        $message->setPassword('tester10');

        $type = 'proto';

        // Send the request
        $streamId = $conn->send($fullMethod, $message, $type);

        // Receive and print user data
        while ($data = $conn->recv($streamId)) {
            print_r($data);
        }
    }

    // // Get and display the client statistics
    // $clientStats = $conn->stats();
    // echo "Client Statistics:\n";
    // echo json_encode($clientStats, JSON_PRETTY_PRINT) . "\n";    

    // Create a new HTTP2 client
    $http2Client = new \Swoole\Coroutine\Http2\Client('127.0.0.1', 9502, true);

    // Set the HTTP2 client for the gRPC client
    $conn->setHttpClient($http2Client);

    $conn->close();
    echo "Closed\n";
});
