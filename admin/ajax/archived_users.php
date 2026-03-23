<?php 
require('../inc/db_config.php');
require('../inc/essentials.php');
adminLogin();

if(isset($_POST['get_archived_users']))
{
  $query = "SELECT * FROM `archived_user_cred` ORDER BY `archived_at` DESC";
  $res = mysqli_query($con, $query);
  
  $i = 1;
  $table_data = "";

  if(mysqli_num_rows($res) == 0) {
    echo "<tr><td colspan='6' class='text-center py-4'>No archived users found</td></tr>";
    exit;
  }

  while($data = mysqli_fetch_assoc($res))
  {
    $verified = "
      <span class='badge bg-danger'>
        <i class='bi bi-x-lg'></i>
      </span>";
    
    if($data['is_verified'] == 1) {
      $verified = "
        <span class='badge bg-success'>
          <i class='bi bi-check-lg'></i>
        </span>";
    }

    $status = "
      <span class='badge bg-dark'>Inactive</span>";
    
    if($data['status'] == 1) {
      $status = "
        <span class='badge bg-success'>Active</span>";
    }

    $profile_img = $data['profile'] != '' ? 
      "../assets/images/users/{$data['profile']}" : 
      "../assets/images/users/default.png";

    $archived_date = date("d M Y H:i", strtotime($data['archived_at']));

    $table_data .= "
      <tr>
        <td>$i</td>
        <td>
          <div class='d-flex align-items-center'>
            <img src='$profile_img' class='user-image me-2' alt='User Image'>
            <div>
              <h6 class='mb-0'>{$data['name']}</h6>
              <small class='text-muted'>{$data['email']}</small>
            </div>
          </div>
        </td>
        <td>
          <div><i class='bi bi-telephone me-2'></i>{$data['phonenum']}</div>
          <div><i class='bi bi-geo-alt me-2'></i>" . substr($data['address'], 0, 20) . "...</div>
        </td>
        <td>
          $status
          $verified
        </td>
        <td>$archived_date</td>
        <td>
          <button type='button' onclick='restoreUser({$data['id']})' class='btn btn-sm btn-success' title='Restore User'>
            <i class='bi bi-arrow-counterclockwise'></i>
          </button>
        </td>
      </tr>";

    $i++;
  }

  echo $table_data;
}

if(isset($_POST['restore_user']))
{
  $user_id = (int)$_POST['user_id'];
  
  // Start transaction
  mysqli_begin_transaction($con);
  
  try {
    // 1. Get user data from archive
    $query = "SELECT * FROM `archived_user_cred` WHERE `id` = ?";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, 'i', $user_id);
    mysqli_stmt_execute($stmt);
    $user_data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    
    if(!$user_data) {
      throw new Exception("User not found in archive");
    }
    
    // 2. Insert into main user_cred table
    $insert_query = "INSERT INTO `user_cred` 
                    (`id`, `name`, `email`, `address`, `phonenum`, `pincode`, `dob`, 
                     `password`, `is_verified`, `token`, `t_expire`, `datentime`, `status`, `profile`)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = mysqli_prepare($con, $insert_query);
    mysqli_stmt_bind_param($stmt, 'issssiisssssis', 
      $user_data['id'],
      $user_data['name'],
      $user_data['email'],
      $user_data['address'],
      $user_data['phonenum'],
      $user_data['pincode'],
      $user_data['dob'],
      $user_data['password'],
      $user_data['is_verified'],
      $user_data['token'],
      $user_data['t_expire'],
      $user_data['datentime'],
      $user_data['status'],
      $user_data['profile']
    );
    
    $inserted = mysqli_stmt_execute($stmt);
    
    if(!$inserted) {
      throw new Exception("Failed to restore user");
    }
    
    // 3. Delete from archive
    $delete_query = "DELETE FROM `archived_user_cred` WHERE `id` = ?";
    $stmt = mysqli_prepare($con, $delete_query);
    mysqli_stmt_bind_param($stmt, 'i', $user_id);
    $deleted = mysqli_stmt_execute($stmt);
    
    if(!$deleted) {
      throw new Exception("Failed to remove from archive");
    }
    
    // Commit transaction
    mysqli_commit($con);
    echo 1;
    
  } catch (Exception $e) {
    // Rollback transaction on error
    mysqli_rollback($con);
    echo 0;
  }
}
?>
