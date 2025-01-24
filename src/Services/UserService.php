<?php

namespace Src\Services;

use GuzzleHttp\Client;
use Exception;
use Dotenv\Dotenv;

class UserService
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
     * Получает информацию о пользователе по никнейму
     *
     * @return array Массив с информацией о пользователе
     * @throws Exception При ошибках запроса или обработки данных
     */
    public function getUserInfo(): array
    {
        if (!isset($_SESSION['faceit_nickname'])) {
            throw new Exception("Никнейм пользователя не установлен в сессии.");
        }

        $nickname = $_SESSION['faceit_nickname'];
        $data = $this->fetchPlayerData($nickname);

        unset($data['games']['csgo']);

        $filteredData = $this->filterUserData($data);

        return $filteredData;
    }

    /**
     * Выполняет запрос к API для получения данных игрока
     *
     * @param string $nickname
     * @return array
     * @throws Exception
     */
    private function fetchPlayerData(string $nickname): array
    {
        $url = "https://open.faceit.com/data/v4/players";

        try {
            $response = $this->httpClient->get($url, [
                'query' => ['nickname' => $nickname]
            ]);
            $data = json_decode($response->getBody(), true);
        } catch (Exception $e) {
            throw new Exception("Ошибка при получении данных пользователя: " . $e->getMessage());
        }

        if (!isset($data['player_id'])) {
            throw new Exception("Игрок с никнеймом {$nickname} не найден.");
        }

        return $data;
    }

    /**
     * Фильтрует данные пользователя, оставляя только необходимые ключи
     *
     * @param array $data
     * @return array
     */
    private function filterUserData(array $data): array
    {
        $needKeys = ['nickname', 'avatar', 'cover_image', 'skill_level', 'faceit_elo', 'faceit_url', 'activated_at'];
        $result = [];

        $this->recursiveFilter($data, $needKeys, $result);

        return $result;
    }

    /**
     * Рекурсивно проходит по массиву и собирает значения нужных ключей.
     *
     * @param array $data Исходный массив данных.
     * @param array $needKeys Ключи, которые необходимо найти.
     * @param array &$result Ссылка на массив, куда будут собираться результаты.
     */
    private function recursiveFilter(array $data, array $needKeys, array &$result): void
    {
        foreach ($data as $key => $value) {
            if (in_array($key, $needKeys, true)) {
                $result[$key] = $value;
            }

            if (is_array($value)) {
                $this->recursiveFilter($value, $needKeys, $result);
            }
        }
    }
}
