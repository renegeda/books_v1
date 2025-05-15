<?php
require_once __DIR__ . '/functions.php';
session_start();

// 1. Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "Требуется авторизация";
    header("Location: ../index.php");
    exit;
}

// 2. Валидация ID
$bookId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$bookId) {
    $_SESSION['error'] = "Неверный ID книги";
    header("Location: ../index.php");
    exit;
}

// 3. Получение книги
try {
    $book = $bookService->getBookById($bookId);
    if (!$book) {
        throw new Exception("Книга не найдена");
    }
} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
    header("Location: ../index.php");
    exit;
}

// 4. Обработка формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $data = [
            'title' => trim($_POST['title']),
            'author' => trim($_POST['author']),
            'price' => (float)$_POST['price'],
            'publishYear' => !empty($_POST['publishYear']) ? (int)$_POST['publishYear'] : null,
            'isbn' => !empty($_POST['isbn']) ? trim($_POST['isbn']) : null
        ];
        
        $bookService->updateBook($bookId, $data);
        
        $_SESSION['success'] = "Книга успешно обновлена";
        header("Location: ../index.php");
        exit;
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }
}

// 5. Подключение шаблонов
require __DIR__ . '/../header.php';
?>

<!-- Форма редактирования -->
<div class="container mt-4">
    <!-- ... существующая форма ... -->
</div>

<?php
require __DIR__ . '/../footer.php';