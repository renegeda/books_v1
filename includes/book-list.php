<h2 class="text-center my-2">Book Library</h2>
<p class="my-3 lead">Тут можно вывести задание.</p>
<p>Ejercicio 7</p>
<p>Crear una Base de Datos que se llame Biblioteca en MYSQL</p>
<p>Dentro de esa Base de Datos vamos a crear una tabla que se llame libros</p>
<p>IDLibro A_I Campo Llave<br>TituloLibro VARCHAR 100<br>AutorLibro VARCHAR 100<br>PrecioLibro DOUBLE<br>FechaEdicion DATE</p>
<p>Trabajo a realizar:</p>
<ul class="list-group list-group-numbered w-25">
    <li class="list-group-item">Crear un proceso en PHP que permita agregar datos</li>
    <li class="list-group-item">Hacer un proceso que nos muestre en una tabla todos los datos</li>
    <li class="list-group-item">Hacer un proceso que no muestre en una tabla los libros de más de 12 euros</li>
    <li class="list-group-item">Hacer un proceso que nos permita eliminar un libro</li>
    <li class="list-group-item">Hacer un proceso que nos permita modificar un libro</li>
</ul>
<div class="d-flex align-items-center justify-content-between mt-4">
    <h2 class="text-start mb-0">Все книги</h2>
    <div class="d-flex align-items-center">
        <a href="#" class="border border-2 rounded-3 card-dashed-hover me-4" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight">
            <div class="icon-shape icon-lg"><i class="bi bi-book"></i></div>
        </a>
        <div class="me-3">
            <h4 class="mb-0"><a href="#" class="text-inherit" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight">Добавить новую книгу</a></h4>
        </div>
    </div>
</div>

<div class="table-responsive py-5">
    <div class="input-group mb-3" style="width: 205px;">
        <label class="input-group-text" for="perPageSelect">Книг на странице:</label>
        <select class="form-select" id="perPageSelect">
            <option value="5" <?= $perPage == 5 ? 'selected' : '' ?>>5</option>
            <option value="10" <?= $perPage == 10 ? 'selected' : '' ?>>10</option>
            <option value="20" <?= $perPage == 20 ? 'selected' : '' ?>>20</option>
        </select>
    </div>

    <div id="books-container">
        <?php include 'book-table.php'; ?>
    </div>
</div>

<div class="table-responsive py-5">
    <h2 class="text-start my-2">Books Under 12€</h2>
    <?php include 'cheap-books-table.php'; ?>
</div>

<!-- Модальное окно добавления книги -->
<?php include 'book-form-modal.php'; ?>