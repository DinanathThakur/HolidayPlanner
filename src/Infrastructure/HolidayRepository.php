<?php

namespace Infrastructure;

use Domain\Holiday;
use PDO;

class HolidayRepository
{
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function save(Holiday $holiday)
    {
        $stmt = $this->db->prepare("INSERT INTO holidays (user_id, reason, from, to) VALUES (:user_id, :reason, :from, :to)");

        $stmt->bindParam(':user_id', $$holiday->getUserID(), PDO::PARAM_INT);
        $stmt->bindParam(':reason', $$holiday->getReason(), PDO::PARAM_STR);
        $stmt->bindParam(':from', $$holiday->getFrom(), PDO::PARAM_STR);
        $stmt->bindParam(':to', $$holiday->getTo(), PDO::PARAM_STR);

        $stmt->execute();

        return $this->db->lastInsertId();
    }

    public function findOneByID($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM holidays WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $holiday = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($holiday) {
            return $this->arrayToObject($holiday);
        }

        return null;
    }

    public function findAll($where = [], $limit, $page)
    {
        $query = "SELECT * FROM holidays";
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
        
        $holidays = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $holidays[] = $this->arrayToObject($row);
        }
        
        return $holidays;
    }

    public function updateStatus($id, $status)
    {
        $stmt = $this->db->prepare("UPDATE holidays SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM holidays WHERE id = ?");
        $stmt->execute([$id]);
    }

    public function arrayToObject($result)
    {
        $holiday = new Holiday();

        $holiday->setID($result['id'] ?? null);
        $holiday->setUserID($result['user_id']);
        $holiday->setReason($result['reason']);
        $holiday->setFrom($result['from']);
        $holiday->setTo($result['to']);
        $holiday->setRequestedOn($result['requested_on']);
        $holiday->setStatus($result['status']);

        return $holiday;
    }

    public function objectToArray(Holiday $holiday)
    {
        return [
            'id' => $holiday->getID(),
            'user_id' => $holiday->getUserID(),
            'reason' => $holiday->getReason(),
            'from' => $holiday->getFrom(),
            'to' => $holiday->getTo(),
            'requested_on' => $holiday->getRequestedOn(),
            'status' => $holiday->getStatus(),
        ];
    }

    public function objectsToArrays($holidays)
    {
        return array_map(function (Holiday $holiday) {
            return $this->objectToArray($holiday);
        }, $holidays);
    }
}
