<?php
require_once 'config/database.php';

$query = isset($_GET['q']) ? trim($_GET['q']) : '';
$query = mysqli_real_escape_string($conn, $query);

$results = [];
$totalFound = 0;

if (!empty($query)) {
    // Поиск по названию и описанию
    $searchSql = "
        SELECT m.*, c.name as category_name, c.slug as category_slug, c.icon as category_icon, c.color as category_color
        FROM materials m
        JOIN categories c ON m.category_id = c.id
        WHERE m.title LIKE '%$query%' OR m.description LIKE '%$query%' OR m.content LIKE '%$query%'
        ORDER BY 
            CASE 
                WHEN m.title LIKE '%$query%' THEN 1
                WHEN m.description LIKE '%$query%' THEN 2
                ELSE 3
            END,
            m.created_at DESC
        LIMIT 20
    ";
    $searchResult = mysqli_query($conn, $searchSql);
    while ($row = mysqli_fetch_assoc($searchResult)) {
        $results[] = $row;
    }
    $totalFound = count($results);
}

// Получаем категории для навигации
$catResult = mysqli_query($conn, "SELECT * FROM categories ORDER BY id");
$categories = [];
while ($row = mysqli_fetch_assoc($catResult)) {
    $categories[] = $row;
}
?>
    <!DOCTYPE html>
    <html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Поиск<?php echo !empty($query) ? ': ' . htmlspecialchars($query) : ''; ?> | English Way</title>
        <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
        <link rel="stylesheet" href="css/home.css">
        <link rel="stylesheet" href="css/search.css">
        <link rel="stylesheet" href="css/theme.css">
    </head>
    <body>

    <!-- ВЕРХНЯЯ НАВИГАЦИЯ -->
    <nav class="top-nav">
        <div class="container">
            <div class="logo-area">
                <a href="index.php" class="logo-link">
                    <img src="image/logo.png" alt="Logo" onerror="this.src='https://via.placeholder.com/45'">
                    <span class="logo-text">English Way</span>
                </a>
            </div>
            <div class="nav-links-left">
                <a href="teacher.php">О преподавателе</a>
                <a href="tests.php">Тесты</a>
                <a href="404.php">Контакты</a>
            </div>
            <form action="search.php" method="GET" class="search-form-top">
                <button type="submit" class="search-submit-btn">
                    <i class="fas fa-search"></i>
                </button>
                <input type="text" name="q" placeholder="Поиск материалов..." value="<?php echo htmlspecialchars($query); ?>" autofocus>
                <?php if (!empty($query)): ?>
                    <a href="search.php" class="search-clear">&times;</a>
                <?php endif; ?>
            </form>
            <button class="theme-toggle" id="themeToggle" title="Сменить тему">
                <i class="fas fa-moon"></i>
            </button>
        </div>
    </nav>

    <!-- ВТОРАЯ НАВИГАЦИЯ -->
    <nav class="second-nav">
        <div class="container">
            <div class="nav-secondary">
                <a href="index.php"><i class="fas fa-home"></i> Главная</a>
                <a href="materials.php">Все материалы</a>
                <?php foreach ($categories as $cat): ?>
                    <a href="materials.php?category=<?php echo $cat['slug']; ?>"><?php echo htmlspecialchars($cat['name']); ?></a>
                <?php endforeach; ?>
            </div>
        </div>
    </nav>

    <!-- ЗАГОЛОВОК -->
    <section class="page-header">
        <div class="container">
            <div class="page-header-content">
                <div class="page-header-text">
                    <h1>Поиск материалов</h1>
                    <?php if (!empty($query)): ?>
                        <p>По запросу «<strong><?php echo htmlspecialchars($query); ?></strong>» найдено: <strong><?php echo $totalFound; ?></strong></p>
                    <?php else: ?>
                        <p>Введите поисковый запрос</p>
                    <?php endif; ?>
                </div>
                <div class="page-header-icon">
                    <i class="fas fa-search"></i>
                </div>
            </div>
        </div>
    </section>

    <!-- РЕЗУЛЬТАТЫ -->
    <section class="search-results">
        <div class="container">

            <?php if (empty($query)): ?>
                <!-- Форма поиска -->
                <div class="search-hero" data-aos="fade-up">
                    <div class="search-hero-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <h2>Поиск по архиву</h2>
                    <p>Введите ключевые слова для поиска материалов</p>
                    <form action="search.php" method="GET" class="search-hero-form">
                        <input type="text" name="q" placeholder="Например: времена глагола..." required>
                        <button type="submit"><i class="fas fa-search"></i> Найти</button>
                    </form>
                </div>
            <?php elseif ($totalFound > 0): ?>
                <!-- Результаты -->
                <div class="results-list">
                    <?php foreach ($results as $index => $item): ?>
                        <a href="material.php?id=<?php echo $item['id']; ?>" class="result-item" data-aos="fade-up" data-aos-delay="<?php echo $index * 50; ?>">
                            <div class="result-item-icon" style="background: <?php echo $item['category_color']; ?>15;">
                                <i class="fas <?php echo $item['category_icon']; ?>" style="color: <?php echo $item['category_color']; ?>;"></i>
                            </div>
                            <div class="result-item-content">
                                <span class="result-item-title">
                                    <?php
                                    $title = htmlspecialchars($item['title']);
                                    if (!empty($query)) {
                                        $title = preg_replace('/(' . preg_quote($query, '/') . ')/iu', '<mark>$1</mark>', $title);
                                    }
                                    echo $title;
                                    ?>
                                </span>
                                <span class="result-item-desc">
                                    <?php
                                    $desc = $item['description'] ?? '';
                                    $desc = htmlspecialchars(mb_substr($desc, 0, 120));
                                    if (!empty($query)) {
                                        $desc = preg_replace('/(' . preg_quote($query, '/') . ')/iu', '<mark>$1</mark>', $desc);
                                    }
                                    echo $desc;
                                    if (mb_strlen($item['description'] ?? '') > 120) echo '...';
                                    ?>
                                </span>
                                <span class="result-item-meta">
                                    <span class="result-category" style="color: <?php echo $item['category_color']; ?>;">
                                        <?php echo htmlspecialchars($item['category_name']); ?>
                                    </span>
                                    <span class="result-level"><?php echo $item['level']; ?></span>
                                    <span class="result-date"><?php echo date('d.m.Y', strtotime($item['created_at'])); ?></span>
                                </span>
                            </div>
                            <?php if ($item['is_new']): ?>
                                <span class="result-badge">NEW</span>
                            <?php endif; ?>
                            <div class="result-arrow">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <!-- Ничего не найдено -->
                <div class="no-results" data-aos="fade-up">
                    <div class="no-results-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <h3>Ничего не найдено</h3>
                    <p>По запросу «<?php echo htmlspecialchars($query); ?>» материалов нет. Попробуйте изменить запрос.</p>
                    <a href="search.php" class="btn-primary">Новый поиск</a>
                </div>
            <?php endif; ?>

        </div>
    </section>

    <!-- ПОДВАЛ -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-column">
                    <div class="logo-area">
                        <a href="index.php" class="logo-link">
                            <img src="image/logo.png" alt="Logo" onerror="this.src='https://via.placeholder.com/45'">
                            <span class="logo-text">English Way</span>
                        </a>
                    </div>
                    <p class="footer-desc">Открой мир английского языка вместе с профессиональным преподавателем.</p>
                    <div class="footer-social">
                        <a href="#" class="social-icon"><i class="fab fa-telegram"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-whatsapp"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-vk"></i></a>
                    </div>
                </div>
                <div class="footer-column">
                    <h4>Навигация</h4>
                    <ul class="footer-links">
                        <li><a href="materials.php">Все материалы</a></li>
                        <li><a href="teacher.php">О преподавателе</a></li>
                        <li><a href="404.php">Контакты</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h4>Контакты</h4>
                    <ul class="footer-contacts">
                        <li><i class="fas fa-envelope"></i><div><span>Email</span><a href="mailto:hello@english-way.ru">hello@english-way.ru</a></div></li>
                        <li><i class="fas fa-phone-alt"></i><div><span>Телефон</span><a href="tel:+79991234567">+7 (999) 123-45-67</a></div></li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>AOS.init({ duration: 600, once: true, offset: 80 });</script>
    <script src="js/theme.js"></script>
    </body>
    </html>
<?php mysqli_close($conn); ?>