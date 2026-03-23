<?php
session_start();
require_once(__DIR__.'/../inc/essentials.php');
require_once(__DIR__.'/../admin/inc/db_config.php');

if(!(isset($_SESSION['login']) && $_SESSION['login'] === true) || !isset($_SESSION['uId'])){
    redirect('index.php');
}

$user_id = (int)$_SESSION['uId'];

// Get all notifications
$query = "SELECT n.*, bo.booking_status 
          FROM notifications n
          JOIN booking_order bo ON n.booking_id = bo.booking_id
          WHERE n.user_id = ? 
          ORDER BY n.created_at DESC";
$stmt = $con->prepare($query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$notifications = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Helper function for time ago
function timeAgo($dateString) {
    $date = new DateTime($dateString);
    $now = new DateTime();
    $seconds = $now->getTimestamp() - $date->getTimestamp();
    
    $interval = floor($seconds / 31536000);
    if ($interval >= 1) return $interval . ' year' . ($interval === 1 ? '' : 's') . ' ago';
    
    $interval = floor($seconds / 2592000);
    if ($interval >= 1) return $interval . ' month' . ($interval === 1 ? '' : 's') . ' ago';
    
    $interval = floor($seconds / 86400);
    if ($interval >= 1) return $interval . ' day' . ($interval === 1 ? '' : 's') . ' ago';
    
    $interval = floor($seconds / 3600);
    if ($interval >= 1) return $interval . ' hour' . ($interval === 1 ? '' : 's') . ' ago';
    
    $interval = floor($seconds / 60);
    if ($interval >= 1) return $interval . ' minute' . ($interval === 1 ? '' : 's') . ' ago';
    
    return 'just now';
}

// Helper function to get icon based on booking status
function getNotificationIcon($status) {
    switch($status) {
        case 'booked': return 'check-circle-fill';
        case 'cancelled': return 'x-circle-fill';
        case 'pending': return 'hourglass-split';
        case 'payment_pending': return 'credit-card';
        default: return 'info-circle-fill';
    }
}

// Helper function to get icon color class
function getNotificationIconClass($status) {
    switch($status) {
        case 'booked': return 'text-success';
        case 'cancelled': return 'text-danger';
        case 'pending': return 'text-warning';
        case 'payment_pending': return 'text-info';
        default: return 'text-primary';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Notifications - <?=settings('site_title')?></title>
    <?php require('inc/links.php'); ?>
    <style>
        .notification-card {
            transition: all 0.3s ease;
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        .notification-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.12);
        }
        .notification-item {
            border-radius: 12px;
            padding: 20px;
            background: #fff;
            margin-bottom: 15px;
            transition: all 0.3s ease;
        }
        .notification-item:hover {
            background-color: #f8f9fa;
        }
        .notification-unread {
            background: linear-gradient(135deg, #f8f9fc 0%, #e9ecef 100%);
            border-left: 4px solid #0d6efd;
        }
        .notification-icon {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(13, 110, 253, 0.1);
            font-size: 1.2rem;
        }
        .time-ago {
            font-size: 0.85rem;
            color: #6c757d;
            font-weight: 500;
        }
        .notification-message {
            font-size: 1rem;
            color: #333;
            line-height: 1.5;
        }
        .status-badge {
            font-size: 0.75rem;
            padding: 4px 10px;
            border-radius: 20px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }
        .empty-state i {
            font-size: 4rem;
            color: #dee2e6;
            margin-bottom: 20px;
        }
        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 0;
            margin-bottom: 40px;
            border-radius: 0 0 30px 30px;
        }
    </style>
</head>
<body class="bg-light">
    
    <?php require('inc/header.php'); ?>

    <div class="page-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="mb-0"><i class="bi bi-bell-fill me-3"></i>My Notifications</h2>
                    <p class="mb-0 opacity-75">Stay updated with your booking status and updates</p>
                </div>
                <div class="col-auto">
                    <div class="d-flex align-items-center gap-3">
                        <span class="badge bg-white text-dark fs-6 px-3 py-2 rounded-pill" id="unread-count-badge">
                            <?=count(array_filter($notifications, fn($n) => !$n['is_read']))?> Unread
                        </span>
                        <?php if (count(array_filter($notifications, fn($n) => !$n['is_read'])) > 0): ?>
                        <button id="mark-all-read-btn" class="btn btn-light rounded-pill" onclick="markAllNotificationsAsRead()">
                            <i class="bi bi-check-all me-1"></i>Mark All as Read
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container mb-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <?php if (count($notifications) > 0): ?>
                    <?php foreach ($notifications as $notification): ?>
                        <div class="notification-item <?=!$notification['is_read'] ? 'notification-unread' : ''?>" 
                             data-notification-id="<?=$notification['id']?>"
                             onclick="handleFullPageNotificationClick(<?=$notification['id']?>, '<?=$notification['booking_status']?>')">
                            <div class="d-flex align-items-start">
                                <div class="notification-icon me-3 flex-shrink-0">
                                    <i class="bi bi-<?=getNotificationIcon($notification['booking_status'])?> <?=getNotificationIconClass($notification['booking_status'])?>"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div class="notification-message">
                                            <?=htmlspecialchars($notification['message'])?>
                                        </div>
                                        <?php if(!$notification['is_read']): ?>
                                            <span class="badge bg-primary rounded-pill ms-2 flex-shrink-0">New</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                        <span class="time-ago">
                                            <i class="bi bi-clock me-1"></i><?=timeAgo($notification['created_at'])?>
                                        </span>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="status-badge bg-<?=
                                                $notification['booking_status'] === 'booked' ? 'success' : 
                                                ($notification['booking_status'] === 'cancelled' ? 'danger' : 
                                                ($notification['booking_status'] === 'pending' ? 'warning' : 'primary'))
                                            ?>">
                                                <?=ucfirst($notification['booking_status'])?>
                                            </span>
                                            <a href="booking_details.php?id=<?=$notification['booking_id']?>" class="btn btn-sm btn-outline-primary rounded-pill" onclick="event.stopPropagation()">
                                                <i class="bi bi-eye me-1"></i>View
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="notification-card">
                        <div class="empty-state">
                            <i class="bi bi-bell-slash"></i>
                            <h4 class="text-muted mb-2">No notifications yet</h4>
                            <p class="text-muted mb-4">You don't have any notifications at the moment. Book a room to get started!</p>
                            <a href="../rooms.php" class="btn btn-primary btn-lg rounded-pill px-4">
                                <i class="bi bi-search me-2"></i>Browse Rooms
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php require('inc/footer.php'); ?>
    <?php require('inc/scripts.php'); ?>
    
    <script>
    // Handle notification click on full page
    function handleFullPageNotificationClick(notificationId, status) {
        // Mark as read
        markNotificationAsRead(notificationId);
        
        // Navigate based on status
        let targetUrl = '';
        switch(status) {
            case 'booked':
            case 'cancelled':
            case 'pending':
            case 'payment_pending':
                targetUrl = `booking_details.php?id=${notificationId}`;
                break;
            default:
                targetUrl = 'notifications.php';
        }
        
        window.location.href = targetUrl;
    }
    
    // Override markAllNotificationsAsRead for full page
    function markAllNotificationsAsRead() {
        const btn = document.getElementById('mark-all-read-btn');
        const badge = document.getElementById('unread-count-badge');
        
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<i class="bi bi-check-all me-1"></i>Marking...';
        }
        
        fetch('../ajax/mark_notification_read.php', {
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
                // Update all notification items
                document.querySelectorAll('.notification-item').forEach(item => {
                    item.classList.remove('notification-unread');
                    const newBadge = item.querySelector('.badge.bg-primary');
                    if (newBadge) newBadge.remove();
                });
                
                // Update button
                if (btn) {
                    btn.innerHTML = '<i class="bi bi-check-all me-1"></i>All Read';
                    btn.classList.remove('btn-light');
                    btn.classList.add('btn-success');
                    setTimeout(() => {
                        btn.style.display = 'none';
                    }, 1500);
                }
                
                // Update badge
                if (badge) {
                    badge.innerHTML = '0 Unread';
                }
                
                // Update navbar badge
                updateNavbarBadge(0);
                
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
    
    // Helper to update navbar badge from full page
    function updateNavbarBadge(count) {
        const navBadge = document.getElementById('notification-badge');
        const menuBadge = document.getElementById('notification-badge-menu');
        
        if (navBadge) {
            navBadge.textContent = count;
            navBadge.style.display = count > 0 ? 'inline-block' : 'none';
        }
        if (menuBadge) {
            menuBadge.textContent = count;
            menuBadge.style.display = count > 0 ? 'inline-block' : 'none';
        }
    }
    
    // Show toast notification
    function showToast(message) {
        const toast = document.createElement('div');
        toast.className = 'toast-notification';
        toast.innerHTML = `
            <div class="toast-content">
                <i class="bi bi-check-circle-fill text-success me-2"></i>
                ${message}
            </div>
        `;
        document.body.appendChild(toast);
        
        setTimeout(() => toast.classList.add('show'), 100);
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
    
    // Mark single notification as read
    function markNotificationAsRead(notificationId) {
        fetch('../ajax/mark_notification_read.php', {
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
            if (data.status === 'success' && data.was_unread) {
                // Update UI for this notification
                const item = document.querySelector(`.notification-item[data-notification-id="${notificationId}"]`);
                if (item) {
                    item.classList.remove('notification-unread');
                    const badge = item.querySelector('.badge.bg-primary');
                    if (badge) badge.remove();
                }
                
                // Update unread count
                const badge = document.getElementById('unread-count-badge');
                if (badge) {
                    const current = parseInt(badge.textContent) || 0;
                    const newCount = Math.max(0, current - 1);
                    badge.innerHTML = newCount + ' Unread';
                    
                    // Hide button if no more unread
                    if (newCount === 0) {
                        const btn = document.getElementById('mark-all-read-btn');
                        if (btn) btn.style.display = 'none';
                    }
                }
                
                // Update navbar
                updateNavbarBadgeFromServer();
            }
        })
        .catch(error => {
            console.error('Error marking notification as read:', error);
        });
    }
    
    // Update navbar badge from server
    function updateNavbarBadgeFromServer() {
        fetch('../ajax/get_notifications.php')
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    updateNavbarBadge(data.unread_count);
                }
            });
    }
    
    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Initial sync with server
        updateNavbarBadgeFromServer();
    });
    </script>
</body>
</html>
