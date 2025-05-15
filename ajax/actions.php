<?php
require_once __DIR__ . '/functions.php';

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

session_start();

// Разрешаем CORS для AJAX-запросов
header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

// Для простых POST-запросов (не CORS preflight)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Проверка CSRF-токена
        if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? null)) {
            throw new Exception("Недействительный CSRF-токен");
        }

        // Обработка добавления книги
        if (isset($_POST['addBook'])) {
            $required = ['title', 'author', 'price'];
            foreach ($required as $field) {
                if (empty($_POST[$field])) {
                    throw new Exception("Поле {$field} обязательно для заполнения");
                }
            }

            $bookId = $bookService->addBook(
                trim($_POST['title']),
                trim($_POST['author']),
                (float)$_POST['price'],
                !empty($_POST['publishYear']) ? (int)$_POST['publishYear'] : null,
                !empty($_POST['isbn']) ? trim($_POST['isbn']) : null,
                $_FILES['image'] ?? null
            );

            echo json_encode([
                'success' => true,
                'message' => 'Книга успешно добавлена',
                'bookId' => $bookId
            ]);
            exit;
        }
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
        exit;
    }
}

// После успешного добавления
error_log(print_r($_POST, true));
error_log(print_r($_FILES, true));

http_response_code(403);
echo json_encode(['success' => false, 'message' => 'Доступ запрещён']);