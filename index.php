<?php
require_once 'config/database.php';

// Последние 4 материала
$latestQuery = "
    SELECT m.*, c.name as category_name, c.slug as category_slug, c.icon as category_icon
    FROM materials m
    JOIN categories c ON m.category_id = c.id
    ORDER BY m.created_at DESC
    LIMIT 4
";
$latestResult = mysqli_query($conn, $latestQuery);
$latestMaterials = [];
while ($row = mysqli_fetch_assoc($latestResult)) {
    $latestMaterials[] = $row;
}

// Количество всего
$totalResult = mysqli_query($conn, "SELECT COUNT(*) as count FROM materials");
$totalRow = mysqli_fetch_assoc($totalResult);
$totalMaterials = $totalRow['count'];

// Фото из жизни (из БД)
$photosResult = mysqli_query($conn, "SELECT * FROM life_photos ORDER BY sort_order ASC");
$lifePhotos = [];
while ($row = mysqli_fetch_assoc($photosResult)) {
    $lifePhotos[] = $row;
}
?>
    <!DOCTYPE html>
    <html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>English Way | Преподаватель английского</title>
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
                <input type="text" name="q" placeholder="Поиск материалов..." value="">
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
                <a href="materials.php">Все материалы</a>
                <a href="materials.php?category=grammar">Грамматика</a>
                <a href="materials.php?category=vocabulary">Лексика</a>
                <a href="materials.php?category=speaking">Разговорный</a>
                <a href="materials.php?category=listening">Аудирование</a>
                <a href="materials.php?category=writing">Письмо</a>
            </div>
        </div>
    </nav>

    <!-- ГЕРО -->
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <div class="hero-text" data-aos="fade-right">
                    <h1 class="slogan">Открой для себя мир английского — шаг за шагом к свободному общению</h1>
                    <p>Индивидуальный подход • Современные методики • Доступные материалы</p>
                    <div class="hero-buttons">
                        <a href="materials.php" class="btn-primary">
                            <i class="fas fa-book-open"></i> Смотреть материалы
                        </a>
                        <a href="teacher.php" class="btn-outline">
                            <i class="fas fa-user"></i> О преподавателе
                        </a>
                    </div>
                </div>
                <div class="hero-images" data-aos="fade-left">
                    <div class="photo-stack">
                        <div class="photo-item photo-1">
                            <img src="image/img1.png" alt="Урок" onerror="this.src='https://picsum.photos/id/20/400/500'">
                        </div>
                        <div class="photo-item photo-2">
                            <img src="image/img2.png" alt="Обучение" onerror="this.src='https://picsum.photos/id/26/400/500'">
                        </div>
                        <div class="photo-item photo-3">
                            <img src="image/img3.png" alt="Клуб" onerror="this.src='https://picsum.photos/id/30/400/500'">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ПОСЛЕДНИЕ МАТЕРИАЛЫ -->
    <section class="latest-materials">
        <div class="container">
            <div class="section-header" data-aos="fade-up">
                <div class="section-header-left">
                    <div class="section-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div>
                        <h2 class="section-title">Последние материалы</h2>
                        <p class="section-subtitle">Недавно добавленные в архив</p>
                    </div>
                </div>
                <a href="materials.php" class="section-link">
                    Все материалы <i class="fas fa-arrow-right"></i>
                </a>
            </div>

            <div class="materials-list-vertical">
                <?php if (count($latestMaterials) > 0): ?>
                    <?php foreach ($latestMaterials as $material): ?>
                        <a href="material.php?id=<?php echo $material['id']; ?>" class="material-row" data-aos="fade-up">
                            <div class="material-row-icon">
                                <i class="fas <?php echo $material['category_icon']; ?>"></i>
                            </div>
                            <div class="material-row-content">
                                <span class="material-row-title"><?php echo htmlspecialchars($material['title']); ?></span>
                                <span class="material-row-meta">
                                    <span class="material-row-category"><?php echo htmlspecialchars($material['category_name']); ?></span>
                                    <span class="material-row-date"><?php echo date('d.m.Y', strtotime($material['created_at'])); ?></span>
                                </span>
                            </div>
                            <?php if ($material['is_new']): ?>
                                <span class="material-row-badge">NEW</span>
                            <?php endif; ?>
                            <div class="material-row-arrow">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="empty-text">Пока нет материалов</p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- ЖИЗНЬ В ТЕХНИКУМЕ (ИЗ БД) -->
    <?php if (count($lifePhotos) > 0): ?>
        <section class="life-section">
            <div class="container">
                <div class="section-header" data-aos="fade-up">
                    <div class="section-header-left">
                        <div class="section-icon">
                            <i class="fas fa-camera"></i>
                        </div>
                        <div>
                            <h2 class="section-title">Жизнь в техникуме</h2>
                            <p class="section-subtitle">Как проходят наши занятия и мероприятия</p>
                        </div>
                    </div>
                </div>

                <div class="life-feed">
                    <?php foreach ($lifePhotos as $index => $photo):
                        // Шахматный порядок: чётные — текст слева/фото справа, нечётные — наоборот
                        $isReversed = ($index % 2 !== 0);
                        ?>
                        <div class="life-feed-item <?php echo $isReversed ? 'life-feed-item--reversed' : ''; ?>" data-aos="fade-up">
                            <div class="life-feed-image">
                                <img src="<?php echo htmlspecialchars($photo['image_path']); ?>"
                                     alt="<?php echo htmlspecialchars($photo['title']); ?>"
                                     onerror="this.src='https://picsum.photos/id/<?php echo 10 + $index; ?>/800/600'">
                                <?php if ($photo['is_large'] == 1): ?>
                                    <div class="life-feed-badge">
                                        <i class="fas fa-star"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="life-feed-text">
                                <span class="life-feed-number">0<?php echo $index + 1; ?></span>
                                <h3><?php echo htmlspecialchars($photo['title']); ?></h3>
                                <p><?php echo htmlspecialchars($photo['description']); ?></p>
                                <div class="life-feed-line"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

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

    <!-- МОДАЛКА ВХОДА В АДМИНКУ -->
    <div class="modal-overlay" id="loginModal" style="display:none;">
        <div class="modal-box">
            <div class="modal-close" onclick="closeLoginModal()">&times;</div>
            <h3>Вход для преподавателя</h3>
            <form method="POST" action="admin/index.php" class="modal-form">
                <input type="password" name="admin_key" placeholder="Введите ключ доступа" required autofocus>
                <button type="submit">Войти</button>
            </form>
        </div>
    </div>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({ duration: 600, once: true, offset: 80 });

        // 5 кликов по логотипу = модалка входа
        let logoClicks = 0;
        const logo = document.querySelector('.logo-link');

        if (logo) {
            logo.addEventListener('click', function(e) {
                e.preventDefault();
                logoClicks++;

                if (logoClicks === 1) {
                    setTimeout(() => { logoClicks = 0; }, 2000);
                }

                if (logoClicks >= 5) {
                    logoClicks = 0;
                    document.getElementById('loginModal').style.display = 'flex';
                }
            });
        }

        function closeLoginModal() {
            document.getElementById('loginModal').style.display = 'none';
        }

        document.getElementById('loginModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeLoginModal();
            }
        });
    </script>
    <script src="js/theme.js"></script>
    </body>
    </html>
<?php mysqli_close($conn); ?>