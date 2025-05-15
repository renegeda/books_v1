<?php
require_once 'functions.php';

// AJAX-режим для получения списка книг
if (isset($_GET['ajax'])) {
    $currentPage = $_GET['page'] ?? 1;
    $perPage = $_GET['per_page'] ?? 10;
    
    try {
        $booksData = $bookService->getBooksPaginated($currentPage, $perPage);
        include 'includes/book-table.php';
    } catch (Exception $e) {
        http_response_code(500);
        echo '<div class="alert alert-danger">Ошибка загрузки списка книг</div>';
    }
    exit;
}

// Инициализация CSRF-токена
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}


$currentPage = $_GET['page'] ?? 1;
$perPage = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 5;

try {
    $allBooksData = $bookService->getBooksPaginated($currentPage, $perPage);
    $cheapBooksData = $bookService->getBooksPaginated(1, PHP_INT_MAX, true);
} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
    $allBooksData = ['books' => [], 'totalPages' => 1];
    $cheapBooksData = ['books' => []];
}

require 'includes/header.php';

if (isset($_SESSION['error'])) {
    echo display_error($_SESSION['error']);
    unset($_SESSION['error']);
}

if (isset($_SESSION['success'])) {
    echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['success']) . '</div>';
    unset($_SESSION['success']);
}

require 'includes/book-list.php';
require 'includes/footer.php';