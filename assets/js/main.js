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

    document.getElementById('add-book-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const form = e.target;
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        try {
            // Показать состояние загрузки
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Добавление...';
            
            // Отправка данных
            const response = await fetch('ajax/actions.php', {
                method: 'POST',
                body: new FormData(form)
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const result = await response.json();
            console.log("Результат:", result);
            
            if (result.success) {
                // 1. Закрыть модальное окно
                bootstrap.Offcanvas.getInstance(document.getElementById('offcanvasRight')).hide();
                
                // 2. Показать уведомление
                showToast('Успех!', result.message, 'success');
                
                // 3. Обновить список книг
                await refreshBookList();
                
                // 4. Очистить форму
                form.reset();
            } else {
                throw new Error(result.message);
            }
        } catch (error) {
            console.error("Ошибка:", error);
            showToast('Ошибка', error.message, 'danger');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    });
    
    // Функция обновления списка книг
    async function refreshBookList() {
        try {
            const response = await fetch('index.php?ajax=1');
            if (!response.ok) throw new Error('Ошибка загрузки списка');
            const html = await response.text();
            document.getElementById('books-container').innerHTML = html;
        } catch (error) {
            console.error("Ошибка обновления:", error);
            // Fallback: перезагрузка страницы
            window.location.reload();
        }
    }
    
    // Функция показа уведомлений
    function showToast(title, message, type) {
        const toastHtml = `
            <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
                <div class="toast show" role="alert">
                    <div class="toast-header bg-${type} text-white">
                        <strong class="me-auto">${title}</strong>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
                    </div>
                    <div class="toast-body">
                        ${message}
                    </div>
                </div>
            </div>
        `;
        
        document.body.insertAdjacentHTML('beforeend', toastHtml);
        setTimeout(() => document.querySelector('.toast').remove(), 5000);
    }
    
    // Функция обновления списка книг
    async function refreshBookList() {
        try {
            const response = await fetch('index.php?get_books=1');
            const html = await response.text();
            document.getElementById('books-container').innerHTML = html;
        } catch (error) {
            console.error("Ошибка обновления списка:", error);
            // Fallback - перезагрузка страницы
            window.location.reload();
        }
    }
    
    // Функция показа уведомлений
    function showAlert(title, message, type) {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                <strong>${title}</strong> ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        // Добавляем в начало контейнера
        const container = document.querySelector('.container');
        container.insertAdjacentHTML('afterbegin', alertHtml);
        
        // Автоматическое скрытие через 5 сек
        setTimeout(() => {
            const alert = document.querySelector('.alert');
            if (alert) alert.remove();
        }, 5000);
    }
    
    // Функция для загрузки списка книг
    async function loadBooks() {
        try {
            const response = await fetch('index.php?ajax=1');
            const html = await response.text();
            document.getElementById('books-container').innerHTML = html;
        } catch (error) {
            console.error('Ошибка загрузки книг:', error);
        }
    }
    
    // Функция для показа уведомлений
    function showToast(title, message, type) {
        const toastContainer = document.getElementById('toast-container') || createToastContainer();
        const toastId = 'toast-' + Date.now();
        
        toastContainer.insertAdjacentHTML('beforeend', `
            <div id="${toastId}" class="toast align-items-center text-white bg-${type} border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        <strong>${title}</strong><br>${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `);
        
        const toast = new bootstrap.Toast(document.getElementById(toastId));
        toast.show();
        
        // Автоматическое скрытие через 5 секунд
        setTimeout(() => toast.dispose(), 5000);
    }
    
    function createToastContainer() {
        const container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'position-fixed bottom-0 end-0 p-3';
        container.style.zIndex = '11';
        document.body.appendChild(container);
        return container;
    }
    
    // Инициализация при загрузке
    initEventListeners();
});