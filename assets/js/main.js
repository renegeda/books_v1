/**
 * Показывает уведомление
 */
function showToast(title, message, type = 'success') {
    const toastContainer = document.getElementById('toast-container') || createToastContainer();
    const toastId = 'toast-' + Date.now();
    
    toastContainer.insertAdjacentHTML('beforeend', `
        <div id="${toastId}" class="toast show" role="alert">
            <div class="toast-header bg-${type} text-white">
                <strong class="me-auto">${title}</strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body">
                ${message}
            </div>
        </div>
    `);
    
    // Автоматическое скрытие через 5 секунд
    setTimeout(() => {
        const toast = document.getElementById(toastId);
        if (toast) toast.remove();
    }, 5000);
}

/**
 * Создает контейнер для уведомлений
 */
function createToastContainer() {
    const container = document.createElement('div');
    container.id = 'toast-container';
    container.className = 'position-fixed bottom-0 end-0 p-3';
    container.style.zIndex = '1100';
    document.body.appendChild(container);
    return container;
}

/**
 * Обновляет список книг
 */
async function refreshBookList() {
    try {
        const response = await fetch('index.php?ajax=1');
        if (!response.ok) throw new Error('Ошибка загрузки списка');
        
        const html = await response.text();
        const container = document.getElementById('books-container');
        
        if (container) {
            container.innerHTML = html;
        } else {
            console.error('Контейнер книг не найден');
            window.location.reload();
        }
    } catch (error) {
        console.error('Ошибка обновления списка:', error);
        showToast('Ошибка', 'Не удалось обновить список', 'danger');
        window.location.reload();
    }
}

/**
 * Обработчик отправки формы
 */
async function handleBookSubmit(event) {
    event.preventDefault();
    const form = event.target;
    const submitBtn = form.querySelector('button[type="submit"]');
    
    if (!submitBtn) return;
    
    const originalText = submitBtn.innerHTML;
    
    try {
        // Показать состояние загрузки
        submitBtn.disabled = true;
        submitBtn.innerHTML = `
            <span class="spinner-border spinner-border-sm" role="status"></span>
            Сохранение...
        `;
        
        // Отправка данных
        const response = await fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        if (!response.ok) {
            const error = await response.json().catch(() => null);
            throw new Error(error?.message || `HTTP ошибка: ${response.status}`);
        }
        
        const result = await response.json();
        console.debug('Ответ сервера:', result);
        
        if (result.success) {
            showToast('Успех!', result.message, 'success');
            await refreshBookList();
            
            // Закрыть модальное окно
            const offcanvas = bootstrap.Offcanvas.getInstance(
                document.getElementById('offcanvasRight')
            );
            if (offcanvas) offcanvas.hide();
            
            // Очистить форму
            form.reset();
        } else {
            throw new Error(result.error || result.message || 'Неизвестная ошибка');
        }
    } catch (error) {
        console.error('Ошибка при отправке формы:', error);
        showToast('Ошибка', error.message, 'danger');
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    }
}

// Инициализация после загрузки DOM
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('add-book-form');
    if (form) {
        form.addEventListener('submit', handleBookSubmit);
    }
    
    // Инициализация всех toast-уведомлений
    const toastElList = [].slice.call(document.querySelectorAll('.toast'));
    toastElList.map(function(toastEl) {
        return new bootstrap.Toast(toastEl).show();
    });
});