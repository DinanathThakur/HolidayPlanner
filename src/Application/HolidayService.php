<?php

namespace Application;

use Domain\Holiday;
use Infrastructure\HolidayRepository;

class HolidayService
{
    private HolidayRepository $repository;

    public function __construct(HolidayRepository $repository)
    {
        $this->repository = $repository;
    }

    public function requestHoliday($userID, $reason, $from, $to)
    {
        $holiday = new Holiday();

        $holiday->setUserID($userID);
        $holiday->setReason($reason);
        $holiday->setFrom($from);
        $holiday->setTo($to);

        $holidayID = $this->repository->save($holiday);
    }

    public function getHolidayByID($ID)
    {
        $holiday = $this->repository->findOneByID($ID);
        
        return $this->repository->objectToArray($holiday);
    }

    public function getAllHolidays($where = [], $limit, $page)
    {
        $holidays = $this->repository->findAll($where, $limit, $page);

        return $this->repository->objectsToArrays($holidays);
    }

    public function updateHolidayStatus($ID, $status)
    {
        return $this->repository->updateStatus($ID, $status);
    }
}
