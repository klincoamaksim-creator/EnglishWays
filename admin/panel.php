<?php
session_start();
if (!isset($_SESSION['admin_logged']) || $_SESSION['admin_logged'] !== true) {
    header('Location: index.php');
    exit;
}
require_once '../config/database.php';

// Статистика
$materialsCount = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as cnt FROM materials"))['cnt'];
$photosCount = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as cnt FROM life_photos"))['cnt'];
$categoriesCount = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as cnt FROM categories"))['cnt'];

// Последние материалы
$latestResult = mysqli_query($conn, "SELECT * FROM materials ORDER BY created_at DESC LIMIT 5");
$latestMaterials = [];
while ($row = mysqli_fetch_assoc($latestResult)) {
    $latestMaterials[] = $row;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Панель управления | English Way</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="/css/home.css">
</head>
<body class="admin-body">

<!-- САЙДБАР -->

<aside class="sidebar">
    <a href="index.php" class="logo-link">
        <img src="/image/logo.png" alt="Logo" onerror="this.src='https://via.placeholder.com/45'">
        <span class="logo-text">English Way</span>
    </a>
    <nav class="sidebar-nav">
        <a href="panel.php" class="sidebar-link active">
            <i class="fas fa-th-large"></i> Панель
        </a>
        <a href="materials.php" class="sidebar-link">
            <i class="fas fa-book-open"></i> Материалы
        </a>
        <a href="tests.php" class="sidebar-link">
            <i class="fas fa-clipboard-list"></i> Тесты
        </a>
        <a href="photos.php" class="sidebar-link">
            <i class="fas fa-images"></i> Фото жизни
        </a>
        <a href="../index.php" class="sidebar-link" target="_blank">
            <i class="fas fa-external-link-alt"></i> Сайт
        </a>
    </nav>
    <div class="sidebar-footer">
        <a href="logout.php" class="sidebar-link logout-link">
            <i class="fas fa-sign-out-alt"></i> Выйти
        </a>
    </div>
</aside>

<!-- ОСНОВНОЙ КОНТЕНТ -->
<main class="main-content">
    <header class="content-header">
        <h1>Панель управления</h1>
        <p>Добро пожаловать, преподаватель</p>
    </header>

    <!-- КАРТОЧКИ СТАТИСТИКИ -->
    <div class="stats-cards">
        <div class="stat-card">
            <div class="stat-card-icon" style="background:#fef2f2; color:#ef4444;">
                <i class="fas fa-book-open"></i>
            </div>
            <div class="stat-card-info">
                <span class="stat-card-number"><?php echo $materialsCount; ?></span>
                <span class="stat-card-label">Материалов</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-card-icon" style="background:#eff6ff; color:#2563eb;">
                <i class="fas fa-images"></i>
            </div>
            <div class="stat-card-info">
                <span class="stat-card-number"><?php echo $photosCount; ?></span>
                <span class="stat-card-label">Фото</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-card-icon" style="background:#f0fdf4; color:#16a34a;">
                <i class="fas fa-layer-group"></i>
            </div>
            <div class="stat-card-info">
                <span class="stat-card-number"><?php echo $categoriesCount; ?></span>
                <span class="stat-card-label">Категорий</span>
            </div>
        </div>
    </div>

    <!-- БЫСТРЫЕ ДЕЙСТВИЯ -->
    <div class="quick-actions">
        <h2>Быстрые действия</h2>
        <div class="actions-grid">
            <a href="materials.php?action=add" class="action-card">
                <i class="fas fa-plus-circle"></i>
                <span>Добавить материал</span>
            </a>
            <a href="photos.php?action=add" class="action-card">
                <i class="fas fa-camera"></i>
                <span>Добавить фото</span>
            </a>
            <a href="materials.php" class="action-card">
                <i class="fas fa-list"></i>
                <span>Все материалы</span>
            </a>
            <a href="photos.php" class="action-card">
                <i class="fas fa-images"></i>
                <span>Все фото</span>
            </a>
        </div>
    </div>

    <!-- ПОСЛЕДНИЕ МАТЕРИАЛЫ -->
    <div class="recent-materials">
        <h2>Последние добавленные</h2>
        <div class="table-wrapper">
            <table class="admin-table">
                <thead>
                <tr>
                    <th>Название</th>
                    <th>Категория</th>
                    <th>Дата</th>
                    <th>Действия</th>
                </tr>
                </thead>
                <tbody>
                <?php if (count($latestMaterials) > 0): ?>
                    <?php foreach ($latestMaterials as $item): ?>
                        <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($item['title']); ?></strong>
                                <?php if ($item['is_new']): ?>
                                    <span class="badge-new">NEW</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($item['level']); ?></td>
                            <td><?php echo date('d.m.Y H:i', strtotime($item['created_at'])); ?></td>
                            <td class="actions-cell">
                                <a href="materials.php?action=edit&id=<?php echo $item['id']; ?>" class="btn-icon" title="Редактировать">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="materials.php?action=delete&id=<?php echo $item['id']; ?>" class="btn-icon btn-delete" title="Удалить" onclick="return confirm('Удалить материал?')">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="empty-cell">Нет материалов</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

</body>
</html>