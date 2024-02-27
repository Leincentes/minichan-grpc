<?php
declare(strict_types=1);

namespace Services;

use Minichan\Database\Database;
use PHP\ChatApp\ChatAppServiceInterface;
use PHP\ChatApp\MessagesResponse;
use PHP\ChatApp\Response;
use PHP\ChatApp\UserResponse;

class ChatAppService implements ChatAppServiceInterface {
    private Response $res;
    private UserResponse $ures;
    private MessagesResponse $mres;
    private Database $db;

    public function __construct() {
        $this->res = new Response();
        $this->ures = new UserResponse();
        $this->mres = new MessagesResponse();
        $this->db = new Database([
            'type' => 'mysql',
            'host' => 'localhost',
            'database' => 'db_name',
            'username' => 'db_username',
            'password' => 'db_password'
        ]);
    }
    public function RegisterUser(\Minichan\Grpc\ContextInterface $ctx, \PHP\ChatApp\Users $request): Response {
        $existingUser = $this->db->has('users', [
            'username' => $request->getUsername(), 
        ]);

        if ($existingUser) {
            throw new \Minichan\Exception\AlreadyExistsException('User already exist');
        }
        
        $userData = [
            'username' => $request->getUsername(),
            'password' => $request->getPassword(),
            'image' => $request->getImage(),
            'unique_id' => $request->getUniqueId(),
            'status'=> $request->getStatus(),
            'id' => $request->getId(),
        ];

        if ($this->db->insert('users', $userData)) {
            $this->res->setSuccessOrError(true);
            return $this->res->setUser($request);
        } else {
            return $this->res->setSuccessOrError(false);
        }
    }
    public function Login(\Minichan\Grpc\ContextInterface $ctx, \PHP\ChatApp\Users $request): Response {
        try {
            $this->db->update('users', ['status' => $request->getStatus()], ['username' => $request->getUsername()]);
            $data = $this->db->query("SELECT * FROM users WHERE username = '{$request->getUsername()}'");
            if ($data->rowCount() === 0) {
                throw new \Minichan\Exception\NotFoundException('User not found.');
            } 
            
            $loggedIn = false;
            foreach ($data as $user) {
                if (password_verify($request->getPassword(), $user['password'])) {
                    $request->setUsername($user['username'])
                            ->setImage($user['image'])
                            ->setStatus($user['status'])
                            ->setUniqueId($user['unique_id']);
                    
                    $this->res->setSuccessOrError(true);
                    $loggedIn = true;
                    break; 
                }
            }
            
            if (!$loggedIn) {
                throw new \Minichan\Exception\InvalidArgumentException('Incorrect password.');
            }
            
            return $this->res->setUser($request);
        } catch (\Exception $e) {
            $this->res->setSuccessOrError(false);
            return $this->res;
        }
    }
    

    public function Logout(\Minichan\Grpc\ContextInterface $ctx, \PHP\ChatApp\Users $request): Response {
        $data = $this->db->update('users', ['status' => $request->getStatus()], ['unique_id' => $request->getUniqueId()]);
        $rowCount = $data->rowCount();

        if ($rowCount === 0) {
            throw new \Minichan\Exception\NotFoundException('User does not exist.');
        } else {
            return $this->res->setSuccessOrError(true);
        }
    }
    public function GetUsersList(\Minichan\Grpc\ContextInterface $ctx, \PHP\ChatApp\Users $request): Response {
        $data = $this->db->query("SELECT * FROM users WHERE NOT unique_id = {$request->getUniqueId()} ORDER BY id DESC");
 
        $theUsers = [];
        foreach ($data as $user) {
            $users = new \PHP\ChatApp\Users();
            $users->setUsername($user['username'])
                    ->setUniqueId($user['unique_id'])
                    ->setStatus($user['status'])
                    ->setImage($user['image']);
            $theUsers[] = $users;
        }
        return $this->res->setUsers($theUsers);
    }
    public function GetCurrentUser(\Minichan\Grpc\ContextInterface $ctx, \PHP\ChatApp\Users $request): UserResponse {
        $data = $this->db->query("SELECT * FROM users WHERE unique_id = {$request->getUniqueId()}");

        foreach ($data as $user) {
            $request->setUsername($user['username'])
                    ->setUniqueId($user['unique_id'])
                    ->setStatus($user['status'])
                    ->setImage($user['image']);
        }
        return $this->ures->setUser($request);
    }
    public function InsertMessage(\Minichan\Grpc\ContextInterface $ctx, \PHP\ChatApp\Message $request): Response {
        $data = $this->db->query("INSERT INTO messages (incoming_msg_id, outgoing_msg_id, msg)
        VALUES ({$request->getIncommingMsgId()}, {$request->getOutgoingMsgId()}, '{$request->getMsg()}')");
        
        foreach ($data as $msg) {
            $request->setMsgId($msg['msg_id'])
                    ->setIncommingMsgId($msg['incoming_msg_id'])
                    ->setOutgoingMsgId($msg['outgoing_msg_id'])
                    ->setMsg($msg['msg']);
        }
        return $this->res->setMessage($request);
    }
    public function GetMessage(\Minichan\Grpc\ContextInterface $ctx, \PHP\ChatApp\Message $request): MessagesResponse {
        $data = $this->db->query("SELECT * FROM messages LEFT JOIN users ON users.unique_id = messages.outgoing_msg_id
        WHERE (outgoing_msg_id = {$request->getOutgoingMsgId()} AND incoming_msg_id = {$request->getIncommingMsgId()})
        OR (outgoing_msg_id = {$request->getIncommingMsgId()} AND incoming_msg_id = {$request->getOutgoingMsgId()}) ORDER BY msg_id");
 
        $messages = [];
        foreach ($data as $msg) {
            $msgReq = new \PHP\ChatApp\Message();
            $msgReq->setIncommingMsgId($msg['incoming_msg_id'])
                    ->setMsgId($msg['msg_id'])
                    ->setOutgoingMsgId($msg['outgoing_msg_id'])
                    ->setImg($msg['image'])
                    ->setMsg($msg['msg']);

            $messages[] = $msgReq;
        }
        return $this->mres->setMessages($messages);
    }
    public function Search(\Minichan\Grpc\ContextInterface $ctx, \PHP\ChatApp\Users $request): Response {
        $data = $this->db->query("SELECT * FROM users WHERE NOT unique_id = {$request->getUniqueId()} AND (username LIKE '%{$request->getSearchTerm()}%')");

        $theUsers = [];
        foreach ($data as $user) {
            $users = new \PHP\ChatApp\Users();
            $users->setUsername($user['username'])
                    ->setUniqueId($user['unique_id'])
                    ->setStatus($user['status'])
                    ->setImage($user['image']);
            $theUsers[] = $users;
        }
        return $this->res->setUsers($theUsers);
    }
}