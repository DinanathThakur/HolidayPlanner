<?php

namespace Infrastructure;

use Domain\Holiday;
use Domain\User;
use PDO;

class UserRepository
{
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function save(User $user)
    {
        $stmt = $this->db->prepare("INSERT INTO users (username, email, first_name, last_name, department) VALUES (:username, :email, :first_name, :last_name, :department)");

        $stmt->bindParam(':username', $user->getUsername(), PDO::PARAM_STR);
        $stmt->bindParam(':email', $user->getEmail(), PDO::PARAM_STR);
        $stmt->bindParam(':first_name', $user->getFirstName(), PDO::PARAM_STR);
        $stmt->bindParam(':last_name', $user->getLastName(), PDO::PARAM_STR);
        $stmt->bindParam(':department', $user->getDepartment(), PDO::PARAM_STR);

        $stmt->execute();

        return $this->db->lastInsertId();
    }

    public function findOneByUsername($username)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            return $this->arrayToObject($result);
        }
        return null;
    }

    public function findOneByID($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            return $this->arrayToObject($result);
        }
        return null;
    }

    public function findAll($where = [], $limit, $page)
    {
        $query = "SELECT * FROM users";
        $params = [];

        if (!empty($where)) {
            $conditions = [];
            foreach ($where as $key => $value) {
                $conditions[] = "$key = :$key";
                $params[":$key"] = $value;
            }
            $query .= " WHERE " . implode(" AND ", $conditions);
        }

        $query .= " LIMIT :limit OFFSET :offset";
        $params[':limit'] = $limit;
        $params[':offset'] = ($page - 1) * $limit;

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(function ($result) {
            return $this->arrayToObject($result);
        }, $results);
    }

    public function arrayToObject($result)
    {
        $user = new User();

        $user->setID($result['id']);
        $user->setUsername($result['username']);
        $user->setEmail($result['email']);
        $user->setFirstName($result['first_name']);
        $user->setLastName($result['last_name']);
        $user->setDepartment($result['department']);
        $user->setLeaveBalance($result['leave_balance']);

        return $user;
    }

    public function objectToArray(User $user)
    {
        return [
            'id' => $user->getID(),
            'username' => $user->getUsername(),
            'email' => $user->getEmail(),
            'first_name' => $user->getFirstName(),
            'last_name' => $user->getLastName(),
            'department' => $user->getDepartment(),
            'leave_balance' => $user->getLeaveBalance(),
            'holidays' => $user->getHolidays(),
        ];
    }

    public function objectsToArrays($users)
    {
        return array_map(function (User $user) {
            return $this->objectToArray($user);
        }, $users);
    }

    public function getUsersWithHolidays()
    {
        $query = "SELECT u.*, h.id as holiday_id, h.* FROM users u LEFT JOIN holidays h ON u.id = h.user_id";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $users = [];
        foreach ($results as $result) {
            $user = $this->arrayToObject($result);

            $holiday = new Holiday();

            $holiday->setId($result['holiday_id']);
            $holiday->setUserID($result['user_id']);
            $holiday->setFrom($result['from']);
            $holiday->setTo($result['to']);
            $holiday->setReason($result['reason']);
            $holiday->setStatus($result['status']);
            $holiday->setRequestedOn($result['requested_on']);

            $user->addHoliday($holiday);

            $users[] = $user;
        }
        return $users;
    }
}
