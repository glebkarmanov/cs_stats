<?php

namespace Src\Config;

use PDO;
use PDOException;
use Dotenv\Dotenv;
class db
{
    private $conn;

    public function getConnection()
    {
        if ($this->conn === null) {
            try {
                $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
                $dotenv->load();

                $host = $_ENV['DB_HOST'];
                $port = $_ENV['DB_PORT'];
                $dbname = $_ENV['DB_NAME'];
                $user = $_ENV['DB_USER'];
                $password = $_ENV['DB_PASS'];

                $dsn = "pgsql:host={$host};port={$port};dbname={$dbname}";
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

