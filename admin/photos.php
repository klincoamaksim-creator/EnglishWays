<?php
session_start();
if (!isset($_SESSION['admin_logged']) || $_SESSION['admin_logged'] !== true) {
    header('Location: index.php');
    exit;
}
require_once '../config/database.php';

$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$message = '';

// УДАЛЕНИЕ
if ($action === 'delete' && $id > 0) {
    $fileResult = mysqli_query($conn, "SELECT image_path FROM life_photos WHERE id = $id");
    $fileRow = mysqli_fetch_assoc($fileResult);
    if ($fileRow && file_exists('../' . $fileRow['image_path'])) {
        unlink('../' . $fileRow['image_path']);
    }
    mysqli_query($conn, "DELETE FROM life_photos WHERE id = $id");
    header('Location: photos.php?deleted=1');
    exit;
}

// СОХРАНЕНИЕ
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $is_large = isset($_POST['is_large']) ? 1 : 0;
    $sort_order = intval($_POST['sort_order']);
    $editId = isset($_POST['edit_id']) ? intval($_POST['edit_id']) : 0;

    $image_path = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/life/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        $fileName = time() . '_' . basename($_FILES['image']['name']);
        $image_path = 'uploads/life/' . $fileName;
        move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $fileName);
    }

    if ($editId > 0) {
        if ($image_path) {
            $oldFile = mysqli_fetch_assoc(mysqli_query($conn, "SELECT image_path FROM life_photos WHERE id = $editId"));
            if ($oldFile['image_path'] && file_exists('../' . $oldFile['image_path'])) {
                unlink('../' . $oldFile['image_path']);
            }
            mysqli_query($conn, "UPDATE life_photos SET title='$title', description='$description', is_large=$is_large, sort_order=$sort_order, image_path='$image_path' WHERE id=$editId");
        } else {
            mysqli_query($conn, "UPDATE life_photos SET title='$title', description='$description', is_large=$is_large, sort_order=$sort_order WHERE id=$editId");
        }
        $message = 'Фото обновлено!';
    } else {
        if ($image_path) {
            mysqli_query($conn, "INSERT INTO life_photos (title, description, image_path, is_large, sort_order) VALUES ('$title', '$description', '$image_path', $is_large, $sort_order)");
            $message = 'Фото добавлено!';
        } else {
            $message = 'Выберите изображение!';
        }
    }
}

// Фото для редактирования
$editPhoto = null;
if ($action === 'edit' && $id > 0) {
    $editResult = mysqli_query($conn, "SELECT * FROM life_photos WHERE id = $id");
    $editPhoto = mysqli_fetch_assoc($editResult);
}

// Все фото
$photosResult = mysqli_query($conn, "SELECT * FROM life_photos ORDER BY sort_order ASC");
$allPhotos = [];
while ($row = mysqli_fetch_assoc($photosResult)) {
    $allPhotos[] = $row;
}

if (isset($_GET['deleted'])) $message = 'Фото удалено!';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Фото жизни | Админка English Way</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="/css/home.css">
</head>
<body class="admin-body">

<!-- САЙДБАР -->
<!-- САЙДБАР -->
<aside class="sidebar">
    <a href="index.php" class="logo-link">
        <img src="/image/logo.png" alt="Logo" onerror="this.src='https://via.placeholder.com/45'">
        <span class="logo-text">English Way</span>
    </a>
    <nav class="sidebar-nav">
        <a href="panel.php" class="sidebar-link">
            <i class="fas fa-th-large"></i> Панель
        </a>
        <a href="materials.php" class="sidebar-link">
            <i class="fas fa-book-open"></i> Материалы
        </a>
        <a href="tests.php" class="sidebar-link">
            <i class="fas fa-clipboard-list"></i> Тесты
        </a>
        <a href="photos.php" class="sidebar-link active">
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
        <h1>Фото из жизни</h1>
        <div class="header-actions">
            <a href="photos.php?action=add" class="btn-add">
                <i class="fas fa-plus"></i> Добавить фото
            </a>
        </div>
    </header>

    <?php if ($message): ?>
        <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo $message; ?></div>
    <?php endif; ?>

    <!-- ФОРМА -->
    <?php if ($action === 'add' || $action === 'edit'): ?>
        <div class="form-container">
            <h2><?php echo $editPhoto ? 'Редактировать' : 'Добавить'; ?> фото</h2>
            <form method="POST" enctype="multipart/form-data" class="admin-form">
                <?php if ($editPhoto): ?>
                    <input type="hidden" name="edit_id" value="<?php echo $editPhoto['id']; ?>">
                <?php endif; ?>

                <div class="form-group">
                    <label>Название *</label>
                    <input type="text" name="title" value="<?php echo $editPhoto ? htmlspecialchars($editPhoto['title']) : ''; ?>" required>
                </div>

                <div class="form-group">
                    <label>Описание</label>
                    <textarea name="description" rows="3"><?php echo $editPhoto ? htmlspecialchars($editPhoto['description']) : ''; ?></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Изображение <?php echo $editPhoto ? '' : '*'; ?></label>
                        <input type="file" name="image" accept="image/*" <?php echo $editPhoto ? '' : 'required'; ?>>
                        <?php if ($editPhoto): ?>
                            <img src="../<?php echo $editPhoto['image_path']; ?>" class="preview-img">
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label>Порядок сортировки</label>
                        <input type="number" name="sort_order" value="<?php echo $editPhoto ? $editPhoto['sort_order'] : '0'; ?>">
                    </div>
                </div>

                <div class="form-group checkbox-group">
                    <label>
                        <input type="checkbox" name="is_large" <?php echo ($editPhoto && $editPhoto['is_large']) ? 'checked' : ''; ?>>
                        Большая карточка (на всю ширину)
                    </label>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-save"><i class="fas fa-save"></i> Сохранить</button>
                    <a href="photos.php" class="btn-cancel">Отмена</a>
                </div>
            </form>
        </div>
    <?php endif; ?>

    <!-- СЕТКА ФОТО -->
    <?php if ($action === 'list'): ?>
        <div class="photos-grid">
            <?php if (count($allPhotos) > 0): ?>
                <?php foreach ($allPhotos as $photo): ?>
                    <div class="photo-card <?php echo $photo['is_large'] ? 'photo-card-large' : ''; ?>">
                        <img src="../<?php echo $photo['image_path']; ?>" alt="<?php echo htmlspecialchars($photo['title']); ?>" onerror="this.src='https://via.placeholder.com/400x300/f1f5f9/64748b?text=Нет+фото'">
                        <div class="photo-card-info">
                            <h4><?php echo htmlspecialchars($photo['title']); ?></h4>
                            <p><?php echo htmlspecialchars(mb_substr($photo['description'], 0, 60)); ?></p>
                        </div>
                        <div class="photo-card-actions">
                            <a href="photos.php?action=edit&id=<?php echo $photo['id']; ?>" class="btn-icon"><i class="fas fa-edit"></i></a>
                            <a href="photos.php?action=delete&id=<?php echo $photo['id']; ?>" class="btn-icon btn-delete" onclick="return confirm('Удалить фото?')"><i class="fas fa-trash-alt"></i></a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="empty-text">Нет фото. <a href="photos.php?action=add">Добавить первое</a></p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</main>
</body>
</html>