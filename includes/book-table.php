<table class="table mb-0">
    <thead class="table-light">
        <tr>
            <th>Обложка</th>
            <th>Название</th>
            <th>Автор</th>
            <th>Цена</th>
            <th>Год</th>
            <th>Действия</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($allBooksData['books'] as $book): ?>
            <tr class="border-bottom">
                <td>
                    <?php if (!empty($book['image_url'])): ?>
                        <img src="<?= htmlspecialchars($book['image_url']) ?>" class="book-cover rounded">
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($book['title']) ?></td>
                <td><?= htmlspecialchars($book['author']) ?></td>
                <td><?= number_format($book['price'], 2) ?> €</td>
                <td><?= !empty($book['publishYear']) ? htmlspecialchars($book['publishYear']) : 'N/A' ?></td>
                <td>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary rounded-circle dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown">
                            <i class="bi bi-three-dots-vertical"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="ajax/edit.php?id=<?= htmlspecialchars($book['idLibro']) ?>">
                                    <i class="bi bi-pencil me-2"></i>Редактировать
                                </a></li>
                            <li>
                                <form method="POST" action="ajax/actions.php" class="delete-form"
                                    data-confirm="Вы уверены, что хотите удалить эту книгу?">
                                    <input type="hidden" name="deleteId" value="<?= htmlspecialchars($book['idLibro']) ?>">
                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bi bi-trash me-2"></i> Удалить
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<nav class="mt-3">
    <ul class="pagination">
        <?php if ($currentPage > 1): ?>
            <li class="page-item">
                <a class="page-link" href="?page=<?= $currentPage - 1 ?>&per_page=<?= $perPage ?>">
                    &laquo; Назад
                </a>
            </li>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $allBooksData['totalPages']; $i++): ?>
            <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                <a class="page-link" href="?page=<?= $i ?>&per_page=<?= $perPage ?>">
                    <?= $i ?>
                </a>
            </li>
        <?php endfor; ?>

        <?php if ($currentPage < $allBooksData['totalPages']): ?>
            <li class="page-item">
                <a class="page-link" href="?page=<?= $currentPage + 1 ?>&per_page=<?= $perPage ?>">
                    Вперёд &raquo;
                </a>
            </li>
        <?php endif; ?>
    </ul>
</nav>