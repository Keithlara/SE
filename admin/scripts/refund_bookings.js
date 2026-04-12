async function get_bookings(search = '') {
    try {
        const tbody = document.getElementById('table-data');
        tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>';

        const res = await fetch('ajax/refund_bookings.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'get_bookings=1&search=' + encodeURIComponent(search || '')
        });
        const html = await res.text();
        tbody.innerHTML = html || '<tr><td colspan="5" class="text-center py-4">No refund requests found.</td></tr>';
    } catch (e) {
        console.error('Error loading bookings:', e);
    }
}

async function refund_booking(bookingId, refundAmount, button) {
    const formatted = '₱' + parseFloat(refundAmount).toLocaleString('en-PH', { minimumFractionDigits: 2 });

    const { value: confirmed } = await Swal.fire({
        title: 'Process Refund',
        html: `
            <div class="text-start">
                <div class="mb-3 p-3 bg-light rounded-3">
                    <p class="mb-1">Booking <strong>#${bookingId}</strong></p>
                    <p class="mb-0">Refund amount: <strong class="text-success">${formatted}</strong></p>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        <i class="bi bi-image me-1"></i>Upload Refund Proof
                        <span class="text-muted fw-normal">(Required — photo/screenshot of payment)</span>
                    </label>
                    <input type="file" id="refund-proof-file" class="form-control" accept="image/*,.pdf">
                    <div class="form-text">Accepted: JPG, PNG, GIF, WebP, PDF (max 5 MB)</div>
                    <div id="proof-preview" class="mt-2"></div>
                </div>
            </div>
        `,
        icon: false,
        showCancelButton: true,
        confirmButtonText: '<i class="bi bi-cash-stack me-1"></i>Confirm Refund',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#198754',
        cancelButtonColor: '#6c757d',
        reverseButtons: true,
        width: '520px',
        didOpen: () => {
            const fileInput = document.getElementById('refund-proof-file');
            const preview = document.getElementById('proof-preview');
            fileInput.addEventListener('change', function () {
                preview.innerHTML = '';
                const file = this.files[0];
                if (!file) return;
                if (file.type.startsWith('image/')) {
                    const img = document.createElement('img');
                    img.className = 'img-fluid rounded shadow-sm';
                    img.style.maxHeight = '160px';
                    img.src = URL.createObjectURL(file);
                    preview.appendChild(img);
                } else {
                    preview.innerHTML = `<div class="text-muted small"><i class="bi bi-file-earmark-pdf text-danger me-1"></i>${file.name}</div>`;
                }
            });
        },
        preConfirm: () => {
            const fileInput = document.getElementById('refund-proof-file');
            if (!fileInput.files || !fileInput.files[0]) {
                Swal.showValidationMessage('Please upload a proof of refund image.');
                return false;
            }
            const file = fileInput.files[0];
            if (file.size > 5 * 1024 * 1024) {
                Swal.showValidationMessage('File is too large. Maximum size is 5 MB.');
                return false;
            }
            return file;
        }
    });

    if (!confirmed) return;

    const originalHTML = button.innerHTML;
    button.disabled = true;
    button.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Processing...';

    try {
        const formData = new FormData();
        formData.append('refund_booking', '1');
        formData.append('booking_id', bookingId);
        formData.append('refund_amount', refundAmount);
        formData.append('refund_proof', confirmed);

        const res = await fetch('ajax/refund_bookings.php', { method: 'POST', body: formData });
        const result = await res.text();

        if (result.trim() === '1') {
            await Swal.fire({
                title: 'Refund Processed!',
                text: `Refund of ${formatted} has been processed and the guest has been notified.`,
                icon: 'success',
                confirmButtonColor: '#198754'
            });
            get_bookings(document.querySelector('input[type="text"]')?.value || '');
        } else if (result.trim() === 'already_refunded') {
            await Swal.fire({
                title: 'Already Processed',
                text: 'This refund has already been marked as completed.',
                icon: 'info',
                confirmButtonColor: '#0d6efd'
            });
            get_bookings(document.querySelector('input[type="text"]')?.value || '');
        } else {
            throw new Error('Unexpected response: ' + result);
        }
    } catch (e) {
        console.error('Refund error:', e);
        Swal.fire('Error', 'Failed to process refund. Please try again.', 'error');
        button.disabled = false;
        button.innerHTML = originalHTML;
    }
}

function viewRefundProof(url) {
    if (!url) { Swal.fire('No Proof', 'No refund proof has been uploaded.', 'info'); return; }
    const isPdf = /\.pdf($|\?)/i.test(url);
    Swal.fire({
        title: 'Refund Proof',
        html: isPdf
            ? `<iframe src="${url}" class="w-100" style="height:65vh;" frameborder="0"></iframe>`
            : `<img src="${url}" class="img-fluid rounded shadow-sm" alt="Refund proof">`,
        width: '700px',
        showCloseButton: true,
        showConfirmButton: false
    });
}

async function get_processed(search = '') {
    try {
        const tbody = document.getElementById('table-data-processed');
        tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4"><div class="spinner-border text-success" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>';

        const res = await fetch('ajax/refund_bookings.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'get_processed=1&search=' + encodeURIComponent(search || '')
        });
        const html = await res.text();
        tbody.innerHTML = html || '<tr><td colspan="5" class="text-center py-4">No processed refunds found.</td></tr>';
    } catch (e) {
        console.error('Error loading processed bookings:', e);
    }
}

async function upload_proof_only(bookingId, button) {
    const { value: file } = await Swal.fire({
        title: 'Upload Refund Proof',
        html: `
            <div class="text-start">
                <p class="text-muted small mb-2">Upload a screenshot or photo showing you sent the refund for <strong>Booking #${bookingId}</strong>.</p>
                <label class="form-label fw-semibold"><i class="bi bi-image me-1"></i>Select Image / PDF</label>
                <input type="file" id="proof-only-file" class="form-control" accept="image/*,.pdf">
                <div class="form-text">Accepted: JPG, PNG, GIF, WebP, PDF (max 5 MB)</div>
                <div id="proof-only-preview" class="mt-2"></div>
            </div>
        `,
        icon: false,
        showCancelButton: true,
        confirmButtonText: '<i class="bi bi-upload me-1"></i>Upload',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#0d6efd',
        cancelButtonColor: '#6c757d',
        reverseButtons: true,
        width: '480px',
        didOpen: () => {
            const fileInput = document.getElementById('proof-only-file');
            const preview = document.getElementById('proof-only-preview');
            fileInput.addEventListener('change', function () {
                preview.innerHTML = '';
                const f = this.files[0];
                if (!f) return;
                if (f.type.startsWith('image/')) {
                    const img = document.createElement('img');
                    img.className = 'img-fluid rounded shadow-sm';
                    img.style.maxHeight = '150px';
                    img.src = URL.createObjectURL(f);
                    preview.appendChild(img);
                } else {
                    preview.innerHTML = `<div class="text-muted small"><i class="bi bi-file-earmark-pdf text-danger me-1"></i>${f.name}</div>`;
                }
            });
        },
        preConfirm: () => {
            const fileInput = document.getElementById('proof-only-file');
            if (!fileInput.files || !fileInput.files[0]) {
                Swal.showValidationMessage('Please select a file to upload.');
                return false;
            }
            const f = fileInput.files[0];
            if (f.size > 5 * 1024 * 1024) {
                Swal.showValidationMessage('File too large. Maximum is 5 MB.');
                return false;
            }
            return f;
        }
    });

    if (!file) return;

    const origHTML = button.innerHTML;
    button.disabled = true;
    button.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

    try {
        const formData = new FormData();
        formData.append('upload_proof_only', '1');
        formData.append('booking_id', bookingId);
        formData.append('refund_proof', file);

        const res = await fetch('ajax/refund_bookings.php', { method: 'POST', body: formData });
        const result = (await res.text()).trim();

        if (result === '1') {
            await Swal.fire({
                title: 'Proof Uploaded!',
                text: 'The refund proof has been saved and is now visible to the guest.',
                icon: 'success',
                confirmButtonColor: '#0d6efd'
            });
            // Refresh whichever tab is active
            const search = document.getElementById('search-box')?.value || '';
            if (document.getElementById('panel-pending').classList.contains('d-none')) {
                get_processed(search);
            } else {
                get_bookings(search);
            }
        } else {
            throw new Error('Upload failed: ' + result);
        }
    } catch (e) {
        console.error('Upload error:', e);
        Swal.fire('Error', 'Failed to upload proof. Please try again.', 'error');
        button.disabled = false;
        button.innerHTML = origHTML;
    }
}

document.addEventListener('DOMContentLoaded', () => get_bookings());
