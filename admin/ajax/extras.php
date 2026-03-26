<?php
  require('../inc/db_config.php');
  require('../inc/essentials.php');
  adminLogin();

  // ── GET EXTRAS LIST ──
  if (isset($_POST['get_extras'])) {
    $res = mysqli_query($con, "SELECT * FROM `extras` ORDER BY `id` ASC");
    if (mysqli_num_rows($res) == 0) {
      echo "<tr><td colspan='6' class='text-center py-3 text-muted'>No extras added yet.</td></tr>";
      exit;
    }
    $i = 1;
    while ($row = mysqli_fetch_assoc($res)) {
      $status_badge = $row['status']
        ? "<span class='badge bg-success'>Active</span>"
        : "<span class='badge bg-secondary'>Inactive</span>";
      $toggle_label = $row['status'] ? 'Deactivate' : 'Activate';
      $toggle_class = $row['status'] ? 'btn-outline-secondary' : 'btn-outline-success';
      $name    = htmlspecialchars($row['name']);
      $desc    = htmlspecialchars($row['description']);
      $price   = number_format($row['price'], 2);
      echo "
      <tr>
        <td>$i</td>
        <td><strong>$name</strong></td>
        <td>₱$price / night</td>
        <td class='text-muted small'>$desc</td>
        <td>$status_badge</td>
        <td class='text-center'>
          <div class='d-flex gap-1 justify-content-center'>
            <button class='btn btn-sm btn-outline-primary shadow-none'
              onclick='openEditExtra($row[id], `$name`, $row[price], `$desc`, $row[status])'>
              <i class='bi bi-pencil'></i>
            </button>
            <button class='btn btn-sm $toggle_class shadow-none'
              onclick='toggleExtra($row[id], $row[status])'>
              $toggle_label
            </button>
            <button class='btn btn-sm btn-outline-danger shadow-none'
              onclick='deleteExtra($row[id])'>
              <i class='bi bi-trash'></i>
            </button>
          </div>
        </td>
      </tr>";
      $i++;
    }
    exit;
  }

  // ── ADD EXTRA ──
  if (isset($_POST['add_extra'])) {
    $name  = trim($_POST['name'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $desc  = trim($_POST['description'] ?? '');
    $status = intval($_POST['status'] ?? 1);
    if (!$name) { echo "0"; exit; }
    $res = insert(
      "INSERT INTO `extras` (`name`, `price`, `description`, `status`) VALUES (?,?,?,?)",
      [$name, $price, $desc, $status], 'sdsi'
    );
    echo $res ? "1" : "0";
    exit;
  }

  // ── UPDATE EXTRA ──
  if (isset($_POST['update_extra'])) {
    $id    = intval($_POST['extra_id'] ?? 0);
    $name  = trim($_POST['name'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $desc  = trim($_POST['description'] ?? '');
    $status = intval($_POST['status'] ?? 1);
    if (!$id || !$name) { echo "0"; exit; }
    $res = update(
      "UPDATE `extras` SET `name`=?, `price`=?, `description`=?, `status`=? WHERE `id`=?",
      [$name, $price, $desc, $status, $id], 'sdsii'
    );
    echo $res ? "1" : "0";
    exit;
  }

  // ── DELETE EXTRA ──
  if (isset($_POST['delete_extra'])) {
    $id = intval($_POST['extra_id'] ?? 0);
    if (!$id) { echo "0"; exit; }
    $res = mysqli_query($con, "DELETE FROM `extras` WHERE `id`=$id");
    echo $res ? "1" : "0";
    exit;
  }

  // ── TOGGLE EXTRA STATUS ──
  if (isset($_POST['toggle_extra'])) {
    $id     = intval($_POST['extra_id'] ?? 0);
    $status = intval($_POST['status'] ?? 0);
    if (!$id) { echo "0"; exit; }
    $res = update("UPDATE `extras` SET `status`=? WHERE `id`=?", [$status, $id], 'ii');
    echo $res ? "1" : "0";
    exit;
  }

  // ── GET RULES ──
  if (isset($_POST['get_rules'])) {
    $row = mysqli_fetch_assoc(mysqli_query($con, "SELECT `booking_rules` FROM `settings` WHERE `sr_no`=1 LIMIT 1"));
    echo $row['booking_rules'] ?? '';
    exit;
  }

  // ── SAVE RULES ──
  if (isset($_POST['save_rules'])) {
    $rules = trim($_POST['booking_rules'] ?? '');
    $res = update("UPDATE `settings` SET `booking_rules`=? WHERE `sr_no`=1", [$rules], 's');
    echo $res ? "1" : "0";
    exit;
  }
?>
