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
$error = '';

// УДАЛЕНИЕ
if ($action === 'delete' && $id > 0) {
    // Удаляем файл если есть
    $fileResult = mysqli_query($conn, "SELECT file_path FROM materials WHERE id = $id");
    $fileRow = mysqli_fetch_assoc($fileResult);
    if ($fileRow && $fileRow['file_path'] && file_exists('../' . $fileRow['file_path'])) {
        unlink('../' . $fileRow['file_path']);
    }
    mysqli_query($conn, "DELETE FROM materials WHERE id = $id");
    header('Location: materials.php?deleted=1');
    exit;
}

// СОХРАНЕНИЕ (ДОБАВЛЕНИЕ / РЕДАКТИРОВАНИЕ)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $category_id = intval($_POST['category_id']);
    $level = mysqli_real_escape_string($conn, $_POST['level']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $content = mysqli_real_escape_string($conn, $_POST['content']);
    $is_new = isset($_POST['is_new']) ? 1 : 0;
    $editId = isset($_POST['edit_id']) ? intval($_POST['edit_id']) : 0;

    // Загрузка файла
    $file_path = '';
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/materials/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $fileName = time() . '_' . basename($_FILES['file']['name']);
        $file_path = 'uploads/materials/' . $fileName;
        move_uploaded_file($_FILES['file']['tmp_name'], $uploadDir . $fileName);
    }

    if ($editId > 0) {
        // Обновление
        if ($file_path) {
            // Удаляем старый файл
            $oldFile = mysqli_fetch_assoc(mysqli_query($conn, "SELECT file_path FROM materials WHERE id = $editId"));
            if ($oldFile['file_path'] && file_exists('../' . $oldFile['file_path'])) {
                unlink('../' . $oldFile['file_path']);
            }
            mysqli_query($conn, "UPDATE materials SET title='$title', category_id=$category_id, level='$level', description='$description', content='$content', is_new=$is_new, file_path='$file_path' WHERE id=$editId");
        } else {
            mysqli_query($conn, "UPDATE materials SET title='$title', category_id=$category_id, level='$level', description='$description', content='$content', is_new=$is_new WHERE id=$editId");
        }
        $message = 'Материал обновлён!';
    } else {
        // Добавление
        mysqli_query($conn, "INSERT INTO materials (title, category_id, level, description, content, is_new, file_path) VALUES ('$title', $category_id, '$level', '$description', '$content', $is_new, '$file_path')");
        $message = 'Материал добавлен!';
    }
}

// Получаем материал для редактирования
$editMaterial = null;
if ($action === 'edit' && $id > 0) {
    $editResult = mysqli_query($conn, "SELECT * FROM materials WHERE id = $id");
    $editMaterial = mysqli_fetch_assoc($editResult);
}

// Все материалы для списка
$materialsResult = mysqli_query($conn, "
    SELECT m.*, c.name as category_name 
    FROM materials m 
    JOIN categories c ON m.category_id = c.id 
    ORDER BY m.created_at DESC
");
$allMaterials = [];
while ($row = mysqli_fetch_assoc($materialsResult)) {
    $allMaterials[] = $row;
}

// Категории для select
$catResult = mysqli_query($conn, "SELECT * FROM categories");
$categories = [];
while ($row = mysqli_fetch_assoc($catResult)) {
    $categories[] = $row;
}

if (isset($_GET['deleted'])) {
    $message = 'Материал удалён!';
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Материалы | Админка English Way</title>
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
        <a href="materials.php" class="sidebar-link active">
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
        <h1>Учебные материалы</h1>
        <div class="header-actions">
            <a href="materials.php?action=add" class="btn-add">
                <i class="fas fa-plus"></i> Добавить материал
            </a>
        </div>
    </header>

    <?php if ($message): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <!-- ФОРМА ДОБАВЛЕНИЯ / РЕДАКТИРОВАНИЯ -->
    <?php if ($action === 'add' || $action === 'edit'): ?>
        <div class="form-container">
            <h2><?php echo $editMaterial ? 'Редактировать' : 'Добавить'; ?> материал</h2>
            <form method="POST" enctype="multipart/form-data" class="admin-form">
                <?php if ($editMaterial): ?>
                    <input type="hidden" name="edit_id" value="<?php echo $editMaterial['id']; ?>">
                <?php endif; ?>

                <div class="form-row">
                    <div class="form-group">
                        <label>Название *</label>
                        <input type="text" name="title" value="<?php echo $editMaterial ? htmlspecialchars($editMaterial['title']) : ''; ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Категория *</label>
                        <select name="category_id" required>
                            <option value="">Выберите категорию</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>" <?php echo ($editMaterial && $editMaterial['category_id'] == $cat['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Уровень</label>
                        <select name="level">
                            <?php
                            $levels = ['Beginner', 'Elementary', 'Pre-Intermediate', 'Intermediate', 'Upper-Intermediate', 'Advanced'];
                            foreach ($levels as $lvl):
                                ?>
                                <option value="<?php echo $lvl; ?>" <?php echo ($editMaterial && $editMaterial['level'] == $lvl) ? 'selected' : ''; ?>>
                                    <?php echo $lvl; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Файл (PDF, DOC и т.д.)</label>
                        <input type="file" name="file">
                        <?php if ($editMaterial && $editMaterial['file_path']): ?>
                            <small>Текущий файл: <?php echo $editMaterial['file_path']; ?></small>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-group">
                    <label>Краткое описание</label>
                    <textarea name="description" rows="3"><?php echo $editMaterial ? htmlspecialchars($editMaterial['description']) : ''; ?></textarea>
                </div>

                <div class="form-group">
                    <label>Содержание (HTML)</label>
                    <textarea name="content" rows="10" class="content-editor"><?php echo $editMaterial ? htmlspecialchars($editMaterial['content']) : ''; ?></textarea>
                </div>

                <div class="form-group checkbox-group">
                    <label>
                        <input type="checkbox" name="is_new" <?php echo ($editMaterial && $editMaterial['is_new']) ? 'checked' : ''; ?>>
                        Отметить как NEW
                    </label>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-save">
                        <i class="fas fa-save"></i> Сохранить
                    </button>
                    <a href="materials.php" class="btn-cancel">Отмена</a>
                </div>
            </form>
        </div>
    <?php endif; ?>

    <!-- ТАБЛИЦА МАТЕРИАЛОВ -->
    <?php if ($action === 'list'): ?>
        <div class="table-wrapper">
            <table class="admin-table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Название</th>
                    <th>Категория</th>
                    <th>Уровень</th>
                    <th>Дата</th>
                    <th>Действия</th>
                </tr>
                </thead>
                <tbody>
                <?php if (count($allMaterials) > 0): ?>
                    <?php foreach ($allMaterials as $item): ?>
                        <tr>
                            <td><?php echo $item['id']; ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($item['title']); ?></strong>
                                <?php if ($item['is_new']): ?>
                                    <span class="badge-new">NEW</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($item['category_name']); ?></td>
                            <td><?php echo $item['level']; ?></td>
                            <td><?php echo date('d.m.Y', strtotime($item['created_at'])); ?></td>
                            <td class="actions-cell">
                                <a href="materials.php?action=edit&id=<?php echo $item['id']; ?>" class="btn-icon" title="Редактировать">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="materials.php?action=delete&id=<?php echo $item['id']; ?>" class="btn-icon btn-delete" title="Удалить" onclick="return confirm('Точно удалить?')">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="empty-cell">Нет материалов. <a href="materials.php?action=add">Добавить первый</a></td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</main>
</body>
</html>