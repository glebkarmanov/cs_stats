<?php

namespace Src\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Exception;

class MatchService
{
    private string $apiKey;
    private Client $httpClient;

    public function __construct()
    {
        $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();

        $this->apiKey = $_ENV['FACEIT_API_KEY'];
        $this->httpClient = new Client([
            'headers' => [
                'Authorization' => "Bearer {$this->apiKey}",
                'Accept'        => 'application/json',
            ]
        ]);
    }

    /**
     * Получает информацию о последних десяти матчах игрока
     *
     * @param string $faceit_id
     * @return array
     * @throws Exception
     */
    public function getLastTenMatches(string $faceit_id): array
    {
        $matches = $this->fetchPlayerMatchHistory($faceit_id);

        foreach ($matches as &$match) {
            $result = $this->determineMatchResult($match['teams'], $faceit_id, $match['match_id']);
            $match['teams'] = $result === 1 ? 'Победа' : 'Поражение';
            $match['started_at'] = $formattedDate = date("d.m.Y H:i", $match['started_at']);


            // Получение Map и Score
            try {
                $scoreAndMap = $this->fetchMatchScoreAndMap($match['match_id']);
                $match['Map'] = $scoreAndMap['Map'] ?? 'Неизвестно';
                $match['Score'] = $scoreAndMap['Score'] ?? 'Неизвестно';
            } catch (Exception $e) {
                $match['Map'] = 'Ошибка';
                $match['Score'] = 'Ошибка';
            }
        }

        return $matches;
    }

    /**
     * Выполняет запрос к API для получения истории матчей игрока
     *
     * @param string $faceit_id
     * @return array
     * @throws Exception
     */
    private function fetchPlayerMatchHistory(string $faceit_id): array
    {
        $currentTime = time();
        $url = "https://open.faceit.com/data/v4/players/{$faceit_id}/history";
        $query = [
            'game'   => 'cs2',
            'from'   => 1674053320,
            'to'     => $currentTime,
            'offset' => 0,
            'limit'  => 10
        ];

        try {
            $response = $this->httpClient->get($url, ['query' => $query]);
            $data = json_decode($response->getBody(), true);
        } catch (GuzzleException $e) {
            throw new Exception("Ошибка при получении истории матчей: " . $e->getMessage());
        }

        if (!isset($data['items'])) {
            throw new Exception("Некорректный ответ от API при получении истории матчей.");
        }

        $matches = array_map(function ($item) {
            return [
                'match_id'    => $item['match_id'] ?? '',
                'game_mode'   => $item['game_mode'] ?? '',
                'started_at'  => $item['started_at'] ?? '',
                'teams'       => $item['teams'] ?? [],
                'results'     => $item['results'] ?? []
            ];
        }, $data['items']);

        return $matches;
    }

    /**
     * Определяет результат матча для игрока
     *
     * @param array $teams
     * @param string $faceit_id
     * @return int 1 - Победа, 0 - Поражение
     */
    private function determineMatchResult(array $teams, string $faceit_id, $match_id): int
    {
        $url = "https://open.faceit.com/data/v4/matches/{$match_id}/stats";

        try {
            $response = $this->httpClient->get($url);
            $data = json_decode($response->getBody(), true);
        } catch (GuzzleException $e) {
            throw new Exception("Ошибка при получении данных матча: " . $e->getMessage());
        }

        $winner = $data['rounds'][0]['round_stats']['Winner'];

        $result = [];

        foreach ($teams as $faction) {
            foreach ($faction['players'] as $player) {
                if ($player['player_id'] === $faceit_id) {
                    if ($faction['team_id'] == $winner) {
                        return 1;
                    }
                }
            }
        }
        return 0;
    }

    /**
     * Получает Map и Score матча
     *
     * @param string $match_id
     * @return array
     * @throws Exception
     */
    private function fetchMatchScoreAndMap(string $match_id): array
    {
        $url = "https://open.faceit.com/data/v4/matches/{$match_id}/stats";

        try {
            $response = $this->httpClient->get($url);
            $data = json_decode($response->getBody(), true);
        } catch (GuzzleException $e) {
            throw new Exception("Ошибка при получении данных матча: " . $e->getMessage());
        }

        if (!isset($data['rounds'][0]['round_stats'])) {
            throw new Exception("Некорректный ответ от API при получении данных матча.");
        }

        $roundStats = $data['rounds'][0]['round_stats'];

        return [
            'Map'   => $roundStats['Map'] ?? '',
            'Score' => $roundStats['Score'] ?? ''
        ];
    }
}
