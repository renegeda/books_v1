<?php
// Разрешаем CORS и AJAX-запросы
header("Access-Control-Allow-Origin: http://localhost");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json");

// Для preflight-запросов
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once __DIR__.'/functions.php';
session_start();

// Проверка CSRF-токена
if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? null)) {
    http_response_code(403);
    die(json_encode([
        'success' => false,
        'error' => 'Недействительный CSRF-токен',
        'debug' => [
            'session_token' => $_SESSION['csrf_token'] ?? null,
            'post_token' => $_POST['csrf_token'] ?? null
        ]
    ]));
}

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $response = ['success' => false];
        
        // Обработка добавления книги
        if (isset($_POST['addBook'])) {
            $bookId = $bookService->addBook(
                trim($_POST['title']),
                trim($_POST['author']),
                (float)$_POST['price'],
                !empty($_POST['publishYear']) ? (int)$_POST['publishYear'] : null,
                !empty($_POST['isbn']) ? trim($_POST['isbn']) : null,
                $_FILES['image'] ?? null
            );
            
            // Явная проверка добавленной записи
            $check = $bookService->getBookById($bookId);
            if (!$check) {
                throw new Exception("Книга не была добавлена в БД");
            }
            
            $response = [
                'success' => true,
                'message' => 'Книга успешно добавлена',
                'bookId' => $bookId,
                'data' => [
                    'title' => $_POST['title'],
                    'author' => $_POST['author']
                ]
            ];
        }
        
        echo json_encode($response);
        exit;
    }
    
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Метод не поддерживается']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
}