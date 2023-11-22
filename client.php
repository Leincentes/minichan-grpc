<?php
declare(strict_types=1);

use Minichan\Config\Constant;
use Minichan\Grpc\Client;
use Swoole\Coroutine as Co;
use PHP\UserService\User;

require __DIR__ . '/vendor/autoload.php';

co::set(['log_level' => SWOOLE_LOG_ERROR]);
co::set(['log_level' => SWOOLE_LOG_DEBUG]);

co::create(function () {
    $conn = (new Client('localhost', 9502, Constant::GRPC_CALL))
        ->set([
            'open_http2_protocol' => 1,
        ])
        ->connect();

    // Define an array of methods you want to test
    $methods = [
        // 'RegisterUser',
        // 'Login',
        // 'UpdateUser',
        // 'DeleteUser',
        // 'GetUser',
        'GetAllUser',
    ];

    foreach ($methods as $method) {
        // Create the full method name based on the loop variable
        $fullMethod = "/PHP.UserService.UserService/{$method}";

        // Create a new User message
        $message = new User();
        // $message->setUsername('Hello');
        // $message->setPassword('Hi2');

        $type = 'proto';

        // Send the request
        $streamId = $conn->send($fullMethod, $message, $type);

        // Receive and print user data
        while ($data = $conn->recv($streamId)) {
            print_r($data);
        }
    }

    $conn->close();
    echo "closed\n";
});
