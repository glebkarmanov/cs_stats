<?php

namespace Src\Views;

class View
{
    public static function render(string $template, array $data = [])
    {
        extract($data);

        $templatePath = __DIR__ . '/' . $template . '.php';

        if (file_exists($templatePath)) {
            require_once $templatePath;
        } else {
            http_response_code(500);
            echo "Хуйня твой шаблон";
            exit;
        }
    }
}