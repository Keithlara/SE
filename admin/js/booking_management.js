document.addEventListener('DOMContentLoaded', function() {
    // Handle booking confirmation
    document.querySelectorAll('.confirm-booking-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const bookingId = this.dataset.bookingId;
            const button = this;
            
            // Show loading state
            const originalText = button.innerHTML;
            button.disabled = true;
            button.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Confirming...';
            
            // Send AJAX request
            fetch(`ajax/confirm_booking.php?booking_id=${bookingId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Update button state
                    button.innerHTML = '<i class="bi bi-check-circle-fill"></i> Confirmed';
                    button.classList.remove('btn-primary');
                    button.classList.add('btn-success');
                    
                    // Update status badge if it exists
                    const statusBadge = document.querySelector(`#booking-${bookingId}-status`);
                    if (statusBadge) {
                        statusBadge.className = 'badge bg-success';
                        statusBadge.textContent = 'Confirmed';
                    }
                    
                    // Show success message
                    showAlert('success', data.message || 'Booking confirmed successfully!');
                    
                    // Reload the page after 1.5 seconds
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    throw new Error(data.message || 'Failed to confirm booking');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                button.disabled = false;
                button.innerHTML = originalText;
                showAlert('danger', error.message || 'An error occurred while confirming the booking');
            });
        });
    });
});

// Helper function to show alerts
function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.role = 'alert';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    
    // Add to the top of the page
    const container = document.querySelector('.container-fluid') || document.querySelector('.container');
    if (container) {
        container.prepend(alertDiv);
    } else {
        document.body.prepend(alertDiv);
    }
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}
