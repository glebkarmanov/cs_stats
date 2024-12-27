<?php

namespace Src\Models;

use PDO;
class matchModel
{
    private $pdo;
    // Создаю переменную подключения к БД, чтобы передать ее в контроллере

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAllMatches()
    {
        $sqlAllMatches = "select * from matches";
        $stmt = $this->pdo->query($sqlAllMatches);
        $result = $stmt->fetchAll();
        return $result;
    }
}

