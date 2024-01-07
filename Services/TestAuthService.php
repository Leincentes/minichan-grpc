<?php

declare(strict_types=1);

namespace Services;

use PHP\UserService\User;
use PHP\UserService\UserResponse;
use PHP\UserService\UserServiceInterface;

class TestAuthService implements UserServiceInterface
{
    private UserResponse $response;
    private array $dummyUsers;

    public function __construct(array $initialUsers = [])
    {
        $this->response = new UserResponse();
        $this->dummyUsers = [];

        foreach ($initialUsers as $user) {
            $this->addDummyUser($user['username'], $user['password']);
        }

    }

    private function addDummyUser(string $username, string $password): void
    {
        $this->dummyUsers[strtolower($username)] = [
            'username' => $username,
            'password' => password_hash($password, PASSWORD_DEFAULT),
        ];
    }

    public function RegisterUser(\Minichan\Grpc\ContextInterface $ctx, User $request): UserResponse
    {
        $username = $request->getUsername();
        $password = $request->getPassword();

        if (isset($this->dummyUsers[$username])) {
            throw new \Minichan\Exception\AlreadyExistsException("User with the provided username already exists");
        }

        $this->addDummyUser($request->getUsername(), $request->getPassword());

        return $this->response->setMessage('Registered user: ' . $this->dummyUsers[$username]['username']. $this->dummyUsers[$password]['password']);
    }

    public function Login(\Minichan\Grpc\ContextInterface $ctx, User $request): UserResponse
    {
        $username = $request->getUsername();
        $password = $request->getPassword();
        print_r($username . " " . $password);
        
        if (isset($this->dummyUsers[$username])) {
            $user = $this->dummyUsers[$username];
            print_r($user);
    
            if (password_verify($request->getPassword(), $user['password'])) {
                return $this->response->setMessage('User login successful');
            } else {
                throw new \Minichan\Exception\InvokeException("Authentication failed");
            }
        } else {
            throw new \Minichan\Exception\InvokeException("User not found");
        }
    }
    

    public function UpdateUser(\Minichan\Grpc\ContextInterface $ctx, User $request): UserResponse
    {
        if (isset($this->dummyUsers[$request->getUsername()])) {
            $this->dummyUsers[$request->getUsername()] = [
                'username' => $request->getUsername(),
                'password' => password_hash($request->getPassword(), PASSWORD_DEFAULT),
            ];

            return $this->response->setMessage('User updated successfully');
        } else {
            throw new \Minichan\Exception\InvokeException("Update user failed");
        }
    }

    public function DeleteUser(\Minichan\Grpc\ContextInterface $ctx, User $request): UserResponse
    {
        if (isset($this->dummyUsers[$request->getUsername()])) {
            unset($this->dummyUsers[$request->getUsername()]);
            return $this->response->setMessage('User deleted successfully');
        } else {
            throw new \Minichan\Exception\InvokeException("Delete user failed");
        }
    }

    public function GetUser(\Minichan\Grpc\ContextInterface $ctx, User $request): UserResponse
    {
        if (isset($this->dummyUsers[$request->getUsername()])) {
            return $this->response->setMessage('User: ' . $this->dummyUsers[$request->getUsername()]['username']);
        } else {
            throw new \Minichan\Exception\InvokeException("User does not exist");
        }
    }

    public function GetAllUser(\Minichan\Grpc\ContextInterface $ctx, User $request): UserResponse
    {
        $responseMessage = '';

        foreach ($this->dummyUsers as $user) {
            $responseMessage .= 'Users: ' . $user['username'] . PHP_EOL;
        }

        return $this->response->setMessage($responseMessage);
    }
}
