<?php

namespace Public\Index;

require __DIR__ . '/../vendor/autoload.php';

use Bramus\Router\Router;
use Src\Config\Database;
use Src\Controllers\HomePageController;
use Src\Controllers\AuthController;
use Src\Views\View;
use Src\Services\AuthService;
use Src\Services\MatchService;
use Src\Services\UserService;
use Src\Services\StatsService;
use Src\Models\StatisticsModel;

// Загрузка переменных окружения
use Dotenv\Dotenv;

// Инициализация переменных окружения
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Настройка безопасных куки и сессии
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => $_ENV['DOMAIN'] ?? 'localhost', // Укажите ваш домен
    'secure' => isset($_SERVER['HTTPS']),
    'httponly' => true,
    'samesite' => 'Lax'
]);

// Создание подключения к базе данных
$db = new Database();
$pdo = $db->getConnection();

// Создание необходимых сервисов
$authService = new AuthService($pdo);
$matchService = new MatchService();
$userService = new UserService();
$statsService = new StatsService();
$statisticsModel = new StatisticsModel($pdo);

// Создание контроллеров с передачей зависимостей
$authController = new AuthController($authService);
$homePageController = new HomePageController($matchService, $userService, $statsService, $statisticsModel);

// Создание и настройка роутера
$router = new Router();

// Определение маршрутов
$router->get('/', function () use ($homePageController) {
    $homePageController->homePage();
});

$router->get('/login', function () {
    View::render('login');
});

$router->post('/auth', function () use ($authController) {
    $authController->fetchFaceitData();
});

// Другие маршруты, использующие HomePageController
$router->get('/highlights', function () use ($homePageController) {
    $homePageController->homePage();
});

$router->get('/lottery', function () use ($homePageController) {
    $homePageController->homePage();
});

$router->get('/records', function () use ($homePageController) {
    $homePageController->homePage();
});

$router->get('/allMatches', function () use ($homePageController) {
    $homePageController->homePage();
});

$router->run();
