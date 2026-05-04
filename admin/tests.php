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
    $fileResult = mysqli_query($conn, "SELECT file_path FROM tests WHERE id = $id");
    $fileRow = mysqli_fetch_assoc($fileResult);
    if ($fileRow && $fileRow['file_path'] && file_exists('../' . $fileRow['file_path'])) {
        unlink('../' . $fileRow['file_path']);
    }
    mysqli_query($conn, "DELETE FROM tests WHERE id = $id");
    header('Location: tests.php?deleted=1');
    exit;
}

// СОХРАНЕНИЕ
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $category_id = intval($_POST['category_id']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $time_limit = intval($_POST['time_limit']);
    $questions_count = intval($_POST['questions_count']);
    $is_new = isset($_POST['is_new']) ? 1 : 0;
    $editId = isset($_POST['edit_id']) ? intval($_POST['edit_id']) : 0;

    $file_path = '';
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/tests/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        $fileName = time() . '_' . basename($_FILES['file']['name']);
        $file_path = 'uploads/tests/' . $fileName;
        move_uploaded_file($_FILES['file']['tmp_name'], $uploadDir . $fileName);
    }

    if ($editId > 0) {
        if ($file_path) {
            $oldFile = mysqli_fetch_assoc(mysqli_query($conn, "SELECT file_path FROM tests WHERE id = $editId"));
            if ($oldFile['file_path'] && file_exists('../' . $oldFile['file_path'])) {
                unlink('../' . $oldFile['file_path']);
            }
            mysqli_query($conn, "UPDATE tests SET title='$title', category_id=$category_id, description='$description', time_limit=$time_limit, questions_count=$questions_count, is_new=$is_new, file_path='$file_path' WHERE id=$editId");
        } else {
            mysqli_query($conn, "UPDATE tests SET title='$title', category_id=$category_id, description='$description', time_limit=$time_limit, questions_count=$questions_count, is_new=$is_new WHERE id=$editId");
        }
        $message = 'Тест обновлён!';
    } else {
        mysqli_query($conn, "INSERT INTO tests (title, category_id, description, time_limit, questions_count, is_new, file_path) VALUES ('$title', $category_id, '$description', $time_limit, $questions_count, $is_new, '$file_path')");
        $message = 'Тест добавлен!';
    }
}

// Тест для редактирования
$editTest = null;
if ($action === 'edit' && $id > 0) {
    $editResult = mysqli_query($conn, "SELECT * FROM tests WHERE id = $id");
    $editTest = mysqli_fetch_assoc($editResult);
}

// Все тесты
$testsResult = mysqli_query($conn, "
    SELECT t.*, tc.name as category_name 
    FROM tests t 
    JOIN test_categories tc ON t.category_id = tc.id 
    ORDER BY tc.sort_order, t.created_at DESC
");
$allTests = [];
while ($row = mysqli_fetch_assoc($testsResult)) {
    $allTests[] = $row;
}

// Категории для select
$catResult = mysqli_query($conn, "SELECT * FROM test_categories ORDER BY sort_order");
$categories = [];
while ($row = mysqli_fetch_assoc($catResult)) {
    $categories[] = $row;
}

if (isset($_GET['deleted'])) $message = 'Тест удалён!';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Тесты | Админка English Way</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="/css/home.css">


</head>
<body class="admin-body">

<aside class="sidebar">
    <a href="index.php" class="logo-link">
        <img src="/image/logo.png" alt="Logo" onerror="this.src='https://via.placeholder.com/45'">
        <span class="logo-text">English Way</span>
    </a>
    <nav class="sidebar-nav">
        <a href="panel.php" class="sidebar-link"><i class="fas fa-th-large"></i> Панель</a>
        <a href="materials.php" class="sidebar-link"><i class="fas fa-book-open"></i> Материалы</a>
        <a href="tests.php" class="sidebar-link active"><i class="fas fa-clipboard-list"></i> Тесты</a>
        <a href="photos.php" class="sidebar-link"><i class="fas fa-images"></i> Фото жизни</a>
        <a href="../index.php" class="sidebar-link" target="_blank"><i class="fas fa-external-link-alt"></i> Сайт</a>
    </nav>
    <div class="sidebar-footer">
        <a href="logout.php" class="sidebar-link logout-link"><i class="fas fa-sign-out-alt"></i> Выйти</a>
    </div>
</aside>

<main class="main-content">
    <header class="content-header">
        <h1>Тесты и задания</h1>
        <div class="header-actions">
            <a href="tests.php?action=add" class="btn-add"><i class="fas fa-plus"></i> Добавить тест</a>
        </div>
    </header>

    <?php if ($message): ?>
        <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo $message; ?></div>
    <?php endif; ?>

    <!-- ФОРМА -->
    <?php if ($action === 'add' || $action === 'edit'): ?>
        <div class="form-container">
            <h2><?php echo $editTest ? 'Редактировать' : 'Добавить'; ?> тест</h2>
            <form method="POST" enctype="multipart/form-data" class="admin-form">
                <?php if ($editTest): ?>
                    <input type="hidden" name="edit_id" value="<?php echo $editTest['id']; ?>">
                <?php endif; ?>

                <div class="form-row">
                    <div class="form-group">
                        <label>Название *</label>
                        <input type="text" name="title" value="<?php echo $editTest ? htmlspecialchars($editTest['title']) : ''; ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Категория *</label>
                        <select name="category_id" required>
                            <option value="">Выберите</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>" <?php echo ($editTest && $editTest['category_id'] == $cat['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Описание</label>
                    <textarea name="description" rows="3"><?php echo $editTest ? htmlspecialchars($editTest['description']) : ''; ?></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Время (минут)</label>
                        <input type="number" name="time_limit" value="<?php echo $editTest ? $editTest['time_limit'] : ''; ?>" placeholder="30">
                    </div>
                    <div class="form-group">
                        <label>Количество вопросов</label>
                        <input type="number" name="questions_count" value="<?php echo $editTest ? $editTest['questions_count'] : ''; ?>" placeholder="20">
                    </div>
                </div>

                <div class="form-group">
                    <label>Файл теста (PDF, DOC)</label>
                    <input type="file" name="file">
                    <?php if ($editTest && $editTest['file_path']): ?>
                        <small>Текущий: <?php echo $editTest['file_path']; ?></small>
                    <?php endif; ?>
                </div>

                <div class="form-group checkbox-group">
                    <label>
                        <input type="checkbox" name="is_new" <?php echo ($editTest && $editTest['is_new']) ? 'checked' : ''; ?>>
                        Отметить как NEW
                    </label>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-save"><i class="fas fa-save"></i> Сохранить</button>
                    <a href="tests.php" class="btn-cancel">Отмена</a>
                </div>
            </form>
        </div>
    <?php endif; ?>

    <!-- ТАБЛИЦА -->
    <?php if ($action === 'list'): ?>
        <div class="table-wrapper">
            <table class="admin-table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Название</th>
                    <th>Категория</th>
                    <th>Время</th>
                    <th>Вопросов</th>
                    <th>Дата</th>
                    <th>Действия</th>
                </tr>
                </thead>
                <tbody>
                <?php if (count($allTests) > 0): ?>
                    <?php foreach ($allTests as $test): ?>
                        <tr>
                            <td><?php echo $test['id']; ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($test['title']); ?></strong>
                                <?php if ($test['is_new']): ?><span class="badge-new">NEW</span><?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($test['category_name']); ?></td>
                            <td><?php echo $test['time_limit'] ? $test['time_limit'] . ' мин' : '-'; ?></td>
                            <td><?php echo $test['questions_count'] ?: '-'; ?></td>
                            <td><?php echo date('d.m.Y', strtotime($test['created_at'])); ?></td>
                            <td class="actions-cell">
                                <a href="tests.php?action=edit&id=<?php echo $test['id']; ?>" class="btn-icon"><i class="fas fa-edit"></i></a>
                                <a href="tests.php?action=delete&id=<?php echo $test['id']; ?>" class="btn-icon btn-delete" onclick="return confirm('Удалить?')"><i class="fas fa-trash-alt"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="7" class="empty-cell">Нет тестов</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</main>
</body>
</html>