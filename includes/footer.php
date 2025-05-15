</div>
<!-- offcanvas -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight" style="width: 500px">
    <div class="offcanvas-body" data-simplebar>
        <div class="offcanvas-header px-2 pt-0">
            <h3 class="offcanvas-title" id="offcanvasExampleLabel">Добавить новую книгу</h3>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <!-- card body -->
        <div class="container">
            <!-- form -->
            <form method="post" action="actions.php" enctype="multipart/form-data">
                <!-- form group -->
                <div class="mb-3 col-12">
                    <div class="form-group">
                        <label>Название: <input type="text" class="form-control" name="title" placeholder="Укажите название книги" required></label>
                    </div>
                </div>
                <!-- form group -->
                <div class="mb-3 col-12">
                    <div class="form-group">
                        <label>Автор: <input type="text" class="form-control" name="author" placeholder="Укажите автора книги" required></label>
                    </div>
                </div>
                <!-- form group -->
                <div class="mb-3 col-12">
                    <div class="form-group">
                        <label>Цена: <input type="number" class="form-control" name="price" step="0.01" min="0" required></label>
                    </div>
                </div>
                <!-- form group -->
                <div class="mb-3 col-12">
                    <div class="form-group">
                        <label>Год издания: <input type="number" class="form-control" name="publishYear" min="1000" max="<?= date('Y') + 5 ?>" step="1">
                    </div>
                </div>
                <!-- form group -->
                <div class="mb-3 col-12">
                    <div class="form-group">
                        <label>ISBN: <input type="text" class="form-control" name="isbn" pattern="\d{13}" title="13-digit ISBN"></label>
                    </div>
                </div>
                <!-- form group -->
                <div class="mb-3 col-12">
                    <div class="form-group">
                        <label>Обложка: <input type="file" class="form-control" name="image" accept="image/*"></label>
                    </div>
                </div>
                <!-- button -->
                <div class="col-12 mt-4">
                    <button type="submit" class="btn btn-outline-light me-2" name="addBook">Добавить книгу</button>
                    <button type="button" class="btn btn-outline-secondary ms-2" data-bs-dismiss="offcanvas" aria-label="Close">Я передумал</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/main.js"></script>
</body>

</html>