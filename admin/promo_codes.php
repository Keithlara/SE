<?php
  require('inc/essentials.php');
  require('inc/db_config.php');
  adminLogin();
  requireAdminPermission('promos.manage');

  $message = '';
  $message_type = 'success';

  if (isset($_POST['save_promo'])) {
    $code = strtoupper(trim((string)($_POST['code'] ?? '')));
    $description = trim((string)($_POST['description'] ?? ''));
    $discount_type = trim((string)($_POST['discount_type'] ?? 'percent'));
    $discount_value = (float)($_POST['discount_value'] ?? 0);
    $min_amount = (float)($_POST['min_amount'] ?? 0);
    $max_discount = (float)($_POST['max_discount'] ?? 0);
    $start_date = trim((string)($_POST['start_date'] ?? ''));
    $end_date = trim((string)($_POST['end_date'] ?? ''));
    $usage_limit = (int)($_POST['usage_limit'] ?? 0);
    $promo_id = (int)($_POST['promo_id'] ?? 0);

    if ($code === '' || $discount_value <= 0 || !in_array($discount_type, ['percent', 'fixed'], true)) {
      $message = 'Please fill in the required promo fields.';
      $message_type = 'error';
    } else {
      if ($promo_id > 0) {
        update(
          "UPDATE `promo_codes`
           SET `code`=?, `description`=?, `discount_type`=?, `discount_value`=?, `min_amount`=?, `max_discount`=?, `start_date`=?, `end_date`=?, `usage_limit`=?, `is_active`=?
           WHERE `id`=?",
          [$code, sanitizeMultilineText($description, 255), $discount_type, $discount_value, $min_amount, $max_discount, $start_date ?: null, $end_date ?: null, $usage_limit, !empty($_POST['is_active']) ? 1 : 0, $promo_id],
          'sssdddssiii'
        );
        $message = 'Promo code updated.';
      } else {
        insert(
          "INSERT INTO `promo_codes`
           (`code`,`description`,`discount_type`,`discount_value`,`min_amount`,`max_discount`,`start_date`,`end_date`,`usage_limit`,`used_count`,`is_active`,`created_by`)
           VALUES (?,?,?,?,?,?,?,?,?,0,?,?)",
          [$code, sanitizeMultilineText($description, 255), $discount_type, $discount_value, $min_amount, $max_discount, $start_date ?: null, $end_date ?: null, $usage_limit, !empty($_POST['is_active']) ? 1 : 0, (int)($_SESSION['adminId'] ?? 0)],
          'sssdddssiii'
        );
        $message = 'Promo code created.';
      }
    }
  }

  if (isset($_POST['toggle_promo'])) {
    $promo_id = (int)($_POST['promo_id'] ?? 0);
    $active = !empty($_POST['make_active']) ? 1 : 0;
    update("UPDATE `promo_codes` SET `is_active`=? WHERE `id`=?", [$active, $promo_id], 'ii');
    $message = $active ? 'Promo activated.' : 'Promo deactivated.';
  }

  $promo_rows = [];
  $promo_res = mysqli_query($con, "SELECT * FROM `promo_codes` ORDER BY `created_at` DESC, `id` DESC");
  while ($promo_res && $row = mysqli_fetch_assoc($promo_res)) {
    $promo_rows[] = $row;
  }

  $edit_promo = null;
  if (!empty($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    foreach ($promo_rows as $row) {
      if ((int)$row['id'] === $edit_id) {
        $edit_promo = $row;
        break;
      }
    }
  }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Panel - Promo Codes</title>
  <?php require('inc/links.php'); ?>
  <style>
    .promo-card, .promo-form-card { border-radius: 18px; border: 1px solid rgba(148,163,184,0.18); background: rgba(255,255,255,0.95); box-shadow: 0 14px 30px rgba(15,23,42,0.05); }
    .promo-code-pill { display: inline-flex; padding: 7px 12px; border-radius: 999px; background: rgba(var(--admin-accent-rgb),0.12); color: var(--admin-accent); font-weight: 700; letter-spacing: 0.04em; }
  </style>
</head>
<body class="bg-light">
  <?php require('inc/header.php'); ?>

  <div class="container-fluid" id="main-content">
    <div class="p-4">
      <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
        <div>
          <h3 class="mb-1">Promo Codes</h3>
          <p class="text-muted mb-0">Create discounts cleanly without overcrowding the booking flow.</p>
        </div>
      </div>

      <?php if ($message !== '') { alert($message_type, $message); } ?>

      <div class="row g-4">
        <div class="col-lg-4">
          <div class="promo-form-card p-4">
            <h5 class="mb-3"><?php echo $edit_promo ? 'Edit Promo' : 'Create Promo'; ?></h5>
            <form method="POST">
              <input type="hidden" name="promo_id" value="<?php echo (int)($edit_promo['id'] ?? 0); ?>">
              <div class="mb-2">
                <label class="form-label">Promo Code</label>
                <input type="text" class="form-control shadow-none" name="code" value="<?php echo htmlspecialchars($edit_promo['code'] ?? ''); ?>" placeholder="SUMMER10" required>
              </div>
              <div class="mb-2">
                <label class="form-label">Description</label>
                <textarea class="form-control shadow-none" name="description" rows="2" placeholder="What does this promo apply to?"><?php echo htmlspecialchars($edit_promo['description'] ?? ''); ?></textarea>
              </div>
              <div class="row g-2">
                <div class="col-md-6">
                  <label class="form-label">Discount Type</label>
                  <select class="form-select shadow-none" name="discount_type">
                    <option value="percent" <?php echo (($edit_promo['discount_type'] ?? 'percent') === 'percent') ? 'selected' : ''; ?>>Percent</option>
                    <option value="fixed" <?php echo (($edit_promo['discount_type'] ?? '') === 'fixed') ? 'selected' : ''; ?>>Fixed Amount</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Value</label>
                  <input type="number" step="0.01" class="form-control shadow-none" name="discount_value" value="<?php echo htmlspecialchars($edit_promo['discount_value'] ?? ''); ?>" required>
                </div>
              </div>
              <div class="row g-2 mt-1">
                <div class="col-md-6">
                  <label class="form-label">Minimum Amount</label>
                  <input type="number" step="0.01" class="form-control shadow-none" name="min_amount" value="<?php echo htmlspecialchars($edit_promo['min_amount'] ?? '0'); ?>">
                </div>
                <div class="col-md-6">
                  <label class="form-label">Max Discount</label>
                  <input type="number" step="0.01" class="form-control shadow-none" name="max_discount" value="<?php echo htmlspecialchars($edit_promo['max_discount'] ?? '0'); ?>">
                </div>
              </div>
              <div class="row g-2 mt-1">
                <div class="col-md-6">
                  <label class="form-label">Start Date</label>
                  <input type="date" class="form-control shadow-none" name="start_date" value="<?php echo htmlspecialchars($edit_promo['start_date'] ?? ''); ?>">
                </div>
                <div class="col-md-6">
                  <label class="form-label">End Date</label>
                  <input type="date" class="form-control shadow-none" name="end_date" value="<?php echo htmlspecialchars($edit_promo['end_date'] ?? ''); ?>">
                </div>
              </div>
              <div class="mt-2">
                <label class="form-label">Usage Limit</label>
                <input type="number" class="form-control shadow-none" name="usage_limit" value="<?php echo htmlspecialchars($edit_promo['usage_limit'] ?? '0'); ?>">
              </div>
              <div class="form-check mt-3">
                <input class="form-check-input" type="checkbox" name="is_active" value="1" <?php echo !isset($edit_promo['is_active']) || !empty($edit_promo['is_active']) ? 'checked' : ''; ?>>
                <label class="form-check-label">Active</label>
              </div>
              <button type="submit" class="btn btn-primary shadow-none mt-3 w-100" name="save_promo"><?php echo $edit_promo ? 'Update Promo' : 'Save Promo'; ?></button>
            </form>
          </div>
        </div>
        <div class="col-lg-8">
          <div class="d-flex flex-column gap-3">
            <?php foreach ($promo_rows as $promo): ?>
              <div class="promo-card p-4">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                  <div>
                    <span class="promo-code-pill"><?php echo htmlspecialchars($promo['code']); ?></span>
                    <div class="mt-2 fw-semibold"><?php echo htmlspecialchars($promo['description'] ?: 'No description'); ?></div>
                    <div class="small text-muted mt-2">
                      <?php echo $promo['discount_type'] === 'percent' ? number_format((float)$promo['discount_value'], 2) . '% off' : 'PHP ' . number_format((float)$promo['discount_value'], 2) . ' off'; ?>
                      • Minimum PHP <?php echo number_format((float)$promo['min_amount'], 2); ?>
                      <?php if ((float)$promo['max_discount'] > 0): ?> • Max PHP <?php echo number_format((float)$promo['max_discount'], 2); ?><?php endif; ?>
                    </div>
                    <div class="small text-muted mt-1">
                      <?php echo $promo['start_date'] ? date('M d, Y', strtotime($promo['start_date'])) : 'Starts immediately'; ?>
                      -
                      <?php echo $promo['end_date'] ? date('M d, Y', strtotime($promo['end_date'])) : 'No expiry'; ?>
                      • Used <?php echo (int)$promo['used_count']; ?>/<?php echo (int)$promo['usage_limit']; ?>
                    </div>
                  </div>
                  <div class="text-end">
                    <span class="badge <?php echo !empty($promo['is_active']) ? 'bg-success' : 'bg-secondary'; ?>"><?php echo !empty($promo['is_active']) ? 'Active' : 'Inactive'; ?></span>
                    <div class="d-flex gap-2 mt-3">
                      <a class="btn btn-outline-primary shadow-none btn-sm" href="?edit=<?php echo (int)$promo['id']; ?>">Edit</a>
                      <form method="POST">
                        <input type="hidden" name="promo_id" value="<?php echo (int)$promo['id']; ?>">
                        <input type="hidden" name="make_active" value="<?php echo !empty($promo['is_active']) ? '0' : '1'; ?>">
                        <button class="btn btn-outline-secondary shadow-none btn-sm" type="submit" name="toggle_promo"><?php echo !empty($promo['is_active']) ? 'Pause' : 'Activate'; ?></button>
                      </form>
                    </div>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
            <?php if (empty($promo_rows)): ?>
              <div class="promo-card p-4 text-muted">No promo codes yet.</div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>

  <?php require('inc/scripts.php'); ?>
</body>
</html>
