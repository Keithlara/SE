// Base URL: allow pages in subdirectories to override (e.g. user/notifications.php sets NOTIF_BASE_OVERRIDE = '../')
const NOTIF_BASE = (typeof window.NOTIF_BASE_OVERRIDE !== 'undefined')
  ? window.NOTIF_BASE_OVERRIDE
  : (typeof APP_BASE_URL !== 'undefined' ? APP_BASE_URL : '');

const NOTIFICATION_CONFIG = {
  pollInterval: 10000,
  dropdownLimit: 5,
  retryDelay: 5000
};

let notificationState = {
  lastFetch: 0,
  isFetching: false,
  notifications: [],
  unreadCount: 0
};

// ── Init ────────────────────────────────────────────────────────────────────

document.addEventListener('DOMContentLoaded', function () {
  const hasDrop = document.getElementById('notification-dropdown');
  const hasPage = document.getElementById('notifications-list');
  if (hasDrop || hasPage) {
    initNotifications();
  }
});

function initNotifications() {
  updateNotifications();

  setInterval(function () {
    if (!notificationState.isFetching) updateNotifications();
  }, NOTIFICATION_CONFIG.pollInterval);

  const bellToggle = document.getElementById('notificationDropdown');
  if (bellToggle) {
    bellToggle.addEventListener('show.bs.dropdown', function () {
      updateNotifications();
    });
  }
}

// ── Fetch ───────────────────────────────────────────────────────────────────

function updateNotifications() {
  if (notificationState.isFetching) return;
  notificationState.isFetching = true;

  fetch(NOTIF_BASE + 'ajax/get_notifications.php')
    .then(r => r.json())
    .then(function (data) {
      notificationState.isFetching = false;
      if (data.status === 'success') {
        notificationState.notifications = data.notifications;
        notificationState.unreadCount   = data.unread_count;
        notificationState.lastFetch     = Date.now();
        renderDropdown();
        renderFullPage();
        updateBadges();
      }
    })
    .catch(function () {
      notificationState.isFetching = false;
    });
}

// ── Shared card HTML ────────────────────────────────────────────────────────

/**
 * Split a message into main text + optional admin reply.
 * Messages with a staff reply are stored as:
 *   "…booking info… | Admin reply: <reply text>"
 */
function parseMessage(rawMsg) {
  const SEP = ' | Admin reply: ';
  const idx = rawMsg.indexOf(SEP);
  if (idx !== -1) {
    return {
      main:  rawMsg.slice(0, idx),
      reply: rawMsg.slice(idx + SEP.length)
    };
  }
  return { main: rawMsg, reply: null };
}

function buildNotifItem(n, mode) {
  // Prefer notification type for icon/color, fall back to booking_status
  const notifType  = n.type || n.booking_status || 'system';
  const icon       = getNotifIcon(notifType);
  const iconClass  = getNotifIconClass(notifType);
  const unread     = !n.is_read;
  const time       = timeAgo(n.created_at);
  const statusLbl  = statusLabel(notifType);
  const statusCls  = statusBadgeClass(notifType);

  const { main, reply } = parseMessage(n.message);
  const mainHtml  = escapeHtml(main);
  const replyHtml = reply ? escapeHtml(reply) : null;

  // "View Proof" button (only on full-page mode for refund notifications with proof)
  const proofBtn = (mode === 'page' && n.refund_proof_url)
    ? `<button class="btn btn-sm btn-outline-success mt-2 shadow-none"
               onclick="event.stopPropagation(); viewRefundProof(${JSON.stringify(n.refund_proof_url)})">
         <i class="bi bi-image me-1"></i>View Refund Proof
       </button>`
    : '';

  if (mode === 'dropdown') {
    const truncated = main.length > 80 ? escapeHtml(main.slice(0, 80)) + '…' : mainHtml;
    return `
      <div class="dropdown-item notification-dropdown-item py-2 px-3 border-bottom ${unread ? 'unread' : ''}"
           data-notification-id="${n.id}"
           onclick="handleNotifClick(${n.id}, '${n.booking_status}', event)">
        <div class="d-flex align-items-center gap-2">
          <div class="notification-icon-wrapper flex-shrink-0">
            <i class="bi bi-${icon} ${iconClass} fs-5"></i>
          </div>
          <div class="flex-grow-1" style="min-width:0;">
            <div class="small ${unread ? 'fw-semibold' : ''}"
                 style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
              ${truncated}
            </div>
            <div class="d-flex align-items-center gap-2 mt-1">
              <span class="text-muted" style="font-size:0.72rem;">${time}</span>
              <span class="badge ${statusCls} notif-status-badge">${statusLbl}</span>
              ${replyHtml ? '<span class="badge bg-info text-dark notif-status-badge"><i class="bi bi-reply-fill me-1"></i>Reply</span>' : ''}
            </div>
          </div>
          ${unread ? '<span class="ms-1 flex-shrink-0" style="width:8px;height:8px;border-radius:50%;background:#0d6efd;display:inline-block;"></span>' : ''}
        </div>
      </div>`;
  }

  // page mode
  const replyBlock = replyHtml
    ? `<div class="mt-2 p-2 rounded-2" style="background:#e8f4fd;border-left:3px solid #0d6efd;">
         <div class="small fw-semibold text-primary mb-1"><i class="bi bi-reply-fill me-1"></i>Admin Reply</div>
         <div class="small" style="white-space:pre-wrap;">${replyHtml}</div>
       </div>`
    : '';

  return `
    <div class="notif-page-item ${unread ? 'unread' : ''}"
         data-notification-id="${n.id}"
         onclick="handleNotifClick(${n.id}, '${n.booking_status}', event)">
      <div class="notif-icon-circle ${iconClass.replace('text-', 'bg-opacity-10 text-')}">
        <i class="bi bi-${icon} ${iconClass}"></i>
      </div>
      <div class="notif-body">
        <div class="notif-msg ${unread ? 'fw-semibold' : ''}">${mainHtml}</div>
        ${replyBlock}
        ${proofBtn}
        <div class="notif-meta">
          <span class="notif-time"><i class="bi bi-clock me-1"></i>${time}</span>
          <span class="badge ${statusCls} notif-status-badge">${statusLbl}</span>
        </div>
      </div>
      ${unread ? '<div class="notif-unread-dot"></div>' : ''}
    </div>`;
}

// View refund proof in a lightbox modal
function viewRefundProof(url) {
  if (!url) return;
  const isPdf = /\.pdf($|\?)/i.test(url);
  const content = isPdf
    ? `<iframe src="${url}" style="width:100%;height:70vh;border:none;"></iframe>`
    : `<img src="${url}" style="max-width:100%;border-radius:8px;" alt="Refund proof">`;

  // Use a simple Bootstrap modal if SweetAlert2 isn't available on user pages
  let modal = document.getElementById('refund-proof-modal');
  if (!modal) {
    modal = document.createElement('div');
    modal.id = 'refund-proof-modal';
    modal.className = 'modal fade';
    modal.tabIndex = -1;
    modal.innerHTML = `
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title"><i class="bi bi-image me-2"></i>Refund Proof</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body text-center" id="refund-proof-body"></div>
        </div>
      </div>`;
    document.body.appendChild(modal);
  }
  document.getElementById('refund-proof-body').innerHTML = content;
  bootstrap.Modal.getOrCreateInstance(modal).show();
}

// ── Dropdown renderer ───────────────────────────────────────────────────────

function renderDropdown() {
  const el = document.getElementById('notification-dropdown');
  if (!el) return;

  const list = notificationState.notifications;

  if (list.length === 0) {
    el.innerHTML = `
      <div class="text-center py-4 text-muted">
        <i class="bi bi-bell-slash fs-1 d-block mb-2"></i>
        <small>No notifications yet</small>
      </div>`;
    return;
  }

  const shown = list.slice(0, NOTIFICATION_CONFIG.dropdownLimit);
  el.innerHTML = shown.map(n => buildNotifItem(n, 'dropdown')).join('');

  // Update "View All" link label
  const viewAll = document.querySelector('[aria-labelledby="notificationDropdown"] .dropdown-item.text-primary');
  if (viewAll) {
    const unread = notificationState.unreadCount;
    viewAll.innerHTML = unread > 0
      ? `View All <span class="badge bg-primary ms-1">${unread} unread</span>`
      : 'View All Notifications';
  }
}

// ── Full-page renderer ──────────────────────────────────────────────────────

function renderFullPage() {
  const el = document.getElementById('notifications-list');
  if (!el) return;

  const list = notificationState.notifications;

  if (list.length === 0) {
    el.innerHTML = `
      <div class="empty-state">
        <i class="bi bi-bell-slash"></i>
        <p class="mb-0">You have no notifications yet.</p>
        <small class="text-muted">Booking updates will appear here.</small>
      </div>`;
    updatePageSummary(0, 0);
    return;
  }

  el.innerHTML = list.map(n => buildNotifItem(n, 'page')).join('');
  updatePageSummary(list.length, notificationState.unreadCount);
}

function updatePageSummary(total, unread) {
  const summary = document.getElementById('notif-summary');
  if (!summary) return;
  if (total === 0) {
    summary.textContent = 'No notifications';
    return;
  }
  summary.textContent = unread > 0
    ? `${total} notification${total !== 1 ? 's' : ''} · ${unread} unread`
    : `${total} notification${total !== 1 ? 's' : ''} · all read`;

  const btn = document.getElementById('mark-all-read-btn');
  if (btn) btn.style.display = unread > 0 ? 'inline-flex' : 'none';
}

// ── Badges ──────────────────────────────────────────────────────────────────

function updateBadges() {
  const badge     = document.getElementById('notification-badge');
  const menuBadge = document.getElementById('notification-badge-menu');
  const count     = notificationState.unreadCount;

  if (badge) {
    badge.textContent = count > 9 ? '9+' : count;
    badge.style.display = count > 0 ? 'inline-block' : 'none';
    if (badge.dataset.prevCount && parseInt(badge.dataset.prevCount) < count) {
      badge.classList.add('badge-pulse');
      setTimeout(() => badge.classList.remove('badge-pulse'), 500);
    }
    badge.dataset.prevCount = count;
  }

  if (menuBadge) {
    menuBadge.textContent = count;
    menuBadge.style.display = count > 0 ? 'inline-block' : 'none';
  }
}

// ── Click handler ────────────────────────────────────────────────────────────

function handleNotifClick(notifId, status, event) {
  event.preventDefault();
  event.stopPropagation();

  markNotifRead(notifId, false);

  let target;
  switch (status) {
    case 'booked':
    case 'cancelled':
    case 'pending':
    case 'payment_pending':
      target = NOTIF_BASE + 'bookings.php';
      break;
    default:
      target = NOTIF_BASE + 'notifications.php';
  }

  // Close dropdown if open
  const bell = document.getElementById('notificationDropdown');
  if (bell) {
    const drop = bootstrap.Dropdown.getInstance(bell);
    if (drop) drop.hide();
  }

  window.location.href = target;
}

// ── Mark as read ─────────────────────────────────────────────────────────────

function markNotifRead(notifId, redirect) {
  fetch(NOTIF_BASE + 'ajax/mark_notification_read.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ action: 'mark_single', notification_id: notifId })
  })
    .then(r => r.json())
    .then(function (data) {
      if (data.status === 'success') {
        const n = notificationState.notifications.find(x => x.id === notifId);
        if (n && !n.is_read) {
          n.is_read = true;
          if (data.was_unread) {
            notificationState.unreadCount = Math.max(0, notificationState.unreadCount - 1);
          }
        }
        renderDropdown();
        renderFullPage();
        updateBadges();
      }
    })
    .catch(function (e) { console.error('markNotifRead error:', e); });
}

function markAllNotificationsAsRead() {
  const btn = document.getElementById('mark-all-read-btn');
  if (btn) {
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-check-all me-1"></i>Marking...';
  }

  fetch(NOTIF_BASE + 'ajax/mark_notification_read.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ action: 'mark_all' })
  })
    .then(r => r.json())
    .then(function (data) {
      if (data.status === 'success') {
        notificationState.notifications.forEach(n => n.is_read = true);
        notificationState.unreadCount = 0;
        renderDropdown();
        renderFullPage();
        updateBadges();
        if (btn) {
          btn.innerHTML = '<i class="bi bi-check-all me-1"></i>All Read';
          btn.classList.replace('btn-outline-primary', 'btn-success');
        }
      } else {
        if (btn) {
          btn.disabled = false;
          btn.innerHTML = '<i class="bi bi-check-all me-1"></i>Mark All as Read';
        }
      }
    })
    .catch(function () {
      if (btn) {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-check-all me-1"></i>Mark All as Read';
      }
    });
}

// ── Helpers ──────────────────────────────────────────────────────────────────

function getNotifIcon(status) {
  switch (status) {
    case 'booked':          return 'check-circle-fill';
    case 'cancelled':       return 'x-circle-fill';
    case 'pending':         return 'hourglass-split';
    case 'payment_pending': return 'credit-card';
    case 'refund':          return 'cash-stack';
    case 'system':          return 'gear-fill';
    default:                return 'info-circle-fill';
  }
}

function getNotifIconClass(status) {
  switch (status) {
    case 'booked':          return 'text-success';
    case 'cancelled':       return 'text-danger';
    case 'pending':         return 'text-warning';
    case 'payment_pending': return 'text-info';
    case 'refund':          return 'text-success';
    case 'system':          return 'text-secondary';
    default:                return 'text-primary';
  }
}

function statusLabel(status) {
  switch (status) {
    case 'booked':          return 'Confirmed';
    case 'cancelled':       return 'Cancelled';
    case 'pending':         return 'Pending';
    case 'payment_pending': return 'Payment Due';
    case 'refund':          return 'Refund';
    case 'system':          return 'System';
    default:                return status || 'Info';
  }
}

function statusBadgeClass(status) {
  switch (status) {
    case 'booked':          return 'bg-success';
    case 'cancelled':       return 'bg-danger';
    case 'pending':         return 'bg-warning text-dark';
    case 'payment_pending': return 'bg-info text-dark';
    case 'refund':          return 'bg-success';
    case 'system':          return 'bg-secondary';
    default:                return 'bg-primary';
  }
}

function escapeHtml(unsafe) {
  return String(unsafe)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;');
}

function timeAgo(dateString) {
  const date    = new Date(dateString);
  const seconds = Math.floor((Date.now() - date) / 1000);

  if (seconds < 60)  return 'just now';
  const mins = Math.floor(seconds / 60);
  if (mins < 60)     return mins + ' min' + (mins === 1 ? '' : 's') + ' ago';
  const hrs = Math.floor(mins / 60);
  if (hrs < 24)      return hrs + ' hour' + (hrs === 1 ? '' : 's') + ' ago';
  const days = Math.floor(hrs / 24);
  if (days < 30)     return days + ' day' + (days === 1 ? '' : 's') + ' ago';
  const months = Math.floor(days / 30);
  if (months < 12)   return months + ' month' + (months === 1 ? '' : 's') + ' ago';
  const years = Math.floor(months / 12);
  return years + ' year' + (years === 1 ? '' : 's') + ' ago';
}
