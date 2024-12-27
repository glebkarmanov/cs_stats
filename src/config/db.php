<?php

namespace Src\Config\Db;

use PDO;
use PDOException;
class connectionDb
{
    private $conn;

    public function getConnect()
    {
        if ($this->conn === null) {
            try {
                $dsn = 'pgsql:host=localhost;port=5433;dbname=cs_stats';
                $user = 'postgres';
                $password = '1Nocokooo';

                // Создаём экземпляр PDO
                $this->conn = new PDO($dsn, $user, $password);
                $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

                $this->conn->exec("SET search_path TO public");
            } catch (PDOException $e) {
                die("Ошибка подключения к базе данных: " . $e->getMessage());
            }
        }
        return $this->conn;
    }
}
