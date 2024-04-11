<?php

namespace Presentation;

use Application\HolidayService;

class HolidayController
{
    private $service;
    const DEFAULT_LIMIT = 10;

    public function __construct(HolidayService $service)
    {
        $this->service = $service;
    }

    public function requestHolidayAction()
    {
        $userID = $_POST['userID'];
        $reason = $_POST['reason'];
        $from = $_POST['from'];
        $to = $_POST['to'];
        $requestedOn = $_POST['requestedOn'];
        $status = $_POST['status'];

        $this->service->requestHoliday($userID, $reason, $from, $to, $requestedOn, $status);
    }

    public function getAction()
    {
        $holiday = $this->service->getHolidayByID($_GET['id']);

        if ($holiday) {
            return $holiday;
        } else {
            return ['error' => 'Holiday not found'];
        }
    }

    public function getAllAction()
    {
        $limit = $_GET['limit'] ?? self::DEFAULT_LIMIT;
        $page = $_GET['page'] ?? 1;

        $where['user_id'] = $_GET['userID'] ?? null;

        return $this->service->getAllHolidays($where, $limit, $page);
    }

    public function updateStatusAction()
    {
        $id = $_POST['id'];
        $status = $_POST['status'];

        return ['success' => $this->service->updateHolidayStatus($id, $status) ? 'Status updated' : 'Status not updated'];
    }
}
