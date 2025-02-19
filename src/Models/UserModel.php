<?php

namespace Src\Models;

use PDO;

class UserModel
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getUserInfo(): array
    {
        $sqlGetUserInfo = "SELECT * FROM users WHERE id = :id LIMIT 1";
        $stmt = $this->pdo->prepare($sqlGetUserInfo);

        $stmt->bindParam(':id', $_SESSION['id'], PDO::PARAM_INT);

        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ?? [];
    }

    public function newUser(string $faceit_id, string $username, string $image): int
    {
        $newUserSql = "INSERT INTO users (faceit_id, username, created_at, image) VALUES (:faceit_id, :username, NOW(), :image)";
        $stmt = $this->pdo->prepare($newUserSql);

        $stmt->bindParam(':faceit_id', $faceit_id);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':image', $image);

        $stmt->execute();

        return (int)$this->pdo->lastInsertId();
    }

    public function checkUser(string $username): ?array
    {
        $checkUserSql = "SELECT id, faceit_id FROM users WHERE username = :username";
        $stmt = $this->pdo->prepare($checkUserSql);

        $stmt->bindParam(':username', $username);

        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ?: null;
    }
}
