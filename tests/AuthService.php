<?php

declare(strict_types=1);

namespace Tests;

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
            'database' => 'database_name',
            'username' => 'database_user',
            'password' => 'database_pass'
        ]);
    }
    /**
    * @param \Minichan\Grpc\ContextInterface $ctx
    * @param User $request
    * @return UserResponse
    *
    * @throws \Minichan\Exception\InvokeException
    */
    public function RegisterUser(\Minichan\Grpc\ContextInterface $ctx, User $request): UserResponse {
        return $this->response->setMessage("This is a Sample Message.");
    }   

    /**
     * @param \Minichan\Grpc\ContextInterface $ctx
     * @param User $request
     * @return UserResponse
     *
     * @throws \Minichan\Exception\InvokeException
     */
    public function Login(\Minichan\Grpc\ContextInterface $ctx, User $request): UserResponse {
        return $this->response->setMessage("This is a Sample Message.");
    }

    /**
     * @param \Minichan\Grpc\ContextInterface $ctx
     * @param User $request
     * @return UserResponse
     *
     *a@throws \Minichan\Exception\InvokeException
     */
    public function UpdateUser(\Minichan\Grpc\ContextInterface $ctx, User $request): UserResponse {
        return $this->response->setMessage("This is a Sample Message.");
    }

    /**
     * @param \Minichan\Grpc\ContextInterface $ctx
     * @param User $request
     * @return UserResponse
     *
     * @throws \Minichan\Exception\InvokeException
     */
    public function DeleteUser(\Minichan\Grpc\ContextInterface $ctx, User $request): UserResponse {
        return $this->response->setMessage("This is a Sample Message.");
    }

    /**
     * @param \Minichan\Grpc\ContextInterface $ctx
     * @param User $request
     * @return UserResponse
     *
     * @throws \Minichan\Exception\InvokeException
     */
    public function GetUser(\Minichan\Grpc\ContextInterface $ctx, User $request): UserResponse {
        return $this->response->setMessage("This is a Sample Message.");
    }

    /**
     * @param \Minichan\Grpc\ContextInterface $ctx
     * @param User $request
     * @return UserResponse
     *
     * @throws \Minichan\Exception\InvokeException
     */
    public function GetAllUser(\Minichan\Grpc\ContextInterface $ctx, User $request): UserResponse { 
        return $this->response->setMessage("This is a Sample Message.");
    }
}
