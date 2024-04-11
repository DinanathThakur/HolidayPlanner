<?php

namespace Domain;

class Holiday
{
    private $id;
    private $userid;
    private $reason;
    private $from;
    private $to;
    private $requestedon;
    private $status;

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
}
