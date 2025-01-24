<?php

namespace Src\Models;

use PDO;
class StatisticsModel
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function allStatistics()
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

    public function statisticsLastMatch()
    {
        $sqlLastMatch = "SELECT * FROM public.game_statistics order by match_id desc limit 1";
        $stmt = $this->pdo->query($sqlLastMatch);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $lastMatchStatiscticsResult = [];

        $lastMatchStatiscticsResult['kills'] = $result['kills'];
        $lastMatchStatiscticsResult['deaths'] = $result['deaths'];

        $lastMatchStatiscticsResult['KD'] = $lastMatchStatiscticsResult['kills'] / $lastMatchStatiscticsResult['deaths'];
        $lastMatchStatiscticsResult['KD'] = round($lastMatchStatiscticsResult['KD'], 2);


        return $lastMatchStatiscticsResult;
    }
}