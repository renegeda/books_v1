<?php
require_once __DIR__ . '/functions.php';

session_start();

header('Content-Type: application/json'); // По умолчанию для всех ответов

function sendResponse($success, $message = '', $redirect = null) {
    $response = [
        'success' => $success,
        'message' => $message,
        'redirect' => $redirect
    ];
    
    if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) {
        // Для обычных запросов сохраняем сообщение в сессии и редиректим
        if ($success) {
            $_SESSION['success'] = $message;
        } else {
            $_SESSION['error'] = $message;
        }
        header("Location: " . ($redirect ?? '../index.php'));
        exit;
    }
    
    // Для AJAX запросов возвращаем JSON
    echo json_encode($response);
    exit;
}

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Метод запроса не поддерживается");
    }

    // Обработка удаления книги
    if (isset($_POST['deleteId'])) {
        $bookId = (int)$_POST['deleteId'];
        
        if ($bookId <= 0) {
            throw new Exception("Неверный ID книги");
        }
        
        $success = $bookService->deleteBook($bookId);
        
        if (!$success) {
            throw new Exception("Не удалось удалить книгу");
        }
        
        sendResponse(true, "Книга успешно удалена");
    }

    // Обработка добавления книги
    if (isset($_POST['addBook'])) {
        $requiredFields = ['title', 'author', 'price'];
        foreach ($requiredFields as $field) {
            if (empty($_POST[$field])) {
                throw new Exception("Поле " . ucfirst($field) . " обязательно для заполнения");
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
        
        sendResponse(true, "Книга успешно добавлена", "../index.php");
    }

    // Обработка обновления книги (если нужно)
    if (isset($_POST['updateBook'])) {
        // ... аналогичная обработка ...
    }

    // Если ни одно действие не распознано
    throw new Exception("Неизвестное действие");

} catch (Exception $e) {
    error_log("Ошибка в actions.php: " . $e->getMessage());
    sendResponse(false, $e->getMessage());
}