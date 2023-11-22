<?php

declare(strict_types=1);
namespace Minichan\Services;

use Minichan\Database\Database;
use PHP\UserService\User;
use PHP\UserService\UserResponse;
use PHP\UserService\UserServiceInterface;

class AuthService implements UserServiceInterface
{
    private UserResponse $response;
    private Database $db;
    public function __construct() {
        $this->response = new UserResponse();
        // dynamic instance that depends on you
        $this->db = new Database([
            'type' => 'mysql',
            'host' => 'localhost',
            'database' => 'user_auth',
            'username' => 'tester',
            'password' => 'testeR123()!'
        ]);
    }
    /**
    * @param \Minichan\Grpc\ContextInterface $ctx
    * @param User $request
    * @return User
    *
    * @throws \Minichan\Exception\InvokeException
    */
    public function RegisterUser(\Minichan\Grpc\ContextInterface $ctx, User $request): UserResponse {
        $existingUsers = $this->db->select('users', ['username'], ['username' => $request->getUsername()]);

        if (count($existingUsers) > 0) {
            throw new \Minichan\Exception\AlreadyExistsException("user with the provided username already exists");
        }

        $userRecord = [
            'username' => $request->getUsername(),
            'password' => password_hash($request->getPassword(), PASSWORD_DEFAULT),
        ];

        $this->db->insert('users', $userRecord);

        return $this->response->setMessage('registered user: ' . $userRecord['username']);
    }   

    /**
     * @param \Minichan\Grpc\ContextInterface $ctx
     * @param User $request
     * @return UserResponse
     *
     * @throws \Minichan\Exception\InvokeException
     */
    public function Login(\Minichan\Grpc\ContextInterface $ctx, User $request): UserResponse {
        $users = $this->db->select('users', ['username', 'password'],
            [
                'username' => $request->getUsername(),
            ]);
        if (count($users) > 0) {
            $user = $users[0];
    
            if (password_verify($request->getPassword(), $user['password'])) {
                return $this->response->setMessage('User login successful');
            } else {
                throw new \Minichan\Exception\InvokeException("authentication failed");
            }
        } else {
            throw new \Minichan\Exception\InvokeException("user not found");
        }
    }

    /**
     * @param \Minichan\Grpc\ContextInterface $ctx
     * @param User $request
     * @return User
     *
     *a@throws \Minichan\Exception\InvokeException
     */
    public function UpdateUser(\Minichan\Grpc\ContextInterface $ctx, User $request): UserResponse {
        $users = $this->db->update('users', 
            [
                'username' => $request->getUsername(),
                'password' => password_hash($request->getPassword(), PASSWORD_DEFAULT),
            ], [
            'username' => $request->getUsername(),
            ]);
        if($users) {
            return $this->response->setMessage('user updated successfully');
        } else {
            throw new \Minichan\Exception\InvokeException("update user failed");
        }
    }

    /**
     * @param \Minichan\Grpc\ContextInterface $ctx
     * @param User $request
     * @return User
     *
     * @throws \Minichan\Exception\InvokeException
     */
    public function DeleteUser(\Minichan\Grpc\ContextInterface $ctx, User $request): UserResponse {
        $users = $this->db->delete('users', [
            'username' => $request->getUsername(),
        ]);
        if($users) {
            return $this->response->setMessage('user deleted successfully');
        } else {
            throw new \Minichan\Exception\InvokeException("delete user failed");
        }
    }

    /**
     * @param \Minichan\Grpc\ContextInterface $ctx
     * @param User $request
     * @return User
     *
     * @throws \Minichan\Exception\InvokeException
     */
    public function GetUser(\Minichan\Grpc\ContextInterface $ctx, User $request): UserResponse {

        $users = $this->db->get('users', ['username', 'password'], ['username' => $request->getUsername()]);

        if($users) {
            return $this->response->setMessage('user: ' . $users['username']);
        } else {
            throw new \Minichan\Exception\InvokeException("user does not exist");
        }
    }

    /**
     * @param \Minichan\Grpc\ContextInterface $ctx
     * @param User $request
     * @return User[]
     *
     * @throws \Minichan\Exception\InvokeException
     */
    public function GetAllUser(\Minichan\Grpc\ContextInterface $ctx, User $request): UserResponse { 
        
        $users = $this->db->select('users', ['username', 'password']);
        $responseMessage = '';

        foreach ($users as $user) {
            $responseMessage .= 'users: ' . $user['username'] . PHP_EOL;
        }
    
        return $this->response->setMessage($responseMessage);
    }
}
