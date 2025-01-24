<?php
namespace Src\Controllers;

use Src\Models\UserModel;
use Src\Services\AuthService;
use PDO;
class AuthController
{
    public $userModel;
    public AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }
    /**
     * Обрабатывает форму ввода никнейма
     */
    public function fetchFaceitData(): void
    {
        $this->authService->fetchFaceit();
    }
}
