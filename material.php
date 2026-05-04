<?php
require_once 'config/database.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    header('Location: materials.php');
    exit;
}

// Получаем материал из БД
$result = mysqli_query($conn, "
    SELECT m.*, c.name as category_name, c.slug as category_slug, c.icon as category_icon, c.color as category_color
    FROM materials m
    JOIN categories c ON m.category_id = c.id
    WHERE m.id = $id
");

if (!$result || mysqli_num_rows($result) === 0) {
    header('Location: materials.php');
    exit;
}

$material = mysqli_fetch_assoc($result);

// Увеличиваем счётчик просмотров
mysqli_query($conn, "UPDATE materials SET views = views + 1 WHERE id = $id");

// Определяем иконку типа файла
$fileIcon = 'fa-file';
$fileTypeName = 'Файл';
if (!empty($material['file_path'])) {
    $ext = strtolower(pathinfo($material['file_path'], PATHINFO_EXTENSION));
    switch ($ext) {
        case 'pdf': $fileIcon = 'fa-file-pdf'; $fileTypeName = 'PDF'; break;
        case 'doc': case 'docx': $fileIcon = 'fa-file-word'; $fileTypeName = 'Word'; break;
        case 'xls': case 'xlsx': $fileIcon = 'fa-file-excel'; $fileTypeName = 'Excel'; break;
        case 'ppt': case 'pptx': $fileIcon = 'fa-file-powerpoint'; $fileTypeName = 'PowerPoint'; break;
        case 'jpg': case 'jpeg': case 'png': case 'gif': $fileIcon = 'fa-file-image'; $fileTypeName = 'Изображение'; break;
        case 'zip': case 'rar': $fileIcon = 'fa-file-archive'; $fileTypeName = 'Архив'; break;
        case 'mp3': case 'wav': $fileIcon = 'fa-file-audio'; $fileTypeName = 'Аудио'; break;
        case 'mp4': case 'avi': $fileIcon = 'fa-file-video'; $fileTypeName = 'Видео'; break;
    }
}

// Форматируем размер файла
$fileSize = '';
if (!empty($material['file_path']) && file_exists($material['file_path'])) {
    $size = filesize($material['file_path']);
    if ($size < 1024) {
        $fileSize = $size . ' Б';
    } elseif ($size < 1048576) {
        $fileSize = round($size / 1024, 1) . ' КБ';
    } else {
        $fileSize = round($size / 1048576, 1) . ' МБ';
    }
}

// Другие материалы из той же категории
$relatedResult = mysqli_query($conn, "
    SELECT m.id, m.title, m.is_new, c.icon as category_icon
    FROM materials m
    JOIN categories c ON m.category_id = c.id
    WHERE m.category_id = {$material['category_id']} AND m.id != $id
    ORDER BY m.created_at DESC
    LIMIT 3
");
$relatedMaterials = [];
while ($row = mysqli_fetch_assoc($relatedResult)) {
    $relatedMaterials[] = $row;
}
?>
    <!DOCTYPE html>
    <html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo htmlspecialchars($material['title']); ?> | English Way</title>
        <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
        <link rel="stylesheet" href="css/home.css">
        <link rel="stylesheet" href="css/material.css">
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
            <div class="search-area" onclick="window.location.href='materials.php'">
                <i class="fas fa-search"></i>
                <span>К материалам</span>
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

    <!-- ХЛЕБНЫЕ КРОШКИ -->
    <div class="breadcrumbs">
        <div class="container">
            <a href="index.php">Главная</a>
            <i class="fas fa-chevron-right"></i>
            <a href="materials.php">Материалы</a>
            <i class="fas fa-chevron-right"></i>
            <a href="materials.php?category=<?php echo $material['category_slug']; ?>"><?php echo htmlspecialchars($material['category_name']); ?></a>
            <i class="fas fa-chevron-right"></i>
            <span><?php echo htmlspecialchars($material['title']); ?></span>
        </div>
    </div>

    <!-- ОСНОВНОЙ КОНТЕНТ -->
    <section class="material-detail">
        <div class="container">
            <div class="material-layout">

                <!-- ЛЕВАЯ КОЛОНКА (контент) -->
                <div class="material-main" data-aos="fade-up">

                    <!-- Заголовок -->
                    <div class="material-header">
                        <div class="material-header-top">
                            <a href="materials.php?category=<?php echo $material['category_slug']; ?>" class="material-category-badge" style="background: <?php echo $material['category_color']; ?>15; color: <?php echo $material['category_color']; ?>;">
                                <i class="fas <?php echo $material['category_icon']; ?>"></i>
                                <?php echo htmlspecialchars($material['category_name']); ?>
                            </a>
                            <?php if ($material['is_new']): ?>
                                <span class="material-new-badge">NEW</span>
                            <?php endif; ?>
                        </div>
                        <h1><?php echo htmlspecialchars($material['title']); ?></h1>
                        <div class="material-meta">
                            <span><i class="fas fa-signal"></i> <?php echo $material['level']; ?></span>
                            <span><i class="far fa-calendar-alt"></i> <?php echo date('d.m.Y', strtotime($material['created_at'])); ?></span>
                            <span><i class="far fa-eye"></i> <?php echo $material['views'] + 1; ?> просмотров</span>
                        </div>
                    </div>

                    <!-- Описание -->
                    <?php if (!empty($material['description'])): ?>
                        <div class="material-description">
                            <p><?php echo nl2br(htmlspecialchars($material['description'])); ?></p>
                        </div>
                    <?php endif; ?>

                    <!-- Содержание -->
                    <?php if (!empty($material['content'])): ?>
                        <div class="material-content">
                            <?php echo $material['content']; ?>
                        </div>
                    <?php endif; ?>

                    <!-- БЛОК СКАЧИВАНИЯ -->
                    <?php if (!empty($material['file_path']) && file_exists($material['file_path'])): ?>
                        <div class="download-block">
                            <div class="download-card">
                                <div class="download-icon">
                                    <i class="fas <?php echo $fileIcon; ?>"></i>
                                </div>
                                <div class="download-info">
                                    <span class="download-title">Прикреплённый файл</span>
                                    <span class="download-meta">
                                        <?php echo $fileTypeName; ?>
                                        <?php if ($fileSize): ?>
                                            • <?php echo $fileSize; ?>
                                        <?php endif; ?>
                                    </span>
                                </div>
                                <a href="<?php echo $material['file_path']; ?>" class="btn-download" download>
                                    <i class="fas fa-download"></i> Скачать
                                </a>
                            </div>
                        </div>
                    <?php elseif (!empty($material['file_path'])): ?>
                        <div class="download-block">
                            <div class="download-card download-card--missing">
                                <div class="download-icon">
                                    <i class="fas fa-exclamation-triangle"></i>
                                </div>
                                <div class="download-info">
                                    <span class="download-title">Файл не найден</span>
                                    <span class="download-meta">Файл был удалён или перемещён</span>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Кнопка назад -->
                    <div class="material-back">
                        <a href="materials.php" class="btn-back">
                            <i class="fas fa-arrow-left"></i> Ко всем материалам
                        </a>
                        <a href="materials.php?category=<?php echo $material['category_slug']; ?>" class="btn-back btn-back--outline">
                            <i class="fas <?php echo $material['category_icon']; ?>"></i> <?php echo htmlspecialchars($material['category_name']); ?>
                        </a>
                    </div>
                </div>

                <!-- ПРАВАЯ КОЛОНКА (сайдбар) -->
                <div class="material-sidebar" data-aos="fade-up" data-aos-delay="100">

                    <!-- Инфо о материале -->
                    <div class="sidebar-card">
                        <h3>Информация</h3>
                        <ul class="sidebar-info">
                            <li>
                                <i class="fas fa-layer-group"></i>
                                <div>
                                    <span>Категория</span>
                                    <strong><?php echo htmlspecialchars($material['category_name']); ?></strong>
                                </div>
                            </li>
                            <li>
                                <i class="fas fa-signal"></i>
                                <div>
                                    <span>Уровень</span>
                                    <strong><?php echo $material['level']; ?></strong>
                                </div>
                            </li>
                            <li>
                                <i class="far fa-calendar-alt"></i>
                                <div>
                                    <span>Добавлено</span>
                                    <strong><?php echo date('d.m.Y', strtotime($material['created_at'])); ?></strong>
                                </div>
                            </li>
                            <?php if (!empty($material['file_path'])): ?>
                                <li>
                                    <i class="fas fa-paperclip"></i>
                                    <div>
                                        <span>Файл</span>
                                        <strong><?php echo $fileTypeName; ?></strong>
                                    </div>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>

                    <!-- Кнопка скачать в сайдбаре -->
                    <?php if (!empty($material['file_path']) && file_exists($material['file_path'])): ?>
                        <a href="<?php echo $material['file_path']; ?>" class="btn-download-sidebar" download>
                            <i class="fas fa-download"></i> Скачать материал
                        </a>
                    <?php endif; ?>

                    <!-- Похожие материалы -->
                    <?php if (count($relatedMaterials) > 0): ?>
                        <div class="sidebar-card">
                            <h3>Похожие материалы</h3>
                            <div class="related-list">
                                <?php foreach ($relatedMaterials as $rel): ?>
                                    <a href="material.php?id=<?php echo $rel['id']; ?>" class="related-item">
                                        <div class="related-item-icon">
                                            <i class="fas <?php echo $rel['category_icon']; ?>"></i>
                                        </div>
                                        <div class="related-item-content">
                                            <span><?php echo htmlspecialchars($rel['title']); ?></span>
                                        </div>
                                        <?php if ($rel['is_new']): ?>
                                            <span class="related-new">NEW</span>
                                        <?php endif; ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
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
<?php mysqli_close($conn); ?>