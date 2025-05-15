<?php
// Изменяем путь к config.php в зависимости от расположения файла
require_once __DIR__ . '/../config/config.php';

class BookService
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function addBook($title, $author, $price, $publishYear, $isbn = null, $imageFile = null)
    {
        try {
            $publishDate = $publishYear ? $publishYear . '-01-01' : null;
            $imagePath = $this->handleImageUpload($imageFile);

            $stmt = $this->conn->prepare("INSERT INTO Books (title, author, price, publishDate, isbn, image_path) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssdsss", $title, $author, $price, $publishDate, $isbn, $imagePath);

            if (!$stmt->execute()) {
                throw new Exception("Failed to add book: " . $stmt->error);
            }

            return $stmt->insert_id;
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    private function handleImageUpload($file)
    {
        if (!$file || $file['error'] != UPLOAD_ERR_OK) {
            return null;
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);

        if (!array_key_exists($mime, ALLOWED_TYPES)) {
            throw new Exception("Invalid file type. Allowed: " . implode(', ', array_keys(ALLOWED_TYPES)));
        }

        if ($file['size'] > MAX_SIZE) {
            throw new Exception("File too large. Max size: " . (MAX_SIZE / 1024 / 1024) . "MB");
        }

        $ext = ALLOWED_TYPES[$mime];
        $filename = uniqid() . '.' . $ext;
        $destination = UPLOAD_DIR . $filename;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            throw new Exception("Failed to move uploaded file");
        }

        return $filename;
    }

    public function getBooksPaginated($page = 1, $perPage = 5, $filterByPrice = false)
    {
        try {
            $page = max(1, (int)$page);
            $perPage = max(1, (int)$perPage);
            $offset = ($page - 1) * $perPage;

            $sql = "SELECT SQL_CALC_FOUND_ROWS idLibro, title, author, price, 
            YEAR(publishDate) as publishYear, isbn, 
            CONCAT('assets/img/upload/', image_path) as image_url 
            FROM Books";

            if ($filterByPrice) {
                $sql .= " WHERE price <= 12";
            }

            $sql .= " LIMIT ? OFFSET ?";

            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ii", $perPage, $offset);

            if (!$stmt->execute()) {
                throw new Exception("Failed to fetch books: " . $stmt->error);
            }

            $result = $stmt->get_result();
            $books = $result->fetch_all(MYSQLI_ASSOC);

            $totalRows = $this->conn->query("SELECT FOUND_ROWS()")->fetch_row()[0];
            $totalPages = max(1, ceil($totalRows / $perPage));

            return [
                'books' => $books,
                'totalPages' => $totalPages,
                'currentPage' => $page,
                'totalItems' => $totalRows
            ];
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw new Exception("Error loading books");
        }
    }

    public function deleteBook($id)
    {
        try {
            $this->conn->begin_transaction();

            // Получаем путь к изображению
            $stmt = $this->conn->prepare("SELECT image_path FROM Books WHERE idLibro = ?");
            $stmt->bind_param("i", $id);

            if (!$stmt->execute()) {
                throw new Exception("Failed to find book: " . $stmt->error);
            }

            $imagePath = $stmt->get_result()->fetch_assoc()['image_path'];

            // Удаляем книгу
            $stmt = $this->conn->prepare("DELETE FROM Books WHERE idLibro = ?");
            $stmt->bind_param("i", $id);

            if (!$stmt->execute()) {
                throw new Exception("Failed to delete book: " . $stmt->error);
            }

            // Удаляем изображение, если есть
            if ($imagePath && file_exists(UPLOAD_DIR . $imagePath)) {
                unlink(UPLOAD_DIR . $imagePath);
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log($e->getMessage());
            throw new Exception("Error deleting book");
        }
    }

    public function getBookById($id)
    {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM Books WHERE idLibro = ?");
            $stmt->bind_param("i", $id);

            if (!$stmt->execute()) {
                throw new Exception("Failed to fetch book: " . $stmt->error);
            }

            $book = $stmt->get_result()->fetch_assoc();

            if (!$book) {
                throw new Exception("Book not found");
            }

            return $book;
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function updateBook($id, $data)
    {
        try {
            $stmt = $this->conn->prepare("UPDATE Books SET 
                title = ?, 
                author = ?, 
                price = ?, 
                publishDate = ?, 
                isbn = ? 
                WHERE idLibro = ?");

            $publishDate = !empty($data['publishYear']) ? $data['publishYear'] . '-01-01' : null;

            $stmt->bind_param(
                "ssdssi",
                $data['title'],
                $data['author'],
                $data['price'],
                $publishDate,
                $data['isbn'],
                $id
            );

            if (!$stmt->execute()) {
                throw new Exception("Failed to update book: " . $stmt->error);
            }

            return true;
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw new Exception("Error updating book");
        }
    }
}

// Инициализация сервиса
$bookService = new BookService($conn);
