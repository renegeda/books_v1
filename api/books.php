<?php
require_once '../ajax/functions.php';

header('Content-Type: application/json');

try {
    $page = max(1, (int)($_GET['page'] ?? 1));
    $perPage = max(1, (int)($_GET['per_page'] ?? 5));
    $cheapOnly = isset($_GET['cheap']) && $_GET['cheap'] == 'true';
    
    $data = $bookService->getBooksPaginated($page, $perPage, $cheapOnly);
    
    ob_start();
    if ($cheapOnly) {
        include '../includes/cheap-books-table.php';
    } else {
        include '../includes/book-table.php';
    }
    $html = ob_get_clean();
    
    echo json_encode([
        'success' => true,
        'html' => $html,
        'totalPages' => $data['totalPages'],
        'currentPage' => $data['currentPage']
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}