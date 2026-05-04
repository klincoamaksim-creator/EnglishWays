<?php
session_start();
require_once '../config/database.php';

// Если уже вошёл — редирект в панель
if (isset($_SESSION['admin_logged']) && $_SESSION['admin_logged'] === true) {
    header('Location: panel.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $key = isset($_POST['admin_key']) ? $_POST['admin_key'] : '';

    // Получаем ключ из БД
    $result = mysqli_query($conn, "SELECT setting_value FROM settings WHERE setting_key = 'admin_key'");
    $row = mysqli_fetch_assoc($result);
    $storedKey = $row ? $row['setting_value'] : 'admin123'; // по умолчанию

    if ($key === $storedKey) {
        $_SESSION['admin_logged'] = true;
        header('Location: panel.php');
        exit;
    } else {
        $error = 'Неверный ключ доступа';
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход | Админка English Way</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body class="login-page">

<div class="login-container">
    <div class="login-card">
        <div class="login-header">
            <div class="logo-area">
                <a href="index.php" class="logo-link">
                    <img src="/image/logo.png" alt="Logo" onerror="this.src='https://via.placeholder.com/45'">
                    <span class="logo-text">English Way</span>
                </a>
            </div>
            <h1>English Way</h1>
            <p>Панель преподавателя</p>
        </div>

        <form method="POST" class="login-form">
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <div class="form-group">
                <label for="admin_key">Ключ доступа</label>
                <div class="input-with-icon">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="admin_key" name="admin_key" placeholder="Введите секретный ключ" required autofocus>
                    <button type="button" class="toggle-password" onclick="togglePassword()">
                        <i class="fas fa-eye" id="eyeIcon"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn-login">
                <i class="fas fa-arrow-right"></i> Войти в панель
            </button>
        </form>

        <div class="login-footer">
            <a href="../index.php"><i class="fas fa-arrow-left"></i> На главную</a>
        </div>
    </div>
</div>

<script>
    function togglePassword() {
        const input = document.getElementById('admin_key');
        const icon = document.getElementById('eyeIcon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.className = 'fas fa-eye-slash';
        } else {
            input.type = 'password';
            icon.className = 'fas fa-eye';
        }
    }
</script>
</body>
</html>