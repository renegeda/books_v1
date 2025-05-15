<?php
// Настройки ошибок
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/error.log');

// Конфигурация базы данных
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'My_Library');

// Настройки загрузки файлов
define('UPLOAD_DIR', __DIR__ . '/../assets/img/upload/');
define('ALLOWED_TYPES', ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif']);
define('MAX_SIZE', 2 * 1024 * 1024); // 2MB

// Создание подключения
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        throw new Exception("Database connection failed: " . $conn->connect_error);
    }
    
    $conn->set_charset("utf8mb4");
} catch (Exception $e) {
    error_log($e->getMessage());
    die("System error. Please try again later.");
}

// Создаем директории если их нет
if (!file_exists(UPLOAD_DIR)) {
    if (!mkdir(UPLOAD_DIR, 0755, true)) {
        error_log("Failed to create upload directory");
    }
}

if (!file_exists(__DIR__ . '/../logs')) {
    if (!mkdir(__DIR__ . '/../logs', 0755, true)) {
        error_log("Failed to create logs directory");
    }
}

// Функция для безопасного вывода ошибок
function display_error($message) {
    return '<div class="alert alert-danger">' . htmlspecialchars($message) . '</div>';
}