document.addEventListener('DOMContentLoaded', function() {
    // Инициализация
    const booksContainer = document.getElementById('books-container');
    const perPageSelect = document.getElementById('perPageSelect');
    
    // Обработчик изменения количества элементов на странице
    perPageSelect.addEventListener('change', function() {
        const perPage = this.value;
        loadBooks(1, perPage);
    });
    
    // Загрузка книг через AJAX
    function loadBooks(page, perPage) {
        showLoading();
        
        fetch(`/api/books.php?page=${page}&per_page=${perPage}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                booksContainer.innerHTML = data.html;
                initEventListeners();
            })
            .catch(error => {
                console.error('Error:', error);
                booksContainer.innerHTML = `
                    <div class="alert alert-danger">
                        Ошибка загрузки данных. Пожалуйста, попробуйте позже.
                    </div>
                `;
            })
            .finally(() => hideLoading());
    }
    
    // Удаление книги
    function initEventListeners() {
        document.querySelectorAll('.delete-book').forEach(btn => {
            btn.addEventListener('click', function() {
                if (confirm('Вы уверены, что хотите удалить эту книгу?')) {
                    deleteBook(this.dataset.id);
                }
            });
        });
    }
    
    function deleteBook(id) {
        showLoading();
        
        fetch('ajax/actions.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `deleteId=${id}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('Книга успешно удалена', 'success');
                loadBooks(1, perPageSelect.value);
            } else {
                throw new Error(data.message || 'Ошибка при удалении');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert(error.message, 'danger');
        })
        .finally(() => hideLoading());
    }
    
    // Вспомогательные функции
    function showLoading() {
        const loadingDiv = document.createElement('div');
        loadingDiv.className = 'text-center my-5';
        loadingDiv.innerHTML = `
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        `;
        booksContainer.innerHTML = '';
        booksContainer.appendChild(loadingDiv);
    }
    
    function hideLoading() {
        // Уже обрабатывается в loadBooks
    }
    
    function showAlert(message, type) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        const container = document.querySelector('.container');
        container.insertBefore(alertDiv, container.firstChild);
        
        setTimeout(() => {
            alertDiv.classList.remove('show');
            setTimeout(() => alertDiv.remove(), 150);
        }, 5000);
    }

    document.querySelectorAll('.delete-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (confirm(this.dataset.confirm)) {
                fetch(this.action, {
                    method: 'POST',
                    body: new FormData(this),
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Удаляем строку таблицы или обновляем список
                        this.closest('tr').remove();
                    } else {
                        alert('Ошибка: ' + data.message);
                    }
                });
            }
        });
    });
    
    // Инициализация при загрузке
    initEventListeners();
});