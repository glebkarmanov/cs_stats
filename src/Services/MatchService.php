<?php

namespace Src\Services;

use GuzzleHttp\Client;
use Exception;
use Dotenv\Dotenv;

class MatchService
{
    private string $apiKey;
    private Client $httpClient;

    public function __construct()
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
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
     * @param string $faceit_id Идентификатор игрока FACEIT
     * @return array Массив матчей с необходимыми данными
     * @throws Exception При ошибках запроса или обработки данных
     */
    public function getLastTenMatches(string $faceit_id): array
    {
        $matches = $this->fetchPlayerMatchHistory($faceit_id);

            for ($i = 0; $i < count($matches); $i++) {
                $checkResult = [];

                $checkResult[] = $this->determineMatchResult($matches[$i]['teams']['faction1']['players'], $faceit_id);

                if ($matches[$i]['results']['score']['faction1'] == $checkResult[0]['faction1']) {
                    unset($matches[$i]['teams']);
                    $matches[$i]['teams'] = 'Победа';
                } else {
                    unset($matches[$i]['teams']);
                    $matches[$i]['teams'] = 'Поражение';
                }


            // Получение Map и Score
            try {
                $scoreAndMap = $this->fetchMatchScoreAndMap($matches[$i]['match_id']);
                $matches[$i]['Map'] = $scoreAndMap['Map'] ?? 'Неизвестно';
                $matches[$i]['Score'] = $scoreAndMap['Score'] ?? 'Неизвестно';
            } catch (Exception $e) {
                $matches[$i]['Map'] = 'Ошибка';
                $matches[$i]['Score'] = 'Ошибка';
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
        } catch (Exception $e) {
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
     */
    private function determineMatchResult(array $data, string $faceit_id)
    {
        $result = [];

        for ($i = 0; $i < count($data); $i++) {
            foreach ($data[$i] as $key => $value) {
                if ($key == 'player_id') {
                    if ($value === $faceit_id) {
                        $result['faction1'] = 1;
                        return $result;
                    } else {
                        $result['faction1'] = 0;
                    }
                }
            }

        }
        return $result;
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
        } catch (Exception $e) {
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
