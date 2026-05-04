<?php
require_once 'config/database.php';

// Получаем категории тестов с количеством
$catSql = "
    SELECT tc.*, COUNT(t.id) as test_count
    FROM test_categories tc
    LEFT JOIN tests t ON tc.id = t.category_id
    GROUP BY tc.id
    ORDER BY tc.sort_order ASC
";
$catResult = mysqli_query($conn, $catSql);
$categories = [];
while ($row = mysqli_fetch_assoc($catResult)) {
    $categories[] = $row;
}

// Получаем тесты, сгруппированные по категориям
$testsByCategory = [];
$testsSql = "
    SELECT t.*, tc.name as category_name, tc.slug as category_slug, tc.icon as category_icon
    FROM tests t
    JOIN test_categories tc ON t.category_id = tc.id
    ORDER BY tc.sort_order ASC, t.created_at DESC
";
$testsResult = mysqli_query($conn, $testsSql);
while ($row = mysqli_fetch_assoc($testsResult)) {
    $testsByCategory[$row['category_slug']][] = $row;
}

// Общее количество тестов
$totalTests = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as cnt FROM tests"))['cnt'];
?>
    <!DOCTYPE html>
    <html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Тесты и задания | English Way</title>
        <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
        <link rel="stylesheet" href="css/home.css">
        <link rel="stylesheet" href="css/theme.css">
        <link rel="stylesheet" href="css/search.css">

        <link rel="stylesheet" href="css/tests.css">
    </head>
    <body>

    <!-- ВЕРХНЯЯ НАВИГАЦИЯ -->
    <nav class="top-nav">
        <div class="container">
            <a href="index.php" class="logo-link">
                <img src="image/logo.png" alt="Logo" onerror="this.src='https://via.placeholder.com/45'">
                <span class="logo-text">English Way</span>
            </a>
            <div class="nav-links-left">
                <a href="teacher.php">О преподавателе</a>
                <a href="tests.php" class="active">Тесты</a>
                <a href="404.php">Успехи</a>
                <a href="404.php">Контакты</a>
            </div>
            <div class="nav-actions">
                <form action="search.php" method="GET" class="search-form-top">
                    <button type="submit" class="search-submit-btn"><i class="fas fa-search"></i></button>
                    <input type="text" name="q" placeholder="Поиск...">
                </form>
                <button class="theme-toggle" id="themeToggle" title="Сменить тему"><i class="fas fa-moon"></i></button>
            </div>
        </div>
    </nav>

    <!-- ВТОРАЯ НАВИГАЦИЯ -->
    <nav class="second-nav">
        <div class="container">
            <div class="nav-secondary">
                <a href="index.php"><i class="fas fa-home"></i> Главная</a>
                <a href="materials.php">Все материалы</a>
                <a href="materials.php?category=grammar">Грамматика</a>
                <a href="materials.php?category=vocabulary">Лексика</a>
                <a href="materials.php?category=speaking">Разговорный</a>
            </div>
        </div>
    </nav>

    <!-- ХЕДЕР -->
    <section class="page-header">
        <div class="container">
            <div class="page-header-content" data-aos="fade-up">
                <div class="page-header-text">
                    <h1>Тесты и задания</h1>
                    <p>Всего доступно: <strong><?php echo $totalTests; ?></strong> тестов в <strong><?php echo count($categories); ?></strong> категориях</p>
                </div>
                <div class="page-header-icon">
                    <i class="fas fa-clipboard-list"></i>
                </div>
            </div>
        </div>
    </section>

    <!-- КАРТОЧКИ КАТЕГОРИЙ -->
    <section class="tests-categories">
        <div class="container">
            <div class="categories-grid">
                <?php foreach ($categories as $cat): ?>
                    <a href="#<?php echo $cat['slug']; ?>" class="test-category-card" data-aos="fade-up">
                        <div class="test-category-icon">
                            <i class="fas <?php echo $cat['icon']; ?>"></i>
                        </div>
                        <div class="test-category-info">
                            <span class="test-category-name"><?php echo htmlspecialchars($cat['name']); ?></span>
                            <span class="test-category-count"><?php echo $cat['test_count']; ?> тестов</span>
                        </div>
                        <i class="fas fa-arrow-down test-category-arrow"></i>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ТЕСТЫ ПО КАТЕГОРИЯМ -->
    <section class="tests-content">
        <div class="container">
            <?php if ($totalTests > 0): ?>
                <?php foreach ($testsByCategory as $catSlug => $tests): ?>
                    <?php
                    $catInfo = null;
                    foreach ($categories as $cat) {
                        if ($cat['slug'] === $catSlug) {
                            $catInfo = $cat;
                            break;
                        }
                    }
                    if (!$catInfo) continue;
                    ?>
                    <div class="test-section" id="<?php echo $catSlug; ?>" data-aos="fade-up">
                        <div class="test-section-header">
                            <div class="test-section-header-left">
                                <div class="test-section-icon">
                                    <i class="fas <?php echo $catInfo['icon']; ?>"></i>
                                </div>
                                <div>
                                    <h2><?php echo htmlspecialchars($catInfo['name']); ?></h2>
                                    <span class="test-section-count"><?php echo count($tests); ?> тестов</span>
                                </div>
                            </div>
                        </div>

                        <div class="test-items">
                            <?php foreach ($tests as $test): ?>
                                <div class="test-item">
                                    <div class="test-item-main">
                                        <div class="test-item-icon">
                                            <i class="fas fa-file-alt"></i>
                                        </div>
                                        <div class="test-item-body">
                                            <div class="test-item-top">
                                                <h3><?php echo htmlspecialchars($test['title']); ?></h3>
                                                <?php if ($test['is_new']): ?>
                                                    <span class="test-badge-new">NEW</span>
                                                <?php endif; ?>
                                            </div>
                                            <?php if (!empty($test['description'])): ?>
                                                <p><?php echo htmlspecialchars($test['description']); ?></p>
                                            <?php endif; ?>
                                            <div class="test-item-meta">
                                                <?php if (!empty($test['questions_count'])): ?>
                                                    <span><i class="fas fa-question-circle"></i> <?php echo $test['questions_count']; ?> вопросов</span>
                                                <?php endif; ?>
                                                <?php if (!empty($test['time_limit'])): ?>
                                                    <span><i class="fas fa-clock"></i> <?php echo $test['time_limit']; ?> мин</span>
                                                <?php endif; ?>
                                                <span><i class="far fa-calendar-alt"></i> <?php echo date('d.m.Y', strtotime($test['created_at'])); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="test-item-actions">
                                        <?php if (!empty($test['file_path'])): ?>
                                            <a href="<?php echo htmlspecialchars($test['file_path']); ?>" class="btn-download-test" download>
                                                <i class="fas fa-download"></i> Скачать
                                            </a>
                                        <?php endif; ?>
                                        <a href="material.php?id=<?php echo $test['id']; ?>" class="btn-view-test">
                                            <i class="fas fa-eye"></i> Подробнее
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state" data-aos="fade-up">
                    <i class="fas fa-clipboard-list"></i>
                    <h3>Тестов пока нет</h3>
                    <p>Преподаватель скоро добавит тесты и задания</p>
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
                        <li><a href="tests.php">Тесты и задания</a></li>
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
    <script src="js/theme.js"></script>
    <script>
        AOS.init({ duration: 600, once: true, offset: 80 });

        // Плавный скролл к категориям
        document.querySelectorAll('.test-category-card').forEach(card => {
            card.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });
    </script>
    </body>
    </html>
<?php mysqli_close($conn); ?>