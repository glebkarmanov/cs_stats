<?php

namespace Src\Models;

use PDO;

class UserModel
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getUserInfo()
    {
        $sqlGetUserInfo = "select * from users where id = :id LIMIT 1";
        $stmt = $this->pdo->prepare($sqlGetUserInfo);

        $stmt->bindParam(':id', $_SESSION['id'], PDO::PARAM_INT);

        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result;
    }

    public function newUser($faceit_id, $username, $image)
    {
        $newUserSql = "INSERT INTO users (faceit_id, username, created_at, image) VALUES (:faceit_id, :username, NOW(), :image)";
        $stmt = $this->pdo->prepare($newUserSql);

        $stmt->bindParam(':faceit_id', $faceit_id);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':image', $image);

        $stmt->execute();

        $lastId = $this->pdo->lastInsertId();
        return $lastId;
    }

    public function checkUser($username)
    {
        $checkUserSql = "SELECT id, faceit_id FROM users WHERE username = :username";
        $stmt = $this->pdo->prepare($checkUserSql);

        $stmt->bindParam(':username', $username);

        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result;
    }
}