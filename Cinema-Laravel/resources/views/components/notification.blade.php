@props(['type' => 'success', 'message' => '', 'autoClose' => true, 'duration' => 5000])

<div id="notification-container" class="fixed top-4 right-4 z-50 space-y-2">
    @if(session('success'))
        <div class="notification-item success" data-auto-close="{{ $autoClose ? 'true' : 'false' }}" data-duration="{{ $duration }}">
            <div class="notification-content">
                <div class="notification-icon">
                    <i data-lucide="check-circle" class="h-6 w-6"></i>
                </div>
                <div class="notification-message">
                    <h4 class="notification-title">Thành công!</h4>
                    <p class="notification-text">{{ session('success') }}</p>
                </div>
                <button class="notification-close" onclick="closeNotification(this)">
                    <i data-lucide="x" class="h-5 w-5"></i>
                </button>
            </div>
            <div class="notification-progress"></div>
        </div>
    @endif

    @if(session('error'))
        <div class="notification-item error" data-auto-close="{{ $autoClose ? 'true' : 'false' }}" data-duration="{{ $duration }}">
            <div class="notification-content">
                <div class="notification-icon">
                    <i data-lucide="x-circle" class="h-6 w-6"></i>
                </div>
                <div class="notification-message">
                    <h4 class="notification-title">Lỗi!</h4>
                    <p class="notification-text">{{ session('error') }}</p>
                </div>
                <button class="notification-close" onclick="closeNotification(this)">
                    <i data-lucide="x" class="h-5 w-5"></i>
                </button>
            </div>
            <div class="notification-progress"></div>
        </div>
    @endif

    @if(session('warning'))
        <div class="notification-item warning" data-auto-close="{{ $autoClose ? 'true' : 'false' }}" data-duration="{{ $duration }}">
            <div class="notification-content">
                <div class="notification-icon">
                    <i data-lucide="alert-triangle" class="h-6 w-6"></i>
                </div>
                <div class="notification-message">
                    <h4 class="notification-title">Cảnh báo!</h4>
                    <p class="notification-text">{{ session('warning') }}</p>
                </div>
                <button class="notification-close" onclick="closeNotification(this)">
                    <i data-lucide="x" class="h-5 w-5"></i>
                </button>
            </div>
            <div class="notification-progress"></div>
        </div>
    @endif

    @if(session('info'))
        <div class="notification-item info" data-auto-close="{{ $autoClose ? 'true' : 'false' }}" data-duration="{{ $duration }}">
            <div class="notification-content">
                <div class="notification-icon">
                    <i data-lucide="info" class="h-6 w-6"></i>
                </div>
                <div class="notification-message">
                    <h4 class="notification-title">Thông tin</h4>
                    <p class="notification-text">{{ session('info') }}</p>
                </div>
                <button class="notification-close" onclick="closeNotification(this)">
                    <i data-lucide="x" class="h-5 w-5"></i>
                </button>
            </div>
            <div class="notification-progress"></div>
        </div>
    @endif
</div>

<style>
.notification-item {
    @apply relative rounded-xl shadow-2xl border-l-4 min-w-80 max-w-md transform transition-all duration-300 ease-in-out;
    animation: slideInRight 0.3s ease-out, pulseGlow 2s ease-in-out;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25), 0 0 0 1px rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(20px);
    background: rgba(255, 255, 255, 0.95);
}

.notification-item.success {
    @apply border-green-600 bg-gradient-to-r from-green-100 to-green-200;
    border-left-color: #059669;
    border-left-width: 8px;
    box-shadow: 0 25px 50px -12px rgba(5, 150, 105, 0.3), 0 0 0 1px rgba(5, 150, 105, 0.2);
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(5, 150, 105, 0.15) 100%);
}

.notification-item.error {
    @apply border-red-600 bg-gradient-to-r from-red-100 to-red-200;
    border-left-color: #dc2626;
    border-left-width: 8px;
    box-shadow: 0 25px 50px -12px rgba(220, 38, 38, 0.3), 0 0 0 1px rgba(220, 38, 38, 0.2);
    background: linear-gradient(135deg, rgba(239, 68, 68, 0.1) 0%, rgba(220, 38, 38, 0.15) 100%);
}

.notification-item.warning {
    @apply border-yellow-600 bg-gradient-to-r from-yellow-100 to-yellow-200;
    border-left-color: #d97706;
    border-left-width: 8px;
    box-shadow: 0 25px 50px -12px rgba(217, 119, 6, 0.3), 0 0 0 1px rgba(217, 119, 6, 0.2);
    background: linear-gradient(135deg, rgba(245, 158, 11, 0.1) 0%, rgba(217, 119, 6, 0.15) 100%);
}

.notification-item.info {
    @apply border-blue-600 bg-gradient-to-r from-blue-100 to-blue-200;
    border-left-color: #2563eb;
    border-left-width: 8px;
    box-shadow: 0 25px 50px -12px rgba(37, 99, 235, 0.3), 0 0 0 1px rgba(37, 99, 235, 0.2);
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(37, 99, 235, 0.15) 100%);
}

.notification-content {
    @apply flex items-start p-4;
}

.notification-icon {
    @apply flex-shrink-0 mr-3 p-2 rounded-full;
}

.notification-icon i {
    @apply h-6 w-6;
}

.notification-item.success .notification-icon {
    @apply bg-green-600 shadow-lg;
    box-shadow: 0 4px 14px 0 rgba(5, 150, 105, 0.4);
}

.notification-item.success .notification-icon i {
    @apply text-white;
    filter: drop-shadow(0 1px 2px rgba(0, 0, 0, 0.1));
}

.notification-item.error .notification-icon {
    @apply bg-red-600 shadow-lg;
    box-shadow: 0 4px 14px 0 rgba(220, 38, 38, 0.4);
}

.notification-item.error .notification-icon i {
    @apply text-white;
    filter: drop-shadow(0 1px 2px rgba(0, 0, 0, 0.1));
}

.notification-item.warning .notification-icon {
    @apply bg-yellow-600 shadow-lg;
    box-shadow: 0 4px 14px 0 rgba(217, 119, 6, 0.4);
}

.notification-item.warning .notification-icon i {
    @apply text-white;
    filter: drop-shadow(0 1px 2px rgba(0, 0, 0, 0.1));
}

.notification-item.info .notification-icon {
    @apply bg-blue-600 shadow-lg;
    box-shadow: 0 4px 14px 0 rgba(37, 99, 235, 0.4);
}

.notification-item.info .notification-icon i {
    @apply text-white;
    filter: drop-shadow(0 1px 2px rgba(0, 0, 0, 0.1));
}

.notification-message {
    @apply flex-1 min-w-0;
}

.notification-title {
    @apply text-lg font-black mb-2;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
}

.notification-item.success .notification-title {
    @apply text-green-900;
    color: #064e3b;
}

.notification-item.error .notification-title {
    @apply text-red-900;
    color: #7f1d1d;
}

.notification-item.warning .notification-title {
    @apply text-yellow-900;
    color: #78350f;
}

.notification-item.info .notification-title {
    @apply text-blue-900;
    color: #1e3a8a;
}

.notification-text {
    @apply text-base leading-relaxed font-semibold;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}

.notification-item.success .notification-text {
    @apply text-green-800;
    color: #065f46;
}

.notification-item.error .notification-text {
    @apply text-red-800;
    color: #991b1b;
}

.notification-item.warning .notification-text {
    @apply text-yellow-800;
    color: #92400e;
}

.notification-item.info .notification-text {
    @apply text-blue-800;
    color: #1e40af;
}

.notification-close {
    @apply flex-shrink-0 ml-3 p-2 rounded-full transition-all duration-200 hover:scale-110;
}

.notification-item.success .notification-close {
    @apply hover:bg-green-200;
}

.notification-item.error .notification-close {
    @apply hover:bg-red-200;
}

.notification-item.warning .notification-close {
    @apply hover:bg-yellow-200;
}

.notification-item.info .notification-close {
    @apply hover:bg-blue-200;
}

.notification-close i {
    @apply h-5 w-5;
}

.notification-item.success .notification-close i {
    @apply text-green-600 hover:text-green-800;
}

.notification-item.error .notification-close i {
    @apply text-red-600 hover:text-red-800;
}

.notification-item.warning .notification-close i {
    @apply text-yellow-600 hover:text-yellow-800;
}

.notification-item.info .notification-close i {
    @apply text-blue-600 hover:text-blue-800;
}

.notification-progress {
    @apply absolute bottom-0 left-0 h-2 rounded-b-xl;
    animation: progressBar linear;
}

.notification-item.success .notification-progress {
    @apply bg-gradient-to-r from-green-500 to-green-600;
}

.notification-item.error .notification-progress {
    @apply bg-gradient-to-r from-red-500 to-red-600;
}

.notification-item.warning .notification-progress {
    @apply bg-gradient-to-r from-yellow-500 to-yellow-600;
}

.notification-item.info .notification-progress {
    @apply bg-gradient-to-r from-blue-500 to-blue-600;
}

@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

@keyframes slideOutRight {
    from {
        transform: translateX(0);
        opacity: 1;
    }
    to {
        transform: translateX(100%);
        opacity: 0;
    }
}

@keyframes progressBar {
    from {
        width: 100%;
    }
    to {
        width: 0%;
    }
}

@keyframes pulseGlow {
    0%, 100% {
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25), 0 0 0 1px rgba(255, 255, 255, 0.1);
        transform: scale(1);
    }
    50% {
        box-shadow: 0 35px 70px -12px rgba(0, 0, 0, 0.35), 0 0 0 2px rgba(255, 255, 255, 0.2);
        transform: scale(1.02);
    }
}

.notification-item.closing {
    animation: slideOutRight 0.3s ease-in-out forwards;
}

/* Mobile responsive */
@media (max-width: 640px) {
    .notification-item {
        @apply min-w-72 max-w-sm;
    }
    
    #notification-container {
        @apply top-2 right-2 left-2;
    }
}
</style>

<script>
function closeNotification(button) {
    const notification = button.closest('.notification-item');
    notification.classList.add('closing');
    
    setTimeout(() => {
        notification.remove();
    }, 300);
}

// Auto close notifications
document.addEventListener('DOMContentLoaded', function() {
    const notifications = document.querySelectorAll('.notification-item[data-auto-close="true"]');
    
    notifications.forEach(notification => {
        const duration = parseInt(notification.dataset.duration) || 5000;
        
        // Start progress bar animation
        const progressBar = notification.querySelector('.notification-progress');
        if (progressBar) {
            progressBar.style.animationDuration = duration + 'ms';
        }
        
        // Auto close after duration
        setTimeout(() => {
            if (notification.parentNode) {
                closeNotification(notification.querySelector('.notification-close'));
            }
        }, duration);
    });
});

// Global notification functions
window.showNotification = function(message, type = 'success', duration = 5000) {
    const container = document.getElementById('notification-container');
    if (!container) return;
    
    const notification = document.createElement('div');
    notification.className = `notification-item ${type}`;
    notification.setAttribute('data-auto-close', 'true');
    notification.setAttribute('data-duration', duration);
    
    const icons = {
        success: 'check-circle',
        error: 'x-circle',
        warning: 'alert-triangle',
        info: 'info'
    };
    
    const titles = {
        success: 'Thành công!',
        error: 'Lỗi!',
        warning: 'Cảnh báo!',
        info: 'Thông tin'
    };
    
    notification.innerHTML = `
        <div class="notification-content">
            <div class="notification-icon">
                <i data-lucide="${icons[type]}" class="h-6 w-6"></i>
            </div>
            <div class="notification-message">
                <h4 class="notification-title">${titles[type]}</h4>
                <p class="notification-text">${message}</p>
            </div>
            <button class="notification-close" onclick="closeNotification(this)">
                <i data-lucide="x" class="h-5 w-5"></i>
            </button>
        </div>
        <div class="notification-progress"></div>
    `;
    
    container.appendChild(notification);
    
    // Re-initialize Lucide icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
    
    // Auto close
    setTimeout(() => {
        if (notification.parentNode) {
            closeNotification(notification.querySelector('.notification-close'));
        }
    }, duration);
};
</script>
