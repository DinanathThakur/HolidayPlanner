<?php

namespace Presentation;

use Application\UserService;

class UserController
{
    private $service;
    const DEFAULT_LIMIT = 10;

    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    public function getAction()
    {
        $username = $_GET['username'];

        $user = $this->service->getUserByUsername($username);

        if ($user) {
            return $user;
        } else {
            return ['error' => 'User not found'];
        }
    }

    public function getAllAction()
    {
        $limit = $_GET['limit'] ?? self::DEFAULT_LIMIT;
        $page = $_GET['page'] ?? 1;

        return $this->service->getAllUsers([], $limit, $page);
    }

    
}
