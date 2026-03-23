// Helper function to make POST requests
async function postForm(url, params, isJson = false) {
    try {
        const body = new URLSearchParams(params).toString();
        const res = await fetch(url, { 
            method: 'POST', 
            headers: { 
                'Content-Type': 'application/x-www-form-urlencoded' 
            }, 
            body 
        });
        
        return isJson ? await res.json() : await res.text();
    } catch (error) {
        console.error('Request failed:', error);
        throw error;
    }
}

// Load bookings with optional search
async function get_bookings(search = '') {
    try {
        const tbody = document.getElementById('table-data');
        tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>';
        
        const html = await postForm('ajax/refund_bookings.php', { 
            get_bookings: 1, 
            search: search || '' 
        });
        
        tbody.innerHTML = html || '<tr><td colspan="5" class="text-center py-4">No refund requests found.</td></tr>';
    } catch (error) {
        console.error('Error loading bookings:', error);
        alert('error', 'Failed to load bookings. Please try again.');
    }
}

// Process refund for a booking
async function refund_booking(bookingId, refundAmount, button) {
    if (!confirm(`Are you sure you want to process a refund of ₱${refundAmount.toLocaleString('en-PH', { minimumFractionDigits: 2 })} for this booking?`)) {
        return;
    }
    
    const originalButtonHTML = button.innerHTML;
    button.disabled = true;
    button.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';
    
    try {
        const result = await postForm('ajax/refund_bookings.php', { 
            refund_booking: 1,
            booking_id: bookingId,
            refund_amount: refundAmount
        });
        
        if (result === '1') {
            // Show success message
            const toast = document.createElement('div');
            toast.className = 'position-fixed bottom-0 end-0 p-3';
            toast.style.zIndex = '11';
            toast.innerHTML = `
                <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="toast-header bg-success text-white">
                        <strong class="me-auto">Success</strong>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                    <div class="toast-body">
                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                        Refund of ₱${refundAmount.toLocaleString('en-PH', { minimumFractionDigits: 2 })} processed successfully!
                    </div>
                </div>
            `;
            document.body.appendChild(toast);
            
            // Remove toast after 5 seconds
            setTimeout(() => {
                toast.remove();
            }, 5000);
            
            // Refresh the bookings list
            get_bookings(document.querySelector('input[type="search"]')?.value || '');
        } else {
            throw new Error('Failed to process refund');
        }
    } catch (error) {
        console.error('Error processing refund:', error);
        alert('error', 'Failed to process refund. Please try again.');
        button.disabled = false;
        button.innerHTML = originalButtonHTML;
    }
}

// Initialize page
function initPage() {
    // Add event listener for search input
    const searchInput = document.querySelector('input[type="search"]');
    if (searchInput) {
        let searchTimeout;
        searchInput.addEventListener('input', (e) => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                get_bookings(e.target.value);
            }, 500);
        });
    }
    
    // Initial load
    get_bookings();
}

// Run when DOM is fully loaded
document.addEventListener('DOMContentLoaded', initPage);