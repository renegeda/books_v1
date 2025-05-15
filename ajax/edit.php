<?php
require_once __DIR__ . '/functions.php';

session_start();

// Проверка авторизации (если есть)
if (!isset($_SESSION['user'])) {
    $_SESSION['error'] = "Требуется авторизация";
    header("Location: ../index.php");
    exit;
}

// Проверка ID книги
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "Неверный ID книги";
    header("Location: ../index.php");
    exit;
}

$bookId = (int)$_GET['id'];

try {
    $book = $bookService->getBookById($bookId);
    
    if (!$book) {
        $_SESSION['error'] = "Книга не найдена";
        header("Location: ../index.php");
        exit;
    }
} catch (Exception $e) {
    $_SESSION['error'] = "Ошибка загрузки данных: " . $e->getMessage();
    header("Location: ../index.php");
    exit;
}

// Обработка формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $bookService->updateBook($bookId, [
            'title' => $_POST['title'],
            'author' => $_POST['author'],
            'price' => $_POST['price'],
            'publishYear' => $_POST['publishYear'],
            'isbn' => $_POST['isbn'] ?? null
        ]);
        
        $_SESSION['success'] = "Книга успешно обновлена";
        header("Location: ../index.php");
        exit;
    } catch (Exception $e) {
        $_SESSION['error'] = "Ошибка обновления: " . $e->getMessage();
    }
}

// Подключаем шапку
require __DIR__ . '/../includes/header.php';
?>

<div class="container mt-4">
    <h1>Редактировать книгу</h1>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['error']) ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
    
    <form method="POST" enctype="multipart/form-data">
        <!-- Поля формы остаются такими же -->
        <!-- ... -->
        
        <button type="submit" class="btn btn-primary">Сохранить</button>
        <a href="../index.php" class="btn btn-secondary">Отмена</a>
    </form>
</div>

<?php
// Подключаем подвал
require __DIR__ . '/../includes/footer.php';
?>