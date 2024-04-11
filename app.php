<?php
// App.php
require_once 'src/Infrastructure/DatabaseConnection.php';
require_once 'src/Application/HolidayService.php';
require_once 'src/Application/UserService.php';
require_once 'src/Presentation/HolidayController.php';
require_once 'src/Presentation/UserController.php';
require_once 'src/Infrastructure/UserRepository.php';
require_once 'src/Infrastructure/HolidayRepository.php';
require_once 'src/Domain/User.php';
require_once 'src/Domain/Holiday.php';

use Application\HolidayService;
use Application\UserService;
use Presentation\HolidayController;
use Presentation\UserController;
use Infrastructure\UserRepository;
use Infrastructure\HolidayRepository;
use Infrastructure\DatabaseConnection;

class App
{
    private $controller;
    private $action;

    public function __construct()
    {
        $databaseConnection = (new DatabaseConnection())->getConnection();

        $userRepository = new UserRepository($databaseConnection);
        $holidayRepository = new HolidayRepository($databaseConnection);

        $userService = new UserService($userRepository, $holidayRepository);
        $holidayService = new HolidayService($holidayRepository);

        $controllerName = $_GET['controller'] ?? null;
        $this->action = $_GET['action'] ?? null;

        if ($controllerName === 'user') {
            $this->controller = new UserController($userService);
        } elseif ($controllerName === 'holiday') {
            $this->controller = new HolidayController($holidayService);
        } else {
            echo json_encode(['error' => 'Invalid controller']);
            exit;
        }
    }

    public function run()
    {
        if (method_exists($this->controller, $this->action . 'Action')) {
            $result = $this->controller->{$this->action . 'Action'}();
            echo json_encode($result);
        } else {
            echo json_encode(['error' => 'Invalid action']);
        }
    }
}


$app = new App();
$app->run();