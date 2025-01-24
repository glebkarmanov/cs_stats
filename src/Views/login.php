<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FACEIT Информация</title>

</head>
<body>

<div class="auth-container">
    <h1>Введите ваш никнейм на FACEIT</h1>
    <form method="POST" action="/auth">
        <input type="text" name="faceit_nickname" placeholder="Ваш никнейм" required>
        <br>
        <button type="submit">Получить информацию</button>
    </form>

    <?php
    // Отображение ошибок, если они есть в URL
    if (isset($_GET['error'])) {
        if ($_GET['error'] === 'empty') {
            echo '<div class="error">Пожалуйста, введите никнейм.</div>';
        } elseif ($_GET['error'] === 'invalid') {
            echo '<div class="error">Некорректный никнейм. Пожалуйста, попробуйте снова.</div>';
        }
    }
    ?>
</div>
</body>
</html>
