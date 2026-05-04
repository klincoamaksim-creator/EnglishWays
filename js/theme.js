// ========== ПЕРЕКЛЮЧЕНИЕ ТЕМЫ ==========
const themeToggle = document.getElementById('themeToggle');
const body = document.body;

// Проверяем сохранённую тему
const savedTheme = getCookie('theme');
if (savedTheme === 'dark') {
    body.classList.add('dark-theme');
    updateThemeIcon(true);
}

// Клик по кнопке
if (themeToggle) {
    themeToggle.addEventListener('click', function() {
        const isDark = body.classList.contains('dark-theme');

        if (isDark) {
            body.classList.remove('dark-theme');
            setCookie('theme', 'light', 365);
            updateThemeIcon(false);
        } else {
            body.classList.add('dark-theme');
            setCookie('theme', 'dark', 365);
            updateThemeIcon(true);
        }
    });
}

// Обновление иконки
function updateThemeIcon(isDark) {
    const icon = themeToggle.querySelector('i');
    if (isDark) {
        icon.className = 'fas fa-sun';
        themeToggle.style.color = '#ef4444';
    } else {
        icon.className = 'fas fa-moon';
        themeToggle.style.color = '';
    }
}

// Куки
function setCookie(name, value, days) {
    const date = new Date();
    date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
    document.cookie = name + '=' + value + ';expires=' + date.toUTCString() + ';path=/';
}

function getCookie(name) {
    const nameEQ = name + '=';
    const ca = document.cookie.split(';');
    for (let i = 0; i < ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) === ' ') c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
    }
    return null;
}