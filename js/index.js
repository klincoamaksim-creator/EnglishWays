// ========== ИНИЦИАЛИЗАЦИЯ AOS ==========
AOS.init({
    duration: 800,
    once: true,
    offset: 100
});

// ========== ПЛАВНАЯ ПРОКРУТКА ДЛЯ ЯКОРЕЙ ==========
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        const targetId = this.getAttribute('href');
        if (targetId === "#" || targetId === "") return;
        const targetElement = document.querySelector(targetId);
        if (targetElement) {
            e.preventDefault();
            targetElement.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// ========== ПОИСК (клик по поиску - переход на страницу материалов) ==========
const searchArea = document.querySelector('.search-area');
if (searchArea) {
    searchArea.addEventListener('click', function() {
        window.location.href = 'materials.php';
    });
}

// ========== ЗАГРУЗКА ПОСЛЕДНИХ МАТЕРИАЛОВ (демо-данные, потом заменим на Firebase) ==========
const demoMaterials = [
    {
        id: 1,
        title: 'Времена английского глагола',
        category: 'Грамматика',
        description: 'Полный разбор всех 12 времён с примерами и упражнениями',
        icon: 'fa-clock'
    },
    {
        id: 2,
        title: '100 самых нужных слов',
        category: 'Лексика',
        description: 'Базовая лексика для повседневного общения',
        icon: 'fa-book'
    },
    {
        id: 3,
        title: 'Диалоги на каждый день',
        category: 'Разговорный',
        description: 'Практика разговорной речи: кафе, аэропорт, отель',
        icon: 'fa-comments'
    }
];

function loadLatestMaterials() {
    const container = document.getElementById('latestMaterials');
    if (!container) return;

    demoMaterials.forEach(material => {
        const card = document.createElement('div');
        card.className = 'material-preview-card';
        card.setAttribute('data-material-id', material.id);
        card.innerHTML = `
            <div class="material-preview-image">
                <i class="fas ${material.icon}"></i>
            </div>
            <div class="material-preview-body">
                <span class="material-preview-category">${material.category}</span>
                <h3>${material.title}</h3>
                <p>${material.description}</p>
            </div>
        `;
        card.addEventListener('click', function() {
            window.location.href = `material.php?id=${material.id}`;
        });
        container.appendChild(card);
    });
}

// ========== ЗАПУСК ПРИ ЗАГРУЗКЕ ==========
document.addEventListener('DOMContentLoaded', function() {
    loadLatestMaterials();
});