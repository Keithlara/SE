<?php
  require('inc/essentials.php');
  require('inc/db_config.php');
  adminLogin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Panel - Booking Records</title>
  <?php require('inc/links.php'); ?>
  <style>
    .records-hero {
      display: flex;
      align-items: flex-end;
      justify-content: space-between;
      gap: 16px;
      margin-bottom: 20px;
    }

    .records-title {
      margin: 0;
      font-size: 1.7rem;
      font-weight: 700;
      color: #0f172a;
      letter-spacing: 0.02em;
    }

    .records-subtitle {
      margin: 6px 0 0;
      color: #64748b;
      font-size: 0.95rem;
    }

    .records-panel {
      border-radius: 20px !important;
      overflow: hidden;
    }

    .records-toolbar {
      display: grid;
      grid-template-columns: minmax(0, 1.35fr) minmax(260px, 0.9fr);
      gap: 18px;
      margin-bottom: 20px;
      padding: 18px;
      border: 1px solid #e2e8f0;
      border-radius: 18px;
      background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
    }

    .records-filter-grid {
      display: grid;
      grid-template-columns: repeat(4, minmax(0, 1fr));
      gap: 12px;
      align-items: end;
    }

    .records-field label {
      display: block;
      margin-bottom: 6px;
      font-size: 0.78rem;
      font-weight: 700;
      letter-spacing: 0.04em;
      color: #64748b;
      text-transform: uppercase;
    }

    .records-field .form-select,
    .records-field .form-control {
      min-height: 46px;
      border-radius: 12px;
      border: 1px solid #cbd5e1;
      background: #fff;
      font-size: 0.95rem;
    }

    .records-field .form-select:focus,
    .records-field .form-control:focus {
      border-color: #3b82f6;
      box-shadow: 0 0 0 4px rgba(59,130,246,0.12);
    }

    .records-search-shell {
      display: flex;
      align-items: end;
      justify-content: flex-end;
      gap: 10px;
    }

    .records-search-shell .search-input-wrap {
      width: min(100%, 360px);
      position: relative;
    }

    .records-search-shell .search-input-wrap i {
      position: absolute;
      top: 50%;
      left: 14px;
      transform: translateY(-50%);
      color: #94a3b8;
      pointer-events: none;
    }

    .records-search-shell input {
      padding-left: 40px;
    }

    .records-clear-btn {
      min-height: 46px;
      border-radius: 12px;
      padding: 0 16px;
      font-weight: 600;
      white-space: nowrap;
    }

    .records-table-wrap {
      border: 1px solid #e2e8f0;
      border-radius: 18px;
      overflow: hidden;
      background: #fff;
    }

    .booking-records-table {
      margin: 0;
      min-width: 1180px;
    }

    .booking-records-table thead th {
      background: #1f2937 !important;
      color: #fff !important;
      font-size: 0.82rem;
      font-weight: 700;
      letter-spacing: 0.03em;
      padding: 14px 16px;
      vertical-align: middle;
      border-bottom: none;
      text-transform: uppercase;
    }

    .booking-records-table tbody td {
      padding: 16px;
      vertical-align: top;
      border-color: #e2e8f0;
    }

    .booking-records-table tbody tr:hover {
      background: #f8fbff !important;
    }

    .record-index {
      font-weight: 700;
      color: #334155;
      width: 40px;
    }

    .record-order-pill {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 6px 10px;
      border-radius: 999px;
      background: #eff6ff;
      color: #2563eb;
      font-size: 0.78rem;
      font-weight: 700;
      margin-bottom: 10px;
    }

    .record-line {
      margin: 0 0 5px;
      color: #0f172a;
      line-height: 1.35;
    }

    .record-line:last-child {
      margin-bottom: 0;
    }

    .record-line .label {
      color: #475569;
      font-weight: 700;
      margin-right: 4px;
    }

    .record-room-title {
      margin: 0 0 8px;
      font-size: 1.02rem;
      font-weight: 700;
      color: #0f172a;
    }

    .record-meta-stack {
      display: flex;
      flex-direction: column;
      gap: 8px;
    }

    .record-proof-stack {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      margin-top: 10px;
    }

    .record-proof-chip {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 5px 10px;
      border-radius: 999px;
      background: #ecfeff;
      color: #0f766e;
      font-size: 0.76rem;
      font-weight: 700;
    }

    .record-proof-chip.muted {
      background: #f1f5f9;
      color: #64748b;
    }

    .record-proof-btn {
      border-radius: 10px;
      font-weight: 700;
      padding: 6px 12px;
    }

    .record-extras {
      margin-top: 10px;
      padding: 10px 12px;
      border-radius: 12px;
      background: #f8fafc;
      border: 1px solid #e2e8f0;
    }

    .record-extras-title {
      margin: 0 0 6px;
      font-size: 0.8rem;
      font-weight: 700;
      color: #334155;
      text-transform: uppercase;
      letter-spacing: 0.04em;
    }

    .record-extras ul {
      margin: 0;
      padding-left: 18px;
      color: #475569;
      font-size: 0.83rem;
    }

    .record-amount {
      font-size: 1.18rem;
      font-weight: 700;
      color: #0f172a;
      margin-bottom: 8px;
    }

    .record-date-box {
      display: grid;
      gap: 4px;
      margin-bottom: 10px;
      color: #475569;
      font-size: 0.88rem;
    }

    .record-date-box strong {
      color: #0f172a;
    }

    .record-status {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-width: 108px;
      padding: 7px 12px;
      border-radius: 999px;
      font-size: 0.8rem;
      font-weight: 700;
      letter-spacing: 0.02em;
      text-transform: uppercase;
    }

    .record-status.booked {
      background: #dcfce7;
      color: #166534;
    }

    .record-status.cancelled {
      background: #fee2e2;
      color: #b91c1c;
    }

    .record-status.payment-failed {
      background: #fef3c7;
      color: #b45309;
    }

    .record-action-btn {
      width: 44px;
      height: 44px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      border-radius: 12px;
      font-size: 1rem;
    }

    .records-empty {
      padding: 38px 16px;
      text-align: center;
      color: #64748b;
    }

    .records-empty i {
      font-size: 2rem;
      color: #94a3b8;
      display: block;
      margin-bottom: 10px;
    }

    .records-footer {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      margin-top: 16px;
    }

    .records-meta-note {
      color: #64748b;
      font-size: 0.88rem;
      margin: 0;
    }

    #table-pagination .page-link {
      border-radius: 10px !important;
      margin-left: 6px;
      border: 1px solid #cbd5e1;
      color: #0f172a;
      min-width: 42px;
    }

    #table-pagination .page-item.disabled .page-link {
      opacity: 0.5;
    }

    @media (max-width: 1199px) {
      .records-toolbar {
        grid-template-columns: 1fr;
      }

      .records-search-shell {
        justify-content: stretch;
      }

      .records-search-shell .search-input-wrap {
        width: 100%;
      }
    }

    @media (max-width: 767px) {
      .records-hero {
        flex-direction: column;
        align-items: flex-start;
      }

      .records-filter-grid {
        grid-template-columns: 1fr;
      }

      .records-search-shell {
        flex-direction: column;
        align-items: stretch;
      }

      .records-footer {
        flex-direction: column;
        align-items: stretch;
      }

      #table-pagination {
        justify-content: flex-start !important;
      }
    }
  </style>
</head>
<body class="bg-light">

  <?php require('inc/header.php'); ?>

  <div class="container-fluid" id="main-content">
    <div class="row">
      <div class="col-lg-10 ms-auto p-4 overflow-hidden">
        <div class="records-hero">
          <div>
            <h3 class="records-title">Booking Records</h3>
            <p class="records-subtitle">Review completed stays, refunded bookings, and failed payments in one cleaner view.</p>
          </div>
        </div>

        <div class="card border-0 shadow-sm mb-4 records-panel">
          <div class="card-body">

            <div class="records-toolbar">
              <div class="records-filter-grid">
                <div class="records-field">
                  <label for="filter_month">Month</label>
                  <select id="filter_month" class="form-select shadow-none" onchange="applyFilters()">
                    <option value="">All months</option>
                    <option value="1">January</option>
                    <option value="2">February</option>
                    <option value="3">March</option>
                    <option value="4">April</option>
                    <option value="5">May</option>
                    <option value="6">June</option>
                    <option value="7">July</option>
                    <option value="8">August</option>
                    <option value="9">September</option>
                    <option value="10">October</option>
                    <option value="11">November</option>
                    <option value="12">December</option>
                  </select>
                </div>

                <div class="records-field">
                  <label for="filter_year">Year</label>
                  <select id="filter_year" class="form-select shadow-none" onchange="applyFilters()">
                    <option value="">All years</option>
                    <?php
                      $currentYear = (int)date('Y');
                      for($y = $currentYear - 3; $y <= $currentYear + 3; $y++){
                        echo "<option value=\"{$y}\">{$y}</option>";
                      }
                    ?>
                  </select>
                </div>

                <div class="records-field">
                  <label for="filter_status">Status</label>
                  <select id="filter_status" class="form-select shadow-none" onchange="applyFilters()">
                    <option value="all">All statuses</option>
                    <option value="booked">Booked</option>
                    <option value="cancelled">Cancelled</option>
                    <option value="payment_failed">Payment failed</option>
                  </select>
                </div>

                <div class="records-field">
                  <label>&nbsp;</label>
                  <button type="button" class="btn btn-outline-secondary shadow-none records-clear-btn" onclick="clearFilters()">
                    <i class="bi bi-arrow-counterclockwise me-1"></i> Clear
                  </button>
                </div>
              </div>

              <div class="records-search-shell">
                <div class="records-field search-input-wrap">
                  <label for="search_input">Search</label>
                  <i class="bi bi-search"></i>
                  <input id="search_input" type="text" class="form-control shadow-none" placeholder="Search order, guest, or phone...">
                </div>
              </div>
            </div>

            <div class="table-responsive records-table-wrap">
              <table class="table table-hover booking-records-table">
                <thead>
                  <tr>
                    <th scope="col">#</th>
                    <th scope="col">User Details</th>
                    <th scope="col">Room Details</th>
                    <th scope="col">Amount & Stay</th>
                    <th scope="col">Status</th>
                    <th scope="col">Action</th>
                  </tr>
                </thead>
                <tbody id="table-data">
                </tbody>
              </table>
            </div>

            <div class="records-footer">
              <p class="records-meta-note" id="records-meta-note">Showing recent archived and completed booking records.</p>
              <nav aria-label="Booking records pagination">
                <ul id="table-pagination" class="pagination justify-content-end mb-0">
                </ul>
              </nav>
            </div>

          </div>
        </div>

      </div>
    </div>
  </div>

  <?php require('inc/scripts.php'); ?>

  <script src="scripts/booking_records.js"></script>

</body>
</html>
