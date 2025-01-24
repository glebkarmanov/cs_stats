<?php

namespace Src\Services;

use Dotenv\Dotenv;

class StatsService
{
    private $apiKey;

    public function __construct()
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();

        $this->apiKey = $_ENV['FACEIT_API_KEY'];
    }

    public function getAllStatistics()
    {
        $faceit_id = $_SESSION['faceit_id'];
        $url = "https://open.faceit.com/data/v4/players/{$faceit_id}/stats/cs2";
        $needKey = ['Matches', 'Win Rate %', 'Recent Results', 'Longest Win Streak', 'ADR', 'Average K/D Ratio'];
        $needResponseKey = ['Matches' => 'matches', 'Win Rate %' => 'winRate', 'Recent Results' => 'lastResult', 'Longest Win Streak' => 'currWinStreak', 'ADR' => 'ADR', 'Average K/D Ratio' => 'KD'];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer {$this->apiKey}",
            "Accept: application/json"
        ]);

        $response = curl_exec($ch);
        $data = json_decode($response, true);
        $newData = $data['lifetime'];

        $result = $this->getNeedValue($newData, $needKey);
        $newResult = $this->renameKey($result, $needResponseKey);

        $lastResult = $this->arrayToStringLastResult($newResult['lastResult']);
        $newResult['lastResult'] = $lastResult;

        return $newResult;
    }

    public function getNeedValue($data, $needKey)
    {
        $result = [];

        foreach ($data as $key => $item) {
            if (in_array($key, $needKey)) {
                $result[$key] = $item;
            }
        }
        return $result;
    }

    public function renameKey($data, $needResponseKey)
    {
        $result = [];

        foreach ($needResponseKey as $key => $item) {
            if (array_key_exists($key, $data)) {
                $result[$item] = $data[$key];
            }
        }
        return $result;
    }

    public function arrayToStringLastResult($array) {
        $result = '';

        foreach ($array as $item) {
            if ($item === '1') {
                $result = "{$result} W";
            } else {
                $result = "{$result} L";
            }
        }
        return $result;
    }
}
