<?php

namespace Presentation;

use Application\UserService;

class AuthController
{
    private $service;

    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    public function registerUserAction()
    {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $firstName = $_POST['firstName'];
        $registrationDate = $_POST['registrationDate'];
        $department = $_POST['department'];

        $this->service->registerUser($username, $email, $firstName, $registrationDate, $department);
    }

    public function loginAction()
    {
        $username = $_POST['username'];
        $password = $_POST['password'];
        
        // Validate username and password
        
        if ($this->service->authenticateUser($username, $password)) {
            // User is authenticated, perform login logic
            // Redirect to home page or any other desired page
        } else {
            // Authentication failed, display error message
            // Redirect back to login page or display error message
        }
    }
}
