<?php 

  require('../inc/db_config.php');
  require('../inc/essentials.php');
  adminLogin();

  @mysqli_query($con, "ALTER TABLE `archived_user_cred` ADD COLUMN `username` varchar(100) DEFAULT NULL AFTER `email`");
  @mysqli_query($con, "ALTER TABLE `archived_user_cred` ADD COLUMN `verification_code` varchar(255) DEFAULT NULL AFTER `is_verified`");

  // Ensure archive flag exists (fresh DB safety)
  try {
    $col = mysqli_query($con, "SHOW COLUMNS FROM `user_cred` LIKE 'is_archived'");
    if ($col && mysqli_num_rows($col) === 0) {
      mysqli_query($con, "ALTER TABLE `user_cred` ADD COLUMN `is_archived` TINYINT(1) NOT NULL DEFAULT 0 AFTER `status`");
    }
  } catch (Throwable $e) { }

  // Helper: render user rows HTML
  function render_user_rows($res) {
    $i    = 1;
    $path = USERS_IMG_PATH;
    $data = "";

    while ($row = mysqli_fetch_assoc($res)) {
      $verified = "<span class='badge bg-warning'><i class='bi bi-x-lg'></i> Unverified</span>";
      if ($row['is_verified']) {
        $verified = "<span class='badge bg-success'><i class='bi bi-check-lg'></i> Verified</span>";
      }

      $status_btn = "<button onclick='toggle_status($row[id],0)' class='btn btn-success btn-sm shadow-none'>Active</button>";
      if (!$row['status']) {
        $status_btn = "<button onclick='toggle_status($row[id],1)' class='btn btn-secondary btn-sm shadow-none'>Inactive</button>";
      }

      $profile_src = !empty($row['profile'])
        ? $path . $row['profile']
        : $path . 'default.png';

      $date = date("d-m-Y", strtotime($row['datentime']));

      $data .= "
        <tr>
          <td>$i</td>
          <td>
            <img src='$profile_src' width='45px' height='45px' class='rounded-circle object-fit-cover'>
            <div class='fw-semibold mt-1'>" . htmlspecialchars($row['name']) . "</div>
          </td>
          <td>" . htmlspecialchars($row['email']) . "</td>
          <td>" . htmlspecialchars($row['phonenum']) . "</td>
          <td>" . htmlspecialchars($row['address']) . " | " . htmlspecialchars($row['pincode']) . "</td>
          <td>" . htmlspecialchars($row['dob']) . "</td>
          <td>$verified</td>
          <td>$status_btn</td>
          <td>$date</td>
          <td>
            <button type='button' onclick='archive_user($row[id])' class='btn btn-warning shadow-none btn-sm' title='Archive user'>
              <i class='bi bi-archive-fill'></i>
            </button>
          </td>
        </tr>
      ";
      $i++;
    }

    return $data ?: "<tr><td colspan='10' class='text-center text-muted py-3'>No users found</td></tr>";
  }

  if (isset($_POST['get_users'])) {
    $res = select("SELECT * FROM `user_cred` WHERE `is_archived` = 0 ORDER BY `datentime` DESC", [], '');
    echo render_user_rows($res);
  }

  if (isset($_POST['toggle_status'])) {
    $frm_data = filteration($_POST);
    $q = "UPDATE `user_cred` SET `status`=? WHERE `id`=?";
    $v = [$frm_data['value'], $frm_data['toggle_status']];
    echo update($q, $v, 'ii') ? 1 : 0;
  }

  // Archive any user (verified or unverified)
  if (isset($_POST['archive_user'])) {
    $user_id = (int)$_POST['user_id'];

    if ($user_id <= 0) { echo 0; exit; }

    // Get user data
    $get = select("SELECT * FROM `user_cred` WHERE `id`=? AND `is_archived`=0 LIMIT 1", [$user_id], 'i');
    if (!$get || mysqli_num_rows($get) !== 1) { echo 0; exit; }
    $row = mysqli_fetch_assoc($get);

    // Ensure archived_user_cred table exists
    $create = "CREATE TABLE IF NOT EXISTS `archived_user_cred` (
      `id` int(11) NOT NULL,
      `name` varchar(100) NOT NULL,
      `email` varchar(150) NOT NULL,
      `username` varchar(100) DEFAULT NULL,
      `address` varchar(120) NOT NULL,
      `phonenum` varchar(100) NOT NULL,
      `pincode` int(11) NOT NULL,
      `dob` date NOT NULL,
      `password` varchar(200) NOT NULL,
      `is_verified` int(11) NOT NULL DEFAULT 0,
      `verification_code` varchar(255) DEFAULT NULL,
      `token` varchar(200) DEFAULT NULL,
      `t_expire` date DEFAULT NULL,
      `datentime` datetime NOT NULL DEFAULT current_timestamp(),
      `status` int(11) NOT NULL DEFAULT 1,
      `profile` varchar(100) DEFAULT NULL,
      `archived_at` datetime NOT NULL DEFAULT current_timestamp(),
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    if (!mysqli_query($con, $create)) { echo 0; exit; }
    @mysqli_query($con, "ALTER TABLE `archived_user_cred` ADD COLUMN `username` varchar(100) DEFAULT NULL AFTER `email`");
    @mysqli_query($con, "ALTER TABLE `archived_user_cred` ADD COLUMN `verification_code` varchar(255) DEFAULT NULL AFTER `is_verified`");

    // Check if already in archive (avoid duplicate)
    $chk = select("SELECT `id` FROM `archived_user_cred` WHERE `id`=? LIMIT 1", [$user_id], 'i');
    if ($chk && mysqli_num_rows($chk) > 0) {
      // Already archived — just mark in live table
      archiveRefreshUserChildren($user_id);
      archiveDeleteLiveUserChildren($user_id);
      update("UPDATE `user_cred` SET `is_archived`=1, `status`=0 WHERE `id`=?", [$user_id], 'i');
      echo 1; exit;
    }

    // Insert into archive
    $ins = insert(
      "INSERT INTO `archived_user_cred`
       (`id`,`name`,`email`,`username`,`address`,`phonenum`,`pincode`,`dob`,
        `password`,`is_verified`,`verification_code`,`token`,`t_expire`,`datentime`,`status`,`profile`)
       VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)",
      [
        $row['id'], $row['name'], $row['email'], $row['username'] ?? null, $row['address'], $row['phonenum'],
        $row['pincode'], $row['dob'], $row['password'], $row['is_verified'],
        $row['verification_code'] ?? null, $row['token'], $row['t_expire'], $row['datentime'], $row['status'], $row['profile']
      ],
      'isssssississssis'
    );

    if (!$ins) { echo 0; exit; }

    archiveRefreshUserChildren($user_id);
    archiveDeleteLiveUserChildren($user_id);

    // Mark as archived in live table
    $upd = update("UPDATE `user_cred` SET `is_archived`=1, `status`=0 WHERE `id`=?", [$user_id], 'i');
    if ($upd) {
      logAction('archive_user', "Archived user id={$user_id}, email={$row['email']}");
      echo 1;
    } else {
      echo 0;
    }
  }

  // Legacy remove_user — now just calls archive logic
  if (isset($_POST['remove_user'])) {
    $frm_data = filteration($_POST);
    $_POST['archive_user'] = 1;
    $_POST['user_id'] = $frm_data['user_id'];
    // Re-invoke the archive block above by redirecting logic
    $user_id = (int)$frm_data['user_id'];
    if ($user_id <= 0) { echo 0; exit; }
    $get = select("SELECT * FROM `user_cred` WHERE `id`=? AND `is_archived`=0 LIMIT 1", [$user_id], 'i');
    if (!$get || mysqli_num_rows($get) !== 1) { echo 0; exit; }
    $row = mysqli_fetch_assoc($get);
    $ins = insert(
      "INSERT IGNORE INTO `archived_user_cred`
       (`id`,`name`,`email`,`username`,`address`,`phonenum`,`pincode`,`dob`,
        `password`,`is_verified`,`verification_code`,`token`,`t_expire`,`datentime`,`status`,`profile`)
       VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)",
      [
        $row['id'], $row['name'], $row['email'], $row['username'] ?? null, $row['address'], $row['phonenum'],
        $row['pincode'], $row['dob'], $row['password'], $row['is_verified'],
        $row['verification_code'] ?? null, $row['token'], $row['t_expire'], $row['datentime'], $row['status'], $row['profile']
      ],
      'isssssississssis'
    );
    archiveRefreshUserChildren($user_id);
    archiveDeleteLiveUserChildren($user_id);
    $upd = update("UPDATE `user_cred` SET `is_archived`=1, `status`=0 WHERE `id`=?", [$user_id], 'i');
    echo ($ins && $upd) ? 1 : 0;
  }

  if (isset($_POST['search_user'])) {
    $frm_data = filteration($_POST);
    $res = select(
      "SELECT * FROM `user_cred` WHERE `is_archived` = 0 AND `name` LIKE ? ORDER BY `datentime` DESC",
      ["%" . $frm_data['name'] . "%"], 's'
    );
    echo render_user_rows($res);
  }

?>
