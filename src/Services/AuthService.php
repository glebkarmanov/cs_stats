<?php

namespace Src\Services;

use Src\Models\UserModel;
use PDO;
use Dotenv\Dotenv;

class AuthService
{
    private string $apiKey;
    private PDO $pdo;
    public UserModel $userModel;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->userModel = new UserModel($pdo);

        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();

        $this->apiKey = $_ENV['FACEIT_API_KEY'];
    }

    /**
     * Обрабатывает форму ввода никнейма
     */
    public function fetchFaceit(): void
    {
        $this->ensureSessionStarted();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/login');
        }

        $nickname = trim($_POST['faceit_nickname'] ?? '');

        if (empty($nickname)) {
            $this->redirect('/login?error=empty');
        }

        $_SESSION['faceit_nickname'] = $nickname;

        $checkUser = $this->userModel->checkUser($nickname);
        if (!empty($checkUser)) {
            $this->setUserSession($checkUser);
            $this->redirect('/');
        }

        $playerData = $this->fetchPlayerDataFromAPI($nickname);
        if ($playerData === null) {
            $this->redirect('/login?error=invalid');
        }

        $this->registerNewUser($playerData);
        $this->redirect('/');
    }

    private function ensureSessionStarted(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    private function redirect(string $url): void
    {
        header("Location: {$url}");
        exit();
    }

    private function setUserSession(array $user): void
    {
        $_SESSION['id'] = $user['id'];
        $_SESSION['faceit_id'] = $user['faceit_id'];
    }

    private function fetchPlayerDataFromAPI(string $nickname): ?array
    {
        $url = "https://open.faceit.com/data/v4/players?nickname=" . urlencode($nickname);

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer {$this->apiKey}",
            "Accept: application/json"
        ]);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            // Логирование ошибки или другая обработка
            curl_close($ch);
            return null;
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            // Логирование ошибки или другая обработка
            return null;
        }

        $data = json_decode($response, true);

        return $data ?? null;
    }

    private function registerNewUser(array $data): void
    {
        $faceit_id = $data['player_id'] ?? null;
        $username = $data['nickname'] ?? 'Unknown';
        $image = $data['avatar'] ?? '';

        if ($faceit_id === null) {
            // Логирование ошибки или другая обработка
            $this->redirect('/login?error=invalid');
        }

        $id = $this->userModel->newUser($faceit_id, $username, $image);

        $_SESSION['id'] = $id;
        $_SESSION['faceit_id'] = $faceit_id;
        $_SESSION['player_id'] = $data['games']['cs2']['game_player_id'] ?? '';
    }
}
