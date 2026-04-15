<?php
  require('inc/essentials.php');
  require('inc/db_config.php');
  adminLogin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Panel - Extras & Rules</title>
  <?php require('inc/links.php'); ?>
</head>
<body class="bg-light">

  <?php require('inc/header.php'); ?>

  <div class="container-fluid" id="main-content">
    <div class="row">
      <div class="col-lg-10 ms-auto p-4 overflow-hidden">
        <h3 class="mb-4">EXTRAS & BOOKING RULES</h3>

        <!-- ── ADD-ONS ── -->
        <div class="card border-0 shadow-sm mb-4">
          <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-3">
              <h5 class="card-title m-0">
                <i class="bi bi-plus-circle me-2 text-success"></i>Add-on Items
              </h5>
              <button class="btn btn-success btn-sm shadow-none" onclick="openAddExtra()">
                <i class="bi bi-plus-lg me-1"></i> Add Extra
              </button>
            </div>

            <div class="table-responsive">
              <table class="table table-hover border" style="min-width:700px;">
                <thead>
                  <tr class="bg-dark text-light">
                    <th>#</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th class="text-center">Actions</th>
                  </tr>
                </thead>
                <tbody id="extras-table"></tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- ── BOOKING RULES ── -->
        <div class="card border-0 shadow-sm mb-4">
          <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-3">
              <h5 class="card-title m-0">
                <i class="bi bi-shield-check me-2 text-warning"></i>Booking Rules &amp; Policy
              </h5>
              <button class="btn btn-warning btn-sm shadow-none text-dark" onclick="openEditRules()">
                <i class="bi bi-pencil me-1"></i> Edit Rules
              </button>
            </div>
            <div id="rules-display" class="text-muted" style="white-space:pre-line; font-size:0.9rem; line-height:1.8;"></div>
          </div>
        </div>

      </div>
    </div>
  </div>

  <!-- Add/Edit Extra Modal -->
  <div class="modal fade" id="extraModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog">
      <div class="modal-content">
        <form id="extra-form">
          <input type="hidden" id="extra_id" name="extra_id" value="">
          <div class="modal-header">
            <h5 class="modal-title" id="extra-modal-title">Add Extra</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label fw-bold">Name <span class="text-danger">*</span></label>
              <input type="text" name="name" id="extra_name" class="form-control shadow-none" placeholder="e.g. Extra Fan" required>
            </div>
            <div class="mb-3">
              <label class="form-label fw-bold">Price per night (₱) <span class="text-danger">*</span></label>
              <input type="number" name="price" id="extra_price" class="form-control shadow-none" min="0" step="0.01" placeholder="0.00" required>
            </div>
            <div class="mb-3">
              <label class="form-label fw-bold">Description</label>
              <textarea name="description" id="extra_desc" class="form-control shadow-none" rows="3" placeholder="Short description of this extra..."></textarea>
            </div>
            <div class="mb-3">
              <label class="form-label fw-bold">Status</label>
              <select name="status" id="extra_status" class="form-select shadow-none">
                <option value="1">Active (visible to guests)</option>
                <option value="0">Inactive (hidden)</option>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary shadow-none" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-success shadow-none" id="extra-submit-btn">Save</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Edit Rules Modal -->
  <div class="modal fade" id="rulesModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <form id="rules-form">
          <div class="modal-header">
            <h5 class="modal-title">Edit Booking Rules</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <p class="text-muted small">Enter each rule on a new line. These rules are displayed to guests on the booking page.</p>
            <textarea name="booking_rules" id="rules_input" class="form-control shadow-none" rows="12" placeholder="One rule per line..."></textarea>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary shadow-none" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-warning shadow-none text-dark">Save Rules</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <?php require('inc/scripts.php'); ?>
  <script>
    const extraModal = new bootstrap.Modal(document.getElementById('extraModal'));
    const rulesModal = new bootstrap.Modal(document.getElementById('rulesModal'));

    // ── LOAD EXTRAS ──
    async function loadExtras() {
      const res = await fetch('ajax/extras.php', {
        method: 'POST',
        headers: {'Content-Type':'application/x-www-form-urlencoded'},
        body: 'get_extras=1'
      });
      const html = await res.text();
      document.getElementById('extras-table').innerHTML = html;
    }

    // ── LOAD RULES ──
    async function loadRules() {
      const res = await fetch('ajax/extras.php', {
        method: 'POST',
        headers: {'Content-Type':'application/x-www-form-urlencoded'},
        body: 'get_rules=1'
      });
      const text = await res.text();
      document.getElementById('rules-display').textContent = text || 'No rules set yet.';
      document.getElementById('rules_input').value = text;
    }

    // ── OPEN ADD EXTRA ──
    function openAddExtra() {
      document.getElementById('extra-form').reset();
      document.getElementById('extra_id').value = '';
      document.getElementById('extra-modal-title').textContent = 'Add Extra';
      document.getElementById('extra-submit-btn').textContent = 'Add Extra';
      extraModal.show();
    }

    // ── OPEN EDIT EXTRA ──
    function openEditExtra(id, name, price, desc, status) {
      document.getElementById('extra_id').value = id;
      document.getElementById('extra_name').value = name;
      document.getElementById('extra_price').value = price;
      document.getElementById('extra_desc').value = desc;
      document.getElementById('extra_status').value = status;
      document.getElementById('extra-modal-title').textContent = 'Edit Extra';
      document.getElementById('extra-submit-btn').textContent = 'Save Changes';
      extraModal.show();
    }

    // ── SUBMIT EXTRA FORM ──
    document.getElementById('extra-form').addEventListener('submit', async function(e) {
      e.preventDefault();
      const fd = new FormData(this);
      const isEdit = document.getElementById('extra_id').value !== '';
      fd.append(isEdit ? 'update_extra' : 'add_extra', '1');
      const res = await fetch('ajax/extras.php', { method: 'POST', body: fd });
      const result = (await res.text()).trim();
      extraModal.hide();
      if (result === '1') {
        Swal.fire({ icon: 'success', title: isEdit ? 'Extra updated!' : 'Extra added!', timer: 1500, showConfirmButton: false });
        loadExtras();
      } else {
        Swal.fire('Error', 'Could not save. Please try again.', 'error');
      }
    });

    // ── DELETE EXTRA ──
    async function deleteExtra(id) {
      const { isConfirmed } = await Swal.fire({
        title: 'Delete this extra?',
        text: 'Guests will no longer see this option.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        confirmButtonText: 'Yes, delete'
      });
      if (!isConfirmed) return;
      const fd = new FormData();
      fd.append('delete_extra', '1');
      fd.append('extra_id', id);
      const res = await fetch('ajax/extras.php', { method: 'POST', body: fd });
      const result = (await res.text()).trim();
      if (result === '1') {
        Swal.fire({ icon: 'success', title: 'Deleted!', timer: 1200, showConfirmButton: false });
        loadExtras();
      } else {
        Swal.fire('Error', 'Could not delete.', 'error');
      }
    }

    // ── TOGGLE EXTRA STATUS ──
    async function toggleExtra(id, current) {
      const fd = new FormData();
      fd.append('toggle_extra', '1');
      fd.append('extra_id', id);
      fd.append('status', current == 1 ? 0 : 1);
      const res = await fetch('ajax/extras.php', { method: 'POST', body: fd });
      const result = (await res.text()).trim();
      if (result === '1') loadExtras();
    }

    // ── OPEN EDIT RULES ──
    function openEditRules() { rulesModal.show(); }

    // ── SUBMIT RULES ──
    document.getElementById('rules-form').addEventListener('submit', async function(e) {
      e.preventDefault();
      const fd = new FormData(this);
      fd.append('save_rules', '1');
      const res = await fetch('ajax/extras.php', { method: 'POST', body: fd });
      const result = (await res.text()).trim();
      rulesModal.hide();
      if (result === '1') {
        Swal.fire({ icon: 'success', title: 'Rules saved!', timer: 1500, showConfirmButton: false });
        loadRules();
      } else {
        Swal.fire('Error', 'Could not save rules.', 'error');
      }
    });

    document.addEventListener('DOMContentLoaded', () => { loadExtras(); loadRules(); });
  </script>

</body>
</html>
