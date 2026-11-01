/**
 * Câmara Digital - Scripts JavaScript Principais
 */

// Toast Notifications
function showToast(type, message) {
    const container = document.getElementById('toast-container');
    if (!container) return;

    const toast = document.createElement('div');
    const colors = {
        success: 'bg-emerald-600 text-white',
        error: 'bg-rose-600 text-white',
        warning: 'bg-amber-600 text-white',
        info: 'bg-sky-600 text-white'
    };

    const icons = {
        success: 'fa-check-circle',
        error: 'fa-circle-xmark',
        warning: 'fa-triangle-exclamation',
        info: 'fa-circle-info'
    };

    toast.className = `flex items-center gap-3 px-4 py-3 rounded-lg shadow-lg text-sm font-medium transition-all duration-300 transform translate-y-2 opacity-0 ${colors[type] || colors.info}`;
    toast.innerHTML = `
        <i class="fa-solid ${icons[type] || icons.info} text-base"></i>
        <span>${message}</span>
        <button onclick="this.parentElement.remove()" class="ml-auto opacity-70 hover:opacity-100 text-xs">
            <i class="fa-solid fa-xmark"></i>
        </button>
    `;

    container.appendChild(toast);

    requestAnimationFrame(() => {
        toast.classList.remove('translate-y-2', 'opacity-0');
    });

    setTimeout(() => {
        toast.classList.add('opacity-0', 'translate-y-2');
        setTimeout(() => toast.remove(), 300);
    }, 4500);
}

// Modal Management
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;
    modal.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;
    modal.classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
}

// Filter Table dynamically
function filterTable(inputId, tableId) {
    const input = document.getElementById(inputId);
    const table = document.getElementById(tableId);
    if (!input || !table) return;

    const filter = input.value.toLowerCase();
    const rows = table.getElementsByTagName('tr');

    for (let i = 1; i < rows.length; i++) {
        const row = rows[i];
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
    }
}

// Global Quick Search (Command/Ctrl + K)
document.addEventListener('keydown', (e) => {
    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
        e.preventDefault();
        openModal('quick-search-modal');
        const input = document.getElementById('global-search-input');
        if (input) setTimeout(() => input.focus(), 100);
    }
});

// Auto-dismiss Flash notifications
document.addEventListener('DOMContentLoaded', () => {
    const flashEl = document.getElementById('flash-message');
    if (flashEl) {
        setTimeout(() => {
            flashEl.style.transition = 'opacity 0.5s ease';
            flashEl.style.opacity = '0';
            setTimeout(() => flashEl.remove(), 500);
        }, 5000);
    }

    // Restaura preferências de acessibilidade
    if (localStorage.getItem('accessibility_contrast') === 'high') {
        document.documentElement.classList.add('high-contrast');
    }
    const savedFontSize = localStorage.getItem('accessibility_font_size');
    if (savedFontSize) {
        document.documentElement.style.fontSize = savedFontSize;
    }
});

// Acessibilidade: Alto Contraste (eMAG / WCAG 2.1 AA)
function toggleHighContrast() {
    const html = document.documentElement;
    html.classList.toggle('high-contrast');
    const isHigh = html.classList.contains('high-contrast');
    localStorage.setItem('accessibility_contrast', isHigh ? 'high' : 'normal');
}

// Acessibilidade: Controle de Tamanho de Fonte
let currentFontSizePercent = 100;
function changeFontSize(direction) {
    currentFontSizePercent += direction * 10;
    if (currentFontSizePercent < 80) currentFontSizePercent = 80;
    if (currentFontSizePercent > 140) currentFontSizePercent = 140;
    const sizeStr = currentFontSizePercent + '%';
    document.documentElement.style.fontSize = sizeStr;
    localStorage.setItem('accessibility_font_size', sizeStr);
}

function resetFontSize() {
    currentFontSizePercent = 100;
    document.documentElement.style.fontSize = '100%';
    localStorage.removeItem('accessibility_font_size');
}
