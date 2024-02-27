<?php

define('BASE_PATH', dirname(__DIR__, 1));
require_once BASE_PATH . '/vendor/autoload.php';

$server = new \Swoole\Http\Server('localhost', 9501);
$server->set([
    'document_root' => __DIR__,
    'enable_coroutine' => true,
    'enable_static_handler' => true,
]);

$server->on('request', new \Minichan\Grpc\SessionHandler(function ($request, $response) {
    $views = BASE_PATH . '/app/views';
    $cache = BASE_PATH . '/app/cache';
    $blade = new eftec\bladeone\BladeOne($views, $cache, eftec\bladeone\BladeOne::MODE_DEBUG);
    
    
    $conn = (new Minichan\Grpc\Client(Minichan\Config\Constant::SERVER_HOST,  Minichan\Config\Constant::SERVER_PORT, Minichan\Config\Constant::GRPC_CALL))
            ->set([
                'open_http2_protocol' => 1,
                'timeout' => 5.0,
            ])
            ->connect();
    $chatService = new  \PHP\ChatApp\ChatAppServiceClient($conn);
    
    $requestUri = $request->server['request_uri'];
    
    switch ($requestUri) {
        case '/':
        case '/register':
            $response->end($blade->run('register'));
            break;
        case '/register/user':
            $targetDir = __DIR__ . '/views/img/';
            $unique_id = rand(time(), 1000000);
            $targetFile = $targetDir . basename($request->files['image']['name']);
            move_uploaded_file($request->files['image']['tmp_name'], $targetFile);
            
            $user = new \PHP\ChatApp\Users();
            $user->setUsername($request->post['username'])
                    ->setPassword(password_hash($request->post['password'], PASSWORD_DEFAULT))
                    ->setStatus('active')
                    ->setImage($request->files['image']['name'])
                    ->setUniqueId($unique_id);
            
            $result = $chatService->RegisterUser($user);
            
            if ($result->getSuccessOrError() == true) {
                $u = json_decode($result->getUser()->serializeToJsonString());
                if ($u !== null) {
                    $response->status(200);
                    $_SESSION['users'] = $u;
                } else {
                    $response->header('/login');
                    $response->status(404);
                }
            } else {
                $response->status(404);
            }
            break;
            
        case '/login':
            $response->end($blade->run('login'));
            break;
        case '/login/user':
            $user = new \PHP\ChatApp\Users();
            $user->setUsername($request->post['username'])
                    ->setPassword($request->post['password'])
                    ->setStatus('active');
            
            $result = $chatService->Login($user);

            if ($result->getSuccessOrError() == true) {
                $u = json_decode($result->getUser()->serializeToJsonString());
                
                if ($u->username == $user->getUsername()) {
                    $_SESSION['users'] = $u;
                    
                    $response->status(200);
                    $response->end(json_encode(['success' => true, 'user' => $u]));
                } else {
                    $response->status(404);
                    $response->end(json_encode(['success' => false, 'message' => 'User not found']));
                }
            } else {
                $response->status(404);
                $response->end(json_encode(['success' => false, 'message' => 'Login failed']));
            }
            break;
            
        case '/home':
            if(!empty($_SESSION['users'])) {
                $session_user = $_SESSION['users'];

                $response->end($blade->run('home', [
                    'username' => $session_user->username,
                    'image' => $session_user->image,
                    'status' => $session_user->status,
                ]));
                $response->status(200);
            } else {
                $response->status(302);
                $response->header('Location', '/login');
                $response->end();
            }
            break;

        case '/logout':
            if (!empty($_SESSION['users'])) {
                $session_user = $_SESSION['users'];
                $user = new \PHP\ChatApp\Users();
                $user->setUniqueId($session_user->uniqueId)
                        ->setStatus('not active');
                $chatService->Logout($user);

                session_unset();
                session_destroy();
                $response->status(302);
                $response->header('Location', '/login');
                $response->end();
            } else {
                $response->status(302);
                $response->header('Location', '/login');
                $response->end();
            }
            break;

        case '/users':
            if(!empty($_SESSION['users'])) {
                $session_user = $_SESSION['users'];
                $user = new \PHP\ChatApp\Users();
                $user->setUniqueId($session_user->uniqueId);
                $result = $chatService->GetUsersList($user);
                $users = json_decode($result->serializeToJsonString());

                $response->end(json_encode($users->users));
                $response->status(200);
            } else {
                $response->status(302);
                $response->header('Location', '/login');
                $response->end();
            }
            break;

        case '/chat': 
            $user_id = $request->get['user_id'];
            $user = new \PHP\ChatApp\Users();
            $user->setUniqueId($user_id);
            $result = $chatService->GetCurrentUser($user);
            $other_user = json_decode($result->serializeToJsonString());

            foreach ($other_user as $u) {
                $response->end($blade->run('chat', [
                    'username' => $u->username,
                    'image' => $u->image,
                    'status' => $u->status,
                    'userId' => $u->uniqueId,
                ]));
            }
            break;

        case '/get-chat': 
            if(!empty($_SESSION['users'])) {
                $session_user = $_SESSION['users'];
                $incoming_id = $request->post['incoming_id'];
                $outgoing_id = $session_user->uniqueId;
    
                $msg = new \PHP\ChatApp\Message();
                $msg->setOutgoingMsgId((int)$outgoing_id)
                    ->setIncommingMsgId((int)$incoming_id);
    
                $result = $chatService->GetMessage($msg);
                $theMsg = json_decode($result->serializeToJsonString());
                $output = '';
    
                foreach ($theMsg->messages as $m) {
                    $msg = isset($m->msg) ? $m->msg : '';
                    
                    $img = isset($m->img) ? 'views/img/'.$m->img : '';
                
                    if ($m->outgoingMsgId == $outgoing_id) {
                        $output .= '<div class="chat outgoing">
                                        <div class="details">
                                            <p>'. $msg .'</p>
                                        </div>
                                    </div>';
                    } else {
                        $output .= '<div class="chat incoming">
                                        <img src="'.$img.'" alt="">
                                        <div class="details">
                                            <p>'. $msg .'</p>
                                        </div>
                                    </div>';
                    }
                }
                
                $response->status(200);
                $response->end($output);
            }
            break;
        case '/insert-chat':
            $session_user = $_SESSION['users'];
            $incoming_id = $request->post['incoming_id'];
            $message = $request->post['message'];
            
            $msg = new \PHP\ChatApp\Message();
            $msg->setOutgoingMsgId((int)$session_user->uniqueId)
                ->setIncommingMsgId((int)$incoming_id)
                ->setMsg($message);

            $result = $chatService->InsertMessage($msg);
            $theMsg = $result->serializeToJsonString();
            $response->end($theMsg);
            break;

        case '/search':
            if(!empty($_SESSION['users'])) {
                $session_user = $_SESSION['users'];
                $current_user = $session_user->uniqueId;

                $user = new \PHP\ChatApp\Users();
                $user->setUniqueId($current_user)
                    ->setSearchTerm($request->get['searchTerm']);
                $result = $chatService->Search($user);
                $other_user = json_decode($result->serializeToJsonString());

                $response->end(json_encode($other_user->users));
                $response->status(200);
            }  else {
                $response->status(302);
                $response->header('Location', '/login');
                $response->end();
            }
            break;
        default:
            $response->status(302);
            $response->header('Location', '/login');
            $response->end();
            break;
    }

}));

$server->start();
