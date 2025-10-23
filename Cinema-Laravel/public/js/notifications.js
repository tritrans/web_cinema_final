/**
 * Modern Notification System
 * Provides easy-to-use functions for displaying notifications
 */

class NotificationManager {
    constructor() {
        this.container = null;
        this.init();
    }

    init() {
        // Create notification container if it doesn't exist
        if (!document.getElementById('notification-container')) {
            const container = document.createElement('div');
            container.id = 'notification-container';
            container.className = 'fixed top-4 right-4 z-50 space-y-2';
            document.body.appendChild(container);
        }
        this.container = document.getElementById('notification-container');
    }

    show(message, type = 'success', options = {}) {
        const {
            title = this.getDefaultTitle(type),
            duration = 5000,
            autoClose = true,
            icon = this.getDefaultIcon(type),
            onClose = null
        } = options;

        const notification = document.createElement('div');
        notification.className = `notification-item ${type}`;
        notification.setAttribute('data-auto-close', autoClose.toString());
        notification.setAttribute('data-duration', duration.toString());
        
        if (onClose) {
            notification.setAttribute('data-on-close', 'true');
        }

        notification.innerHTML = `
            <div class="notification-content">
                <div class="notification-icon">
                    <i data-lucide="${icon}" class="h-6 w-6"></i>
                </div>
                <div class="notification-message">
                    <h4 class="notification-title">${title}</h4>
                    <p class="notification-text">${message}</p>
                </div>
                <button class="notification-close" onclick="window.notificationManager.closeNotification(this)">
                    <i data-lucide="x" class="h-5 w-5"></i>
                </button>
            </div>
            <div class="notification-progress"></div>
        `;

        this.container.appendChild(notification);

        // Re-initialize Lucide icons
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }

        // Auto close
        if (autoClose) {
            setTimeout(() => {
                if (notification.parentNode) {
                    this.closeNotification(notification.querySelector('.notification-close'), onClose);
                }
            }, duration);
        }

        return notification;
    }

    closeNotification(button, onClose = null) {
        const notification = button.closest('.notification-item');
        notification.classList.add('closing');
        
        setTimeout(() => {
            if (onClose && notification.getAttribute('data-on-close') === 'true') {
                onClose();
            }
            notification.remove();
        }, 300);
    }

    getDefaultTitle(type) {
        const titles = {
            success: 'Thành công!',
            error: 'Lỗi!',
            warning: 'Cảnh báo!',
            info: 'Thông tin'
        };
        return titles[type] || 'Thông báo';
    }

    getDefaultIcon(type) {
        const icons = {
            success: 'check-circle',
            error: 'x-circle',
            warning: 'alert-triangle',
            info: 'info'
        };
        return icons[type] || 'info';
    }

    // Convenience methods
    success(message, options = {}) {
        return this.show(message, 'success', options);
    }

    error(message, options = {}) {
        return this.show(message, 'error', options);
    }

    warning(message, options = {}) {
        return this.show(message, 'warning', options);
    }

    info(message, options = {}) {
        return this.show(message, 'info', options);
    }

    // Clear all notifications
    clearAll() {
        const notifications = this.container.querySelectorAll('.notification-item');
        notifications.forEach(notification => {
            notification.classList.add('closing');
            setTimeout(() => notification.remove(), 300);
        });
    }
}

// Initialize global notification manager
window.notificationManager = new NotificationManager();

// Global convenience functions
window.showNotification = (message, type = 'success', options = {}) => {
    return window.notificationManager.show(message, type, options);
};

window.showSuccess = (message, options = {}) => {
    return window.notificationManager.success(message, options);
};

window.showError = (message, options = {}) => {
    return window.notificationManager.error(message, options);
};

window.showWarning = (message, options = {}) => {
    return window.notificationManager.warning(message, options);
};

window.showInfo = (message, options = {}) => {
    return window.notificationManager.info(message, options);
};

// AJAX error handler
document.addEventListener('DOMContentLoaded', function() {
    // Handle fetch errors globally
    const originalFetch = window.fetch;
    window.fetch = function(...args) {
        return originalFetch.apply(this, args)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                return response;
            })
            .catch(error => {
                console.error('Fetch error:', error);
                window.showError('Có lỗi xảy ra khi tải dữ liệu: ' + error.message);
                throw error;
            });
    };
});

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = NotificationManager;
}
