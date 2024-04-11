<?php

namespace Application;

use Domain\User;
use Infrastructure\HolidayRepository;
use Infrastructure\UserRepository;

class UserService
{
    private $userRepository;
    private $holidayRepository;

    public function __construct(UserRepository $userRepository, HolidayRepository $holidayRepository)
    {
        $this->userRepository = $userRepository;
        $this->holidayRepository = $holidayRepository;
    }

    public function registerUser($username, $email, $firstName, $lastName, $department)
    {
        $user = new User();

        $user->setUsername($username);
        $user->setEmail($email);
        $user->setFirstName($firstName);
        $user->setLastName($lastName);
        $user->setDepartment($department);

        $userID = $this->userRepository->save($user);
    }

    public function getUserByUsername($username)
    {
        $user = $this->userRepository->findOneByUsername($username);

        return $this->userRepository->objectToArray($user);
    }

    public function getAllUsers($where, $limit, $page)
    {
        $users = $this->userRepository->getUsersWithHolidays($where, $limit, $page);
        $usersArray = $this->userRepository->objectsToArrays($users);

        foreach ($usersArray as $key => $user) {
            $usersArray[$key]['holidays'] = $this->holidayRepository->objectsToArrays($user['holidays']);
        }

        return $usersArray;
    }

    public function authenticateUser($username, $password)
    {
        $user = $this->userRepository->findOneByUsername($username);

        if ($user && $user->getPassword() === md5($password)) {
            return true;
        }

        return false;
    }
}
