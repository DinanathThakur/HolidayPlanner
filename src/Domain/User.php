<?php

namespace Domain;

class User
{
    private $id;
    private $username;
    private $email;
    private $firstname;
    private $lastname;
    private $registrationdate;
    private $department;
    private $leavebalance;
    private $holidays;

    public function __call($method, $arguments)
    {
        $action = substr($method, 0, 3);
        $property = strtolower(substr($method, 3));

        if (property_exists($this, $property)) {
            switch ($action) {
                case 'get':
                    return $this->$property;
                case 'set':
                    $this->$property = $arguments[0];
                    return $this;
            }
        } else {
            throw new \Exception("Property $property not found");
        }
    }

    public function addHoliday(Holiday $holiday)
    {
        $this->holidays[] = $holiday;
    }

    public function getHolidays()
    {
        return $this->holidays;
    }
}
