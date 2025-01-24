<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CS2 Статистика</title>
    <link rel="stylesheet" href="/public/css/styles.css">
</head>
<body>

<!-- Верхняя панель -->
<div class="navbar">
    <div class="logo">
        CS2Stats
    </div>
    <!-- Удален блок .stats из navbar -->
</div>

<!-- Основной контейнер -->
<div class="container">

    <!-- Профиль пользователя -->
    <div class="profile">
        <div class="info">
            <div class="background">
                <?php if (!empty($user['avatar'])): ?>
                    <img src="<?= htmlspecialchars($user['avatar']) ?>" alt="Аватар пользователя">
                <?php else: ?>
                    <!-- Безликий профиль -->
                    <img src="/public/images/calcifer.jpeg" alt="Безликий профиль">
                <?php endif; ?>
            </div>
            <div class="details">
                <div class="username"><?= htmlspecialchars($user['nickname'] ?? 'Глеб') ?></div>
                <div class="registration-info">
                    <div class="registration-date">Дата регистрации: <?= htmlspecialchars(!empty($user['activated_at']) ? date("d.m.Y", strtotime($user['activated_at'])) : 'Неизвестно') ?></div>
                </div>
                <div class="elo-level">
                    <div class="elo">
                        <span>Elo:</span>
                        <span><?= htmlspecialchars($user['faceit_elo'] ?? '2500') ?></span>
                    </div>
                    <div class="level">
                        <span>Уровень FACEIT:</span>
                        <span><?= htmlspecialchars($user['skill_level'] ?? '1') ?></span>
                    </div>
                </div>
            </div>
            <a href="<?= htmlspecialchars($user['faceit_url'] ?? '#') ?>" class="steam-button">Перейти на страницу Faceit</a>
        </div>
    </div>

    <!-- Горизонтальное меню -->
    <div class="horizontal-menu">
        <a href="#highlights" class="menu-link">Хайлайты</a>
        <a href="#friends" class="menu-link">Друзья</a>
        <a href="#matches" class="menu-link">Матчи</a>
    </div>

    <!-- Разделитель -->
    <div class="separator"></div>

    <!-- Статистика игры -->
    <div class="game-stats">
        <h3>CS2 Статистика</h3>
        <div class="stats-container">
            <div class="game-logo">
                <img src="/public/images/Counter-strike_2.jpg" alt="Логотип CS2">
            </div>
            <div class="stats">
                <div class="stat">
                    <span>Общее количество матчей:</span>
                    <span><?= htmlspecialchars($stats['matches'] ?? '150') ?></span>
                </div>
                <div class="stat">
                    <span>Процент побед:</span>
                    <span><?= htmlspecialchars("{$stats['winRate']}%" ?? '60%') ?></span>
                </div>
                <div class="stat">
                    <span>Последние результаты:</span>
                    <span><?= htmlspecialchars($stats['lastResult'] ?? 'Победа') ?></span>
                </div>
                <div class="stat">
                    <span>Самый длинный winstreak:</span>
                    <span><?= htmlspecialchars($stats['currWinStreak'] ?? '300') ?></span>
                </div>
                <div class="stat">
                    <span>ADR:</span>
                    <span><?= htmlspecialchars($stats['ADR'] ?? '200') ?></span>
                </div>
                <div class="stat">
                    <span>K/D:</span>
                    <span><?= htmlspecialchars($stats['KD'] ?? '1.5') ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- История матчей -->
    <div class="match-history">
        <h3>История матчей</h3>
        <ul class="matches-list">
            <?php foreach ($matches as $match): ?>
                <li class="<?= ($match['teams'] === 'Победа') ? 'win' : 'lose' ?>" onclick="location.href='/match.php?match_id=<?= htmlspecialchars($match['match_id']) ?>'">
                    <div class="match-info">
                        <div class="date"><?= htmlspecialchars($match['started_at']) ?></div>
                        <div class="gameMode"><?= htmlspecialchars($match['game_mode']) ?></div>
                        <div class="result"><?= htmlspecialchars($match['teams'] === 'Победа' ? 'Победа' : 'Поражение') ?></div>
                        <div class="score"><?= htmlspecialchars($match['Score']) ?></div>
                        <div class="map"><?= htmlspecialchars($match['Map']) ?></div>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
        <a href="/allMatches" class="show-more">Показать еще</a>
    </div>

</div>

</body>
</html>
