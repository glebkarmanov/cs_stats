<?php

namespace Src\Models;

use PDO;

class StatisticsModel
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function allStatistics(): array
    {
        $sqlAllStatistics = "SELECT * FROM public.game_statistics";
        $stmt = $this->pdo->query($sqlAllStatistics);
        $result = $stmt->fetchAll();

        $allStatisticsResult = [
            'kills' => 0,
            'deaths' => 0,
            'win' => 0,
            'lose' => 0
        ];

        foreach ($result as $item) {
            $allStatisticsResult['kills'] += isset($item['kills']) ? (int)$item['kills'] : 0;
            $allStatisticsResult['deaths'] += isset($item['deaths']) ? (int)$item['deaths'] : 0;

            if ($item['victory'] == true) {
                $allStatisticsResult['win'] +=1;
            }
            else {
                $allStatisticsResult['lose'] +=1;
            }
        }

        $allStatisticsResult['KD'] = $allStatisticsResult['kills'] / $allStatisticsResult['deaths'];
        $allStatisticsResult['KD'] = round($allStatisticsResult['KD'], 2);

        return $allStatisticsResult;
    }

    public function statisticsLastMatch(): array
    {
        $sqlLastMatch = "SELECT * FROM public.game_statistics ORDER BY match_id DESC LIMIT 1";
        $stmt = $this->pdo->query($sqlLastMatch);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            return [
                'kills' => 0,
                'deaths' => 0,
                'KD' => 0
            ];
        }

        $lastMatchStatisticsResult = [
            'kills' => $result['kills'],
            'deaths' => $result['deaths'],
            'KD' => $result['kills'] / ($result['deaths'] ?: 1) // Предотвращение деления на ноль
        ];
        $lastMatchStatisticsResult['KD'] = round($lastMatchStatisticsResult['KD'], 2);

        return $lastMatchStatisticsResult;
    }
}
