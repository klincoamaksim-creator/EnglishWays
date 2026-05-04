<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Страница не найдена | English Way</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/404.css">
</head>
<body class="error-page">

<div class="error-container">
    <!-- Большая цифра -->
    <div class="error-code">
        <span class="digit">4</span>
        <div class="error-icon">
            <i class="fas fa-book-open"></i>
        </div>
        <span class="digit">4</span>
    </div>

    <!-- Сообщение -->
    <h1>Страница не найдена</h1>
    <p>Возможно, вы ошиблись адресом или страница была перемещена. Не переживайте — такое бывает даже с носителями языка!</p>

    <!-- Кнопки -->
    <div class="error-buttons">
        <a href="index.php" class="btn-red">
            <i class="fas fa-home"></i> На главную
        </a>
        <a href="materials.php" class="btn-outline">
            <i class="fas fa-book-open"></i> К материалам
        </a>
    </div>

    <!-- Поиск -->
    <div class="error-search">
        <p>Или попробуйте найти то что нужно:</p>
        <form action="search.php" method="GET" class="error-search-form">
            <i class="fas fa-search"></i>
            <input type="text" name="q" placeholder="Поиск по сайту...">
            <button type="submit">Найти</button>
        </form>
    </div>

    <!-- Полезные ссылки -->
    <div class="error-links">
        <span>Возможно вы искали:</span>
        <div class="error-links-row">
            <a href="materials.php?category=grammar">Грамматика</a>
            <a href="materials.php?category=vocabulary">Лексика</a>
            <a href="teacher.php">О преподавателе</a>
        </div>
    </div>
</div>

</body>
</html>