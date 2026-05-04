<?php
require_once 'config/database.php';

// Получаем категории
$catResult = mysqli_query($conn, "SELECT * FROM categories ORDER BY id");
$categories = [];
while ($row = mysqli_fetch_assoc($catResult)) {
    $categories[] = $row;
}

// Параметры
$filter = isset($_GET['category']) ? $_GET['category'] : 'all';
$search = isset($_GET['search']) ? $_GET['search'] : '';
$filter = mysqli_real_escape_string($conn, $filter);
$search = mysqli_real_escape_string($conn, $search);

// Если выбрана конкретная категория — показываем только её
if ($filter !== 'all') {
    $sql = "
        SELECT m.*, c.name as category_name, c.slug as category_slug, c.icon as category_icon, c.color as category_color
        FROM materials m
        JOIN categories c ON m.category_id = c.id
        WHERE c.slug = '$filter'
    ";
    if (!empty($search)) {
        $sql .= " AND (m.title LIKE '%$search%' OR m.description LIKE '%$search%')";
    }
    $sql .= " ORDER BY m.created_at DESC";

    $materialsResult = mysqli_query($conn, $sql);
    $allMaterials = [];
    while ($row = mysqli_fetch_assoc($materialsResult)) {
        $allMaterials[$row['category_slug']][] = $row;
    }
} else {
    // Все категории
    $sql = "
        SELECT m.*, c.name as category_name, c.slug as category_slug, c.icon as category_icon, c.color as category_color
        FROM materials m
        JOIN categories c ON m.category_id = c.id
        WHERE 1=1
    ";
    if (!empty($search)) {
        $sql .= " AND (m.title LIKE '%$search%' OR m.description LIKE '%$search%')";
    }
    $sql .= " ORDER BY c.id, m.created_at DESC";

    $materialsResult = mysqli_query($conn, $sql);
    $allMaterials = [];
    while ($row = mysqli_fetch_assoc($materialsResult)) {
        $allMaterials[$row['category_slug']][] = $row;
    }
}

// Общее количество
$totalResult = mysqli_query($conn, "SELECT COUNT(*) as count FROM materials");
$totalRow = mysqli_fetch_assoc($totalResult);
$totalMaterials = $totalRow['count'];

// Заголовок
$activeCategoryName = 'Все материалы';
if ($filter !== 'all') {
    foreach ($categories as $cat) {
        if ($cat['slug'] === $filter) {
            $activeCategoryName = $cat['name'];
            break;
        }
    }
}

// Количество найденных
$totalFound = 0;
foreach ($allMaterials as $items) {
    $totalFound += count($items);
}
?>
    <!DOCTYPE html>
    <html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo $activeCategoryName; ?> | English Way</title>
        <link rel="stylesheet" href="css/home.css">
        <link rel="stylesheet" href="css/materials.css">
        <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
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
                <a href="index.php"><i class="fas fa-home"></i> Главная</a>
                <a href="materials.php" class="<?php echo ($filter === 'all' && empty($search)) ? 'active' : ''; ?>">Все</a>
                <?php foreach ($categories as $cat): ?>
                    <a href="materials.php?category=<?php echo $cat['slug']; ?>" class="<?php echo ($filter === $cat['slug']) ? 'active' : ''; ?>">
                        <?php echo htmlspecialchars($cat['name']); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </nav>

    <!-- ХЕДЕР -->
    <section class="page-header">
        <div class="container">
            <div class="page-header-content">
                <div class="page-header-text">
                    <h1><?php echo $activeCategoryName; ?></h1>
                    <p>Найдено: <strong><?php echo $totalFound; ?></strong> из <strong><?php echo $totalMaterials; ?></strong></p>
                </div>
                <div class="page-header-icon">
                    <i class="fas fa-book-open"></i>
                </div>
            </div>
        </div>
    </section>

    <!-- КАТЕГОРИИ (ПОКАЗЫВАЕМ ТОЛЬКО ЕСЛИ НЕТ ФИЛЬТРА) -->
    <?php if ($filter === 'all'): ?>
        <section class="categories-section">
            <div class="container">
                <div class="categories-grid">
                    <?php foreach ($categories as $cat):
                        // Считаем количество в каждой категории
                        $countSql = "SELECT COUNT(*) as cnt FROM materials WHERE category_id = " . intval($cat['id']);
                        $countResult = mysqli_query($conn, $countSql);
                        $countRow = mysqli_fetch_assoc($countResult);
                        $count = $countRow['cnt'];
                        ?>
                        <a href="materials.php?category=<?php echo $cat['slug']; ?>" class="category-card">
                            <div class="category-card-icon">
                                <i class="fas <?php echo $cat['icon']; ?>"></i>
                            </div>
                            <div class="category-card-info">
                                <span class="category-card-name"><?php echo htmlspecialchars($cat['name']); ?></span>
                                <span class="category-card-count"><?php echo $count; ?> материалов</span>
                            </div>
                            <i class="fas fa-arrow-right category-card-arrow"></i>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- МАТЕРИАЛЫ ПО КАТЕГОРИЯМ -->
    <section class="materials-section">
        <div class="container">
            <?php if ($totalFound > 0): ?>
                <?php foreach ($allMaterials as $catSlug => $materials): ?>
                    <?php
                    // Находим данные категории
                    $catName = '';
                    $catIcon = 'fa-book';
                    foreach ($categories as $cat) {
                        if ($cat['slug'] === $catSlug) {
                            $catName = $cat['name'];
                            $catIcon = $cat['icon'];
                            break;
                        }
                    }
                    ?>
                    <div class="materials-group" data-aos="fade-up">
                        <!-- Заголовок группы -->
                        <div class="group-header">
                            <div class="group-header-left">
                                <div class="group-icon">
                                    <i class="fas <?php echo $catIcon; ?>"></i>
                                </div>
                                <h2 class="group-title"><?php echo htmlspecialchars($catName); ?></h2>
                                <span class="group-count"><?php echo count($materials); ?></span>
                            </div>
                            <?php if ($filter === 'all'): ?>
                                <a href="materials.php?category=<?php echo $catSlug; ?>" class="group-link">
                                    Смотреть все <i class="fas fa-arrow-right"></i>
                                </a>
                            <?php endif; ?>
                        </div>

                        <!-- Список материалов -->
                        <div class="materials-list">
                            <?php foreach ($materials as $material): ?>
                                <a href="material.php?id=<?php echo $material['id']; ?>" class="material-item">
                                    <div class="material-item-icon">
                                        <i class="fas fa-file-alt"></i>
                                    </div>
                                    <div class="material-item-content">
                                        <span class="material-item-title"><?php echo htmlspecialchars($material['title']); ?></span>
                                        <span class="material-item-desc">
                                            <?php
                                            $desc = $material['description'] ?? '';
                                            echo htmlspecialchars(mb_substr($desc, 0, 80));
                                            if (mb_strlen($desc) > 80) echo '...';
                                            ?>
                                        </span>
                                    </div>
                                    <?php if ($material['is_new']): ?>
                                        <span class="material-item-badge">NEW</span>
                                    <?php endif; ?>
                                    <div class="material-item-arrow">
                                        <i class="fas fa-chevron-right"></i>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-results">
                    <div class="no-results-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <h3>Ничего не найдено</h3>
                    <p>По вашему запросу материалов нет</p>
                    <a href="materials.php" class="btn-primary">Сбросить всё</a>
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
    <script src="js/materials.js"></script>
    <script src="js/theme.js"></script>
    </body>
    </html>
<?php mysqli_close($conn); ?>