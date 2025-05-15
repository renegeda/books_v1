<!-- Добавьте перед закрывающим </body> -->
<div id="toast-container"></div>
<!-- Offcanvas форма -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight" style="width: 500px">
    <div class="offcanvas-body" data-simplebar>
        <div class="offcanvas-header px-2 pt-0">
            <h3 class="offcanvas-title">Добавить новую книгу</h3>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>

        <div class="container">
            <form id="add-book-form" method="post" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                <input type="hidden" name="addBook" value="1">

                <div class="mb-3 col-12">
                    <label class="form-label">Название</label>
                    <input type="text" class="form-control" name="title" required>
                </div>

                <div class="mb-3 col-12">
                    <label class="form-label">Автор</label>
                    <input type="text" class="form-control" name="author" required>
                </div>

                <div class="mb-3 col-12">
                    <label class="form-label">Цена</label>
                    <input type="number" class="form-control" name="price" step="0.01" min="0" required>
                </div>

                <div class="mb-3 col-12">
                    <label class="form-label">Год издания</label>
                    <input type="number" class="form-control" name="publishYear"
                        min="1000" max="<?= date('Y') + 5 ?>">
                </div>

                <div class="mb-3 col-12">
                    <label class="form-label">ISBN</label>
                    <input type="text" class="form-control" name="isbn"
                        pattern="\d{13}" title="13 цифр">
                </div>

                <div class="mb-3 col-12">
                    <label class="form-label">Обложка</label>
                    <input type="file" class="form-control" name="image" accept="image/*">
                </div>

                <div class="col-12 mt-4">
                    <button type="submit" class="btn btn-primary">
                        Добавить книгу
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="offcanvas">
                        Отмена
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- js -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/main.js"></script>

</body>

</html>