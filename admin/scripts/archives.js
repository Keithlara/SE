// Global variables
let currentPage = {
  bookings: 1,
  rooms: 1,
  users: 1,
  queries: 1
};

const limit = 10;
let currentTab = 'bookings';
let currentRoomId = null;

// Change active tab and load data
function changeArchiveType(type) {
  currentTab = type;
  get_archives(type);
}

// Helper function to make POST requests
async function postForm(url, params, isFormData = false) {
  let options = {
    method: 'POST',
    headers: {}
  };

  if (isFormData) {
    options.body = params;
  } else {
    options.headers['Content-Type'] = 'application/x-www-form-urlencoded';
    options.body = new URLSearchParams(params).toString();
  }

  const res = await fetch(url, options);
  const text = await res.text();

  try {
    return JSON.parse(text);
  } catch (err) {
    console.error('Invalid JSON response from', url, '->', text);
    throw new Error('Invalid JSON returned from server');
  }
}

// Collect filters based on active tab
function collectFilters(type) {
  const baseFilters = {
    page: currentPage[type] || 1,
    limit: limit,
    type: type
  };

  // Add tab-specific filters
  switch(type) {
    case 'bookings':
      return {
        ...baseFilters,
        search: document.getElementById('search_bookings')?.value || '',
        date_from: document.getElementById('date_from_bookings')?.value || '',
        date_to: document.getElementById('date_to_bookings')?.value || '',
        guest: document.getElementById('guest_bookings')?.value || '',
        room_type: document.getElementById('room_type_bookings')?.value || ''
      };
    case 'rooms':
      return {
        ...baseFilters,
        search: document.getElementById('search_rooms')?.value || ''
      };
    // Add cases for other types as needed
    default:
      return baseFilters;
  }
}

// Load archives for the specified type
function get_archives(type = currentTab) {
  const loadingElement = document.getElementById(`${type}-data`);
  if (loadingElement) {
    loadingElement.innerHTML = '<tr><td colspan="10" class="text-center"><div class="spinner-border" role="status"></div> Loading...</td></tr>';
  }

  postForm('ajax/archive.php', { 
    action: 'list_archives',
    ...collectFilters(type)
  })
  .then(data => {
    if (data.status === 'success') {
      // Update the specific tab's content
      const container = document.getElementById(`${type}-data`);
      const pagination = document.getElementById(`${type}-pagination`);
      
      if (container) container.innerHTML = data.html || '<tr><td colspan="10" class="text-center">No records found</td></tr>';
      if (pagination) pagination.innerHTML = data.pagination || '';
      
      // Initialize tooltips for the loaded content
      if (typeof bootstrap !== 'undefined') {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
          return new bootstrap.Tooltip(tooltipTriggerEl);
        });
      }
    } else {
      throw new Error(data.message || 'Failed to load data');
    }
  })
  .catch(error => {
    console.error('Error loading archives:', error);
    const container = document.getElementById(`${type}-data`);
    if (container) {
      container.innerHTML = `<tr><td colspan="10" class="text-center text-danger">Error loading data: ${error.message}</td></tr>`;
    }
  });
}

// Permanently delete an archived item
function permanentDelete(id, type) {
  const itemName = type === 'room' ? 'room'
                  : type === 'user' ? 'user'
                  : type === 'booking' ? 'booking'
                  : 'item';

  Swal.fire({
    title: `Delete ${itemName}?`,
    text: `This will permanently delete the ${itemName} from archives. This action cannot be undone.`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Yes, delete permanently',
    cancelButtonText: 'Cancel',
    reverseButtons: true
  }).then((result) => {
    if (result.isConfirmed) {
      const formData = new FormData();
      formData.append('action', 'delete');
      formData.append('type', type);
      formData.append('id', id);

      Swal.fire({
        title: 'Deleting...',
        text: `Please wait while we delete the ${itemName}`,
        allowOutsideClick: false,
        didOpen: () => {
          Swal.showLoading();
        }
      });

      postForm('ajax/archive.php', formData, true)
      .then(data => {
        Swal.close();
        if (data.status === 'success') {
          Swal.fire({
            icon: 'success',
            title: 'Deleted!',
            text: data.message || `${itemName.charAt(0).toUpperCase() + itemName.slice(1)} has been deleted permanently.`,
            timer: 2000,
            showConfirmButton: false
          });
          get_archives(currentTab);
        } else {
          throw new Error(data.message || `Failed to delete ${itemName}`);
        }
      })
      .catch(error => {
        console.error(`Error deleting ${itemName}:`, error);
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: error.message || `An error occurred while deleting the ${itemName}`
        });
      });
    }
  });
}

// Change page for the current tab
function change_page(page) {
  currentPage[currentTab] = page;
  get_archives(currentTab);
}

// Restore an archived item
function restore(id, type = 'booking') {
  const itemName = type === 'room' ? 'room'
                  : type === 'user' ? 'user'
                  : type === 'booking' ? 'booking'
                  : 'item';
  
  Swal.fire({
    title: `Restore ${itemName}?`,
    text: `Are you sure you want to restore this ${itemName}? This will make it active again.`,
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Yes, restore it!',
    cancelButtonText: 'Cancel',
    reverseButtons: true
  }).then((result) => {
    if (result.isConfirmed) {
      const formData = new FormData();
      formData.append('action', 'restore');
      formData.append('type', type);
      formData.append('id', id);
      
      // Show loading state
      Swal.fire({
        title: 'Restoring...',
        text: `Please wait while we restore the ${itemName}`,
        allowOutsideClick: false,
        didOpen: () => {
          Swal.showLoading();
        }
      });

      postForm('ajax/archive.php', formData, true)
      .then(data => {
        Swal.close();
        if (data.status === 'success') {
          // Close the modal if it's open
          const modal = bootstrap.Modal.getInstance(document.getElementById('roomDetailsModal'));
          if (modal) {
            modal.hide();
          }
          
          Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: data.message || `${itemName.charAt(0).toUpperCase() + itemName.slice(1)} has been restored successfully.`,
            timer: 2000,
            showConfirmButton: false
          });
          
          // Refresh the current tab
          get_archives(currentTab);
        } else {
          throw new Error(data.message || `Failed to restore ${itemName}`);
        }
      })
      .catch(error => {
        console.error(`Error restoring ${itemName}:`, error);
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: error.message || `An error occurred while restoring the ${itemName}`
        });
      });
    }
  });
}

// Helper function to show alert messages
function alert(type, message) {
  const alertHtml = `
    <div class="alert alert-${type} alert-dismissible fade show" role="alert">
      ${message}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  `;
  
  // Create or update alert container
  let alertContainer = document.getElementById('alertContainer');
  if (!alertContainer) {
    alertContainer = document.createElement('div');
    alertContainer.id = 'alertContainer';
    alertContainer.style.position = 'fixed';
    alertContainer.style.top = '80px';
    alertContainer.style.right = '20px';
    alertContainer.style.zIndex = '1100';
    alertContainer.style.maxWidth = '400px';
    document.body.appendChild(alertContainer);
  }
  
  // Add new alert
  const alertElement = document.createElement('div');
  alertElement.innerHTML = alertHtml;
  alertContainer.appendChild(alertElement);
  
  // Auto-remove alert after 5 seconds
  setTimeout(() => {
    const bsAlert = new bootstrap.Alert(alertElement.querySelector('.alert'));
    bsAlert.close();
    
    // Remove the alert element after animation
    setTimeout(() => {
      if (alertContainer.contains(alertElement)) {
        alertContainer.removeChild(alertElement);
      }
      
      // Remove container if no more alerts
      if (alertContainer.children.length === 0) {
        document.body.removeChild(alertContainer);
      }
    }, 150);
  }, 5000);
}

// Initialize the page
window.addEventListener('DOMContentLoaded', () => {
  // Load initial data for the active tab
  get_archives(currentTab);
  
  // Set up event listeners for tab changes
  document.querySelectorAll('[data-bs-toggle="tab"]').forEach(tab => {
    tab.addEventListener('shown.bs.tab', event => {
      const tabId = event.target.getAttribute('aria-controls');
      if (tabId && ['bookings', 'rooms', 'users', 'queries'].includes(tabId)) {
        currentTab = tabId;
        get_archives(tabId);
      }
    });
  });
  
  // Initialize room details modal events
  const roomModal = document.getElementById('roomDetailsModal');
  if (roomModal) {
    roomModal.addEventListener('hidden.bs.modal', function () {
      // Reset modal content when hidden
      document.getElementById('carouselIndicators').innerHTML = '';
      document.getElementById('carouselItems').innerHTML = `
        <div class="text-center py-5">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
          <p class="mt-2">Loading room details...</p>
        </div>`;
    });
  }
  
  // Initialize tooltips
  const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
  tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
  });
});


