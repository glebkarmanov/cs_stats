<?php

namespace Src\Controllers;

use Src\Services\MatchService;
use Src\Services\UserService;
use Src\Services\StatsService;
use Src\Models\StatisticsModel;
use Src\Views\View;

class HomePageController
{
    private MatchService $matchService;
    private UserService $userService;
    private StatsService $statsService;
    private StatisticsModel $statisticsModel;

    public function __construct(
        MatchService $matchService,
        UserService $userService,
        StatsService $statsService,
        StatisticsModel $statisticsModel
    ) {
        $this->matchService = $matchService;
        $this->userService = $userService;
        $this->statsService = $statsService;
        $this->statisticsModel = $statisticsModel;
    }

    public function homePage(): void
    {
        session_start(); // Убедитесь, что сессия запущена

        $faceit_id = $_SESSION['faceit_id'] ?? null;

        if ($faceit_id === null) {
            View::render('login', ['error' => 'Необходима авторизация.']);
            exit();
        }

        try {
            $last10Matches = $this->matchService->getLastTenMatches($faceit_id);
            $userInfo = $this->userService->getUserInfo();
            $allStatistics = $this->statsService->getAllStatistics();
            $statisticsLastMatch = $this->statisticsModel->statisticsLastMatch();
        } catch (\Exception $e) {
            // Логирование ошибки и показ сообщения пользователю
            View::render('home', ['error' => $e->getMessage()]);
            exit();
        }

        $data = [
            'matches'   => $last10Matches,
            'user'      => $userInfo,
            'lastMatch' => $statisticsLastMatch,
            'stats'     => $allStatistics
        ];

        View::render('home', $data);
    }
}
