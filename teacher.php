<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>О преподавателе | English Way</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="css/home.css">
    <link rel="stylesheet" href="css/teacher.css">
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
            <a href="teacher.php" class="active">О преподавателе</a>
            <a href="tests.php">Тесты</a>
            <a href="404.php">Контакты</a>
        </div>
        <div class="search-area" onclick="window.location.href='materials.php'">
            <i class="fas fa-search"></i>
            <span>Поиск материалов</span>
        </div>
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
            <a href="materials.php?category=grammar">Грамматика</a>
            <a href="materials.php?category=vocabulary">Лексика</a>
            <a href="materials.php?category=speaking">Разговорный</a>
        </div>
    </div>
</nav>

<!-- ХЕДЕР -->
<section class="page-header">
    <div class="container">
        <div class="page-header-content">
            <div class="page-header-text" data-aos="fade-right">
                <h1>О преподавателе</h1>
                <p>Профессионал с многолетним опытом и любовью к английскому языку</p>
            </div>
            <div class="page-header-icon" data-aos="fade-left">
                <i class="fas fa-chalkboard-teacher"></i>
            </div>
        </div>
    </div>
</section>

<!-- ОСНОВНОЙ БЛОК -->
<section class="teacher-main">
    <div class="container">
        <div class="teacher-profile">
            <!-- Фото -->
            <div class="teacher-photo-wrapper" data-aos="fade-up">
                <div class="teacher-photo">
                    <img src="image/teacher1.jpg" alt="Преподаватель" onerror="this.src='https://via.placeholder.com/400x500/f1f5f9/64748b?text=Фото'">
                </div>
                <div class="teacher-photo-badge">
                    <i class="fas fa-star"></i> 10 лет опыта
                </div>
            </div>

            <!-- Инфо -->
            <div class="teacher-info" data-aos="fade-up" data-aos-delay="100">
                <h2>Ильдика Сергеевна</h2>
                <span class="teacher-title">Преподаватель английского языка</span>

                <p class="teacher-bio">
                    Здравствуйте! Я преподаю английский язык уже более 10 лет. За это время помогла более
                    500 ученикам заговорить на английском свободно и уверенно. Работаю как с начинающими,
                    так и с продвинутыми студентами.
                </p>
                <p class="teacher-bio">
                    Моя методика основана на индивидуальном подходе к каждому ученику. Я использую
                    современные материалы, интерактивные задания и реальные жизненные ситуации, чтобы
                    сделать обучение максимально эффективным и интересным.
                </p>

                <!-- Детали -->
                <div class="teacher-details">
                    <div class="detail-item">
                        <div class="detail-icon">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <div class="detail-content">
                            <span class="detail-label">Образование</span>
                            <span class="detail-value">МГЛУ, лингвист-переводчик</span>
                        </div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-icon">
                            <i class="fas fa-certificate"></i>
                        </div>
                        <div class="detail-content">
                            <span class="detail-label">Сертификаты</span>
                            <span class="detail-value">CELTA, IELTS 8.5</span>
                        </div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="detail-content">
                            <span class="detail-label">Опыт работы</span>
                            <span class="detail-value">10+ лет преподавания</span>
                        </div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="detail-content">
                            <span class="detail-label">Учеников</span>
                            <span class="detail-value">Более 500 студентов</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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