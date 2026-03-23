<?php 

  require('../inc/db_config.php');
  require('../inc/essentials.php');
  adminLogin();

  // Ensure archive flag exists (fresh DB safety)
  try {
    $col = mysqli_query($con, "SHOW COLUMNS FROM `user_cred` LIKE 'is_archived'");
    if ($col && mysqli_num_rows($col) === 0) {
      mysqli_query($con, "ALTER TABLE `user_cred` ADD COLUMN `is_archived` TINYINT(1) NOT NULL DEFAULT 0 AFTER `status`");
    }
  } catch (Throwable $e) {
    // If this fails, queries below may still work on older schemas
  }

  if(isset($_POST['get_users']))
  {
    // Only show non-archived users in the active Users module
    $res = select("SELECT * FROM `user_cred` WHERE `is_archived` = 0", [], '');    
    $i=1;
    $path = USERS_IMG_PATH;

    $data = "";

    while($row = mysqli_fetch_assoc($res))
    {
      $del_btn = "<button type='button' onclick='remove_user($row[id])' class='btn btn-danger shadow-none btn-sm'>
        <i class='bi bi-trash'></i> 
      </button>";

      $verified = "<span class='badge bg-warning'><i class='bi bi-x-lg'></i></span>";

      if($row['is_verified']){
        $verified = "<span class='badge bg-success'><i class='bi bi-check-lg'></i></span>";
        $del_btn = ""; 
      }

      $status = "<button onclick='toggle_status($row[id],0)' class='btn btn-dark btn-sm shadow-none'>
        active
      </button>";

      if(!$row['status']){
        $status = "<button onclick='toggle_status($row[id],1)' class='btn btn-danger btn-sm shadow-none'>
          inactive
        </button>";
      }

      $date = date("d-m-Y",strtotime($row['datentime']));

      $data.="
        <tr>
          <td>$i</td>
          <td>
            <img src='$path$row[profile]' width='55px'>
            <br>
            $row[name]
          </td>
          <td>$row[email]</td>
          <td>$row[phonenum]</td>
          <td>$row[address] | $row[pincode]</td>
          <td>$row[dob]</td>
          <td>$verified</td>
          <td>$status</td>
          <td>$date</td>
          <td>$del_btn</td>
        </tr>
      ";
      $i++;
    }

    echo $data;
  }

  if(isset($_POST['toggle_status']))
  {
    $frm_data = filteration($_POST);

    $q = "UPDATE `user_cred` SET `status`=? WHERE `id`=?";
    $v = [$frm_data['value'],$frm_data['toggle_status']];

    if(update($q,$v,'ii')){
      echo 1;
    }
    else{
      echo 0;
    }
  }

  if(isset($_POST['remove_user']))
  {
    $frm_data = filteration($_POST);
    $user_id  = (int)$frm_data['user_id'];

    // Only allow archiving of non-verified users
    $get = select("SELECT * FROM `user_cred` WHERE `id`=? AND `is_verified`=0 LIMIT 1", [$user_id], 'i');
    if(!$get || mysqli_num_rows($get) !== 1){
      echo 0;
      exit;
    }

    $row = mysqli_fetch_assoc($get);

    // Ensure archived_user_cred table exists (schema from add_more_archive_columns.sql)
    $create = "CREATE TABLE IF NOT EXISTS `archived_user_cred` (
      `id` int(11) NOT NULL,
      `name` varchar(100) NOT NULL,
      `email` varchar(150) NOT NULL,
      `address` varchar(120) NOT NULL,
      `phonenum` varchar(100) NOT NULL,
      `pincode` int(11) NOT NULL,
      `dob` date NOT NULL,
      `password` varchar(200) NOT NULL,
      `is_verified` int(11) NOT NULL DEFAULT 0,
      `token` varchar(200) DEFAULT NULL,
      `t_expire` date DEFAULT NULL,
      `datentime` datetime NOT NULL DEFAULT current_timestamp(),
      `status` int(11) NOT NULL DEFAULT 1,
      `profile` varchar(100) DEFAULT NULL,
      `archived_at` datetime NOT NULL DEFAULT current_timestamp(),
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

    if(!mysqli_query($con, $create)){
      echo 0;
      exit;
    }

    // Insert into archive table
    $ins = insert(
      "INSERT INTO `archived_user_cred`
      (`id`,`name`,`email`,`address`,`phonenum`,`pincode`,`dob`,
       `password`,`is_verified`,`token`,`t_expire`,`datentime`,`status`,`profile`)
       VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)",
      [
        $row['id'],$row['name'],$row['email'],$row['address'],$row['phonenum'],
        $row['pincode'],$row['dob'],$row['password'],$row['is_verified'],
        $row['token'],$row['t_expire'],$row['datentime'],$row['status'],$row['profile']
      ],
      'issssiissssiss'
    );

    if(!$ins){
      echo 0;
      exit;
    }

    // Mark user as archived (keep row for history, but hide from active list)
    $upd = update("UPDATE `user_cred` SET `is_archived`=1, `status`=0 WHERE `id`=?", [$user_id], 'i');

    if($upd){
      logAction('archive_user', "Archived user id={$user_id}, email={$row['email']}");
      echo 1;
    } else {
      echo 0;
    }
  }

  if(isset($_POST['search_user']))
  {
    $frm_data = filteration($_POST);

    // Search only non-archived users
    $query = "SELECT * FROM `user_cred` WHERE `is_archived` = 0 AND `name` LIKE ?";

    $res = select($query,["%".$frm_data['name']."%"],'s');    
    $i=1;
    $path = USERS_IMG_PATH;

    $data = "";

    while($row = mysqli_fetch_assoc($res))
    {
      $del_btn = "<button type='button' onclick='remove_user($row[id])' class='btn btn-danger shadow-none btn-sm'>
        <i class='bi bi-trash'></i> 
      </button>";

      $verified = "<span class='badge bg-warning'><i class='bi bi-x-lg'></i></span>";

      if($row['is_verified']){
        $verified = "<span class='badge bg-success'><i class='bi bi-check-lg'></i></span>";
        $del_btn = ""; 
      }

      $status = "<button onclick='toggle_status($row[id],0)' class='btn btn-dark btn-sm shadow-none'>
        active
      </button>";

      if(!$row['status']){
        $status = "<button onclick='toggle_status($row[id],1)' class='btn btn-danger btn-sm shadow-none'>
          inactive
        </button>";
      }

      $date = date("d-m-Y",strtotime($row['datentime']));

      $data.="
        <tr>
          <td>$i</td>
          <td>
            <img src='$path$row[profile]' width='55px'>
            <br>
            $row[name]
          </td>
          <td>$row[email]</td>
          <td>$row[phonenum]</td>
          <td>$row[address] | $row[pincode]</td>
          <td>$row[dob]</td>
          <td>$verified</td>
          <td>$status</td>
          <td>$date</td>
          <td>$del_btn</td>
        </tr>
      ";
      $i++;
    }

    echo $data;
  }

?>