const BASE_URL = (typeof APP_BASE_URL !== 'undefined') ? APP_BASE_URL : '';

/**
 * Notification System
 * Unified notification handler for navbar dropdown and full notification page
 */

// Configuration
const NOTIFICATION_CONFIG = {
    pollInterval: 10000, // Poll every 10 seconds
    dropdownLimit: 5,    // Show only 5 notifications in dropdown
    retryDelay: 5000     // Retry delay on error
};

// State management
let notificationState = {
    lastFetch: 0,
    isFetching: false,
    notifications: [],
    unreadCount: 0
};

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Check if user is logged in (notification elements exist)
    if (document.getElementById('notification-badge') || document.getElementById('notification-dropdown')) {
        initNotifications();
    }
});

/**
 * Initialize notification system
 */
function initNotifications() {
    // Initial fetch
    updateNotifications();
    
    // Set up polling
    setInterval(() => {
        if (!notificationState.isFetching) {
            updateNotifications();
        }
    }, NOTIFICATION_CONFIG.pollInterval);
    
    // Setup dropdown click handler (fetch when dropdown opens)
    const bellIcon = document.getElementById('notificationDropdown');
    if (bellIcon) {
        bellIcon.addEventListener('shown.bs.dropdown', function() {
            updateNotifications();
        });
    }
}

/**
 * Fetch and update notifications
 */
function updateNotifications() {
    if (notificationState.isFetching) return;
    
    notificationState.isFetching = true;
    
    fetch(`${BASE_URL}ajax/get_notifications.php`)
        .then(response => response.json())
        .then(data => {
            notificationState.isFetching = false;
            
            if (data.status === 'success') {
                notificationState.notifications = data.notifications;
                notificationState.unreadCount = data.unread_count;
                notificationState.lastFetch = Date.now();
                
                renderNavbarNotifications();
                updateBadges();
            }
        })
        .catch(error => {
            notificationState.isFetching = false;
            console.error('Error fetching notifications:', error);
        });
}

/**
 * Render notifications in navbar dropdown
 */
function renderNavbarNotifications() {
    const dropdown = document.getElementById('notification-dropdown');
    if (!dropdown) return;
    
    const notifications = notificationState.notifications;
    
    if (notifications.length === 0) {
        dropdown.innerHTML = `
            <div class="text-center py-4">
                <i class="bi bi-bell-slash fs-1 text-muted mb-2 d-block"></i>
                <span class="text-muted small">No notifications yet</span>
            </div>
        `;
        return;
    }
    
    // Show only latest 5 for dropdown
    const dropdownNotifications = notifications.slice(0, NOTIFICATION_CONFIG.dropdownLimit);
    
    dropdown.innerHTML = dropdownNotifications.map(notification => `
        <div class="dropdown-item notification-dropdown-item py-2 px-3 border-bottom ${!notification.is_read ? 'unread' : ''}" 
             data-notification-id="${notification.id}"
             onclick="handleNotificationClick(${notification.id}, '${notification.booking_status}', event)">
            <div class="d-flex align-items-center">
                <div class="notification-icon-wrapper me-3">
                    <i class="bi bi-${getNotificationIcon(notification.booking_status)} ${getNotificationIconClass(notification.booking_status)} fs-5"></i>
                </div>
                <div class="flex-grow-1" style="min-width: 0;">
                    <div class="notification-message small ${!notification.is_read ? 'fw-semibold' : ''}" 
                         style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        ${escapeHtml(notification.message)}
                    </div>
                    <div class="text-muted" style="font-size: 0.75rem;">
                        ${timeAgo(notification.created_at)}
                    </div>
                </div>
                ${!notification.is_read ? '<span class="unread-dot ms-2"></span>' : ''}
            </div>
        </div>
    `).join('');
    
    // Update "View All" button with unread count
    const viewAllLink = document.querySelector('#notificationDropdown + .dropdown-menu .dropdown-item.text-primary');
    if (viewAllLink && notificationState.unreadCount > 0) {
        viewAllLink.innerHTML = `View All <span class="badge bg-primary ms-1">${notificationState.unreadCount} unread</span>`;
    }
}

/**
 * Update badge counts
 */
function updateBadges() {
    const badge = document.getElementById('notification-badge');
    const menuBadge = document.getElementById('notification-badge-menu');
    
    if (badge) {
        badge.textContent = notificationState.unreadCount;
        badge.style.display = notificationState.unreadCount > 0 ? 'inline-block' : 'none';
        
        // Add animation if count increased
        if (badge.dataset.prevCount && parseInt(badge.dataset.prevCount) < notificationState.unreadCount) {
            badge.classList.add('badge-pulse');
            setTimeout(() => badge.classList.remove('badge-pulse'), 500);
        }
        badge.dataset.prevCount = notificationState.unreadCount;
    }
    
    if (menuBadge) {
        menuBadge.textContent = notificationState.unreadCount;
        menuBadge.style.display = notificationState.unreadCount > 0 ? 'inline-block' : 'none';
    }
}

/**
 * Handle notification click
 */
function handleNotificationClick(notificationId, status, event) {
    event.preventDefault();
    event.stopPropagation();
    
    // Mark as read
    markNotificationAsRead(notificationId, false);
    
    // Navigate based on notification type
    let targetUrl = '';
    switch(status) {
        case 'booked':
        case 'cancelled':
        case 'pending':
        case 'payment_pending':
            targetUrl = `${BASE_URL}bookings.php`;
            break;
        default:
            targetUrl = `${BASE_URL}user/notifications.php`;
    }
    
    // Close dropdown
    const dropdown = bootstrap.Dropdown.getInstance(document.getElementById('notificationDropdown'));
    if (dropdown) dropdown.hide();
    
    // Navigate
    window.location.href = targetUrl;
}

/**
 * Mark single notification as read
 */
function markNotificationAsRead(notificationId, redirect = false) {
    fetch(`${BASE_URL}ajax/mark_notification_read.php`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            action: 'mark_single',
            notification_id: notificationId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            // Update local state
            const notification = notificationState.notifications.find(n => n.id === notificationId);
            if (notification) {
                notification.is_read = true;
            }
            if (data.was_unread) {
                notificationState.unreadCount = Math.max(0, notificationState.unreadCount - 1);
            }
            
            // Re-render
            renderNavbarNotifications();
            updateBadges();
            
            // Also update full page if on notifications page
            if (document.querySelector('.notification-item[data-notification-id]')) {
                updateNotificationItemUI(notificationId);
            }
        }
    })
    .catch(error => {
        console.error('Error marking notification as read:', error);
    });
}

/**
 * Mark all notifications as read
 */
function markAllNotificationsAsRead() {
    const btn = document.getElementById('mark-all-read-btn');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-check-all me-1"></i>Marking...';
    }
    
    fetch(`${BASE_URL}ajax/mark_notification_read.php`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            action: 'mark_all'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            // Update all notifications in state
            notificationState.notifications.forEach(n => n.is_read = true);
            notificationState.unreadCount = 0;
            
            // Update UI
            renderNavbarNotifications();
            updateBadges();
            
            // Update full page
            document.querySelectorAll('.notification-item').forEach(item => {
                item.classList.remove('notification-unread');
                const badge = item.querySelector('.badge.bg-primary');
                if (badge) badge.remove();
            });
            
            // Update button
            if (btn) {
                btn.innerHTML = '<i class="bi bi-check-all me-1"></i>All Read';
                btn.classList.add('btn-success');
                btn.classList.remove('btn-outline-primary');
            }
            
            // Show toast
            showToast(`${data.marked_count} notification(s) marked as read`);
        }
    })
    .catch(error => {
        console.error('Error marking all as read:', error);
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-check-all me-1"></i>Mark All as Read';
        }
    });
}

/**
 * Update UI for a single notification item on full page
 */
function updateNotificationItemUI(notificationId) {
    const item = document.querySelector(`.notification-item[data-notification-id="${notificationId}"]`);
    if (item) {
        item.classList.remove('notification-unread');
        item.style.background = '#fff';
        const badge = item.querySelector('.badge.bg-primary');
        if (badge) badge.remove();
    }
}

/**
 * Show toast notification
 */
function showToast(message) {
    const toast = document.createElement('div');
    toast.className = 'toast-notification';
    toast.innerHTML = `
        <div class="toast-content">
            <i class="bi bi-check-circle-fill text-success me-2"></i>
            ${escapeHtml(message)}
        </div>
    `;
    document.body.appendChild(toast);
    
    setTimeout(() => toast.classList.add('show'), 100);
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// Helper functions

function getNotificationIcon(status) {
    switch(status) {
        case 'booked': return 'check-circle-fill';
        case 'cancelled': return 'x-circle-fill';
        case 'pending': return 'hourglass-split';
        case 'payment_pending': return 'credit-card';
        case 'refund': return 'cash-stack';
        case 'system': return 'gear-fill';
        default: return 'info-circle-fill';
    }
}

function getNotificationIconClass(status) {
    switch(status) {
        case 'booked': return 'text-success';
        case 'cancelled': return 'text-danger';
        case 'pending': return 'text-warning';
        case 'payment_pending': return 'text-info';
        case 'refund': return 'text-success';
        case 'system': return 'text-secondary';
        default: return 'text-primary';
    }
}

function escapeHtml(unsafe) {
    return unsafe
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

function timeAgo(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const seconds = Math.floor((now - date) / 1000);
    
    if (seconds < 60) return 'just now';
    
    const minutes = Math.floor(seconds / 60);
    if (minutes < 60) return minutes + ' min' + (minutes === 1 ? '' : 's') + ' ago';
    
    const hours = Math.floor(minutes / 60);
    if (hours < 24) return hours + ' hour' + (hours === 1 ? '' : 's') + ' ago';
    
    const days = Math.floor(hours / 24);
    if (days < 30) return days + ' day' + (days === 1 ? '' : 's') + ' ago';
    
    const months = Math.floor(days / 30);
    if (months < 12) return months + ' month' + (months === 1 ? '' : 's') + ' ago';
    
    const years = Math.floor(months / 12);
    return years + ' year' + (years === 1 ? '' : 's') + ' ago';
}
