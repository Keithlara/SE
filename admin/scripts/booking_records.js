let bookingSearchTimer = null;

async function postJSON(url, params) {
  const body = new URLSearchParams(params).toString();
  const res = await fetch(url, {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body
  });

  const text = await res.text();

  try {
    return JSON.parse(text);
  } catch (error) {
    console.error('Invalid JSON response from', url, text);
    throw new Error('Invalid server response');
  }
}

async function postFormDataJSON(url, formData) {
  const res = await fetch(url, {
    method: 'POST',
    body: formData
  });

  const text = await res.text();

  try {
    return JSON.parse(text);
  } catch (error) {
    console.error('Invalid JSON response from', url, text);
    throw new Error('Invalid server response');
  }
}

function getFilterValues() {
  const monthSelect = document.getElementById('filter_month');
  const yearSelect = document.getElementById('filter_year');
  const statusSelect = document.getElementById('filter_status');
  const searchInput = document.getElementById('search_input');

  return {
    month: monthSelect ? monthSelect.value : '',
    year: yearSelect ? yearSelect.value : '',
    status: statusSelect ? statusSelect.value : 'all',
    search: searchInput ? searchInput.value.trim() : ''
  };
}

function setLoadingState() {
  const tableData = document.getElementById('table-data');
  const pagination = document.getElementById('table-pagination');
  const meta = document.getElementById('records-meta-note');

  if (tableData) {
    tableData.innerHTML = `
      <tr>
        <td colspan="6">
          <div class="records-empty">
            <i class="bi bi-arrow-repeat"></i>
            <div class="fw-semibold text-dark mb-1">Loading booking records</div>
            <div>Please wait while we refresh the list.</div>
          </div>
        </td>
      </tr>`;
  }

  if (pagination) {
    pagination.innerHTML = '';
  }

  if (meta) {
    meta.textContent = 'Loading booking records...';
  }
}

function setErrorState() {
  const tableData = document.getElementById('table-data');
  const meta = document.getElementById('records-meta-note');

  if (tableData) {
    tableData.innerHTML = `
      <tr>
        <td colspan="6">
          <div class="records-empty">
            <i class="bi bi-exclamation-triangle"></i>
            <div class="fw-semibold text-dark mb-1">Unable to load booking records</div>
            <div>Please try again in a moment.</div>
          </div>
        </td>
      </tr>`;
  }

  if (meta) {
    meta.textContent = 'The booking records list could not be loaded.';
  }
}

function updateMeta(summary) {
  const meta = document.getElementById('records-meta-note');
  if (meta) {
    meta.textContent = summary || 'Showing recent archived and completed booking records.';
  }
}

function get_bookings(search = null, page = 1) {
  const filters = getFilterValues();
  const query = typeof search === 'string' ? search.trim() : filters.search;

  setLoadingState();

  postJSON('ajax/booking_records.php', {
    get_bookings: 1,
    search: query,
    page,
    month: filters.month,
    year: filters.year,
    status: filters.status
  })
    .then(data => {
      document.getElementById('table-data').innerHTML = data.table_data;
      document.getElementById('table-pagination').innerHTML = data.pagination;
      updateMeta(data.summary);
    })
    .catch(() => {
      setErrorState();
    });
}

function change_page(page) {
  const searchInput = document.getElementById('search_input');
  const search = searchInput ? searchInput.value : '';
  get_bookings(search, page);
}

function applyFilters() {
  get_bookings(null, 1);
}

function clearFilters() {
  const monthSelect = document.getElementById('filter_month');
  const yearSelect = document.getElementById('filter_year');
  const statusSelect = document.getElementById('filter_status');
  const searchInput = document.getElementById('search_input');

  if (monthSelect) monthSelect.value = '';
  if (yearSelect) yearSelect.value = '';
  if (statusSelect) statusSelect.value = 'all';
  if (searchInput) searchInput.value = '';

  get_bookings('', 1);
}

function handleSearchInput(value) {
  window.clearTimeout(bookingSearchTimer);
  bookingSearchTimer = window.setTimeout(() => {
    get_bookings(value, 1);
  }, 250);
}

function download(id) {
  window.location.href = 'generate_pdf.php?gen_pdf&id=' + id;
}

function archive_booking_record(id) {
  const bookingId = Number(id);
  if (!bookingId) {
    Swal.fire('Error', 'Invalid booking record selected.', 'error');
    return;
  }

  Swal.fire({
    title: 'Archive booking record?',
    text: 'This booking will be moved to Archives and can be restored later.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Yes, archive it',
    cancelButtonText: 'Cancel',
    reverseButtons: true
  }).then((result) => {
    if (!result.isConfirmed) return;

    const formData = new FormData();
    formData.append('action', 'archive_booking');
    formData.append('archive_record', '1');
    formData.append('booking_id', bookingId);

    postFormDataJSON('ajax/archive.php', formData)
      .then(data => {
        if (data.status === 'success' || data.success === true) {
          Swal.fire({
            icon: 'success',
            title: 'Archived',
            text: data.message || 'Booking archived successfully.',
            timer: 1800,
            showConfirmButton: false
          });
          get_bookings(null, 1);
          return;
        }

        Swal.fire('Error', data.message || 'Failed to archive booking record.', 'error');
      })
      .catch((error) => Swal.fire('Error', error.message || 'Failed to archive booking record.', 'error'));
  });
}

window.addEventListener('DOMContentLoaded', () => {
  const searchInput = document.getElementById('search_input');

  if (searchInput) {
    searchInput.removeAttribute('oninput');
    searchInput.addEventListener('input', event => {
      handleSearchInput(event.target.value);
    });
  }

  document.addEventListener('click', event => {
    const archiveButton = event.target.closest('.js-archive-booking-record, .archive-btn');
    if (!archiveButton) {
      return;
    }

    event.preventDefault();
    archive_booking_record(archiveButton.dataset.bookingId || archiveButton.dataset.id);
  });

  get_bookings();
});
