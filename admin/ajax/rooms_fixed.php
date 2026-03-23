<?php 

require('../inc/db_config.php');
require('../inc/essentials.php');
adminLogin();

if(isset($_POST['add_room']))
{
  $features = filteration(json_decode($_POST['features']));
  $facilities = filteration(json_decode($_POST['facilities']));

  $frm_data = filteration($_POST);
  $flag = 0;

  $q1 = "INSERT INTO `rooms` (`name`, `area`, `price`, `quantity`, `adult`, `children`, `description`) VALUES (?,?,?,?,?,?,?)";
  $values = [$frm_data['name'],$frm_data['area'],$frm_data['price'],$frm_data['quantity'],$frm_data['adult'],$frm_data['children'],$frm_data['desc']];

  if(insert($q1,$values,'siiiiis')){
    $flag = 1;
  }
  
  $room_id = mysqli_insert_id($con);

  $q2 = "INSERT INTO `room_facilities`(`room_id`, `facilities_id`) VALUES (?,?)";
  if($stmt = mysqli_prepare($con,$q2))
  {
    foreach($facilities as $f){
      mysqli_stmt_bind_param($stmt,'ii',$room_id,$f);
      mysqli_stmt_execute($stmt);
    }
    mysqli_stmt_close($stmt);
  }
  else{
    $flag = 0;
    die('query cannot be prepared - insert');
  }

  
  $q3 = "INSERT INTO `room_features`(`room_id`, `features_id`) VALUES (?,?)";
  if($stmt = mysqli_prepare($con,$q3))
  {
    foreach($features as $f){
      mysqli_stmt_bind_param($stmt,'ii',$room_id,$f);
      mysqli_stmt_execute($stmt);
    }
    mysqli_stmt_close($stmt);
  }
  else{
    $flag = 0;
    die('query cannot be prepared - insert');
  }
  
  if($flag){
    echo 1;
  }
  else{
    echo 0;
  }
}

if(isset($_POST['get_all_rooms']))
{
  $res = select("SELECT * FROM `rooms` WHERE `removed`=?",[0],'i');
  $i=1;

  $data = "";

  while($row = mysqli_fetch_assoc($res))
  {
    if($row['status']==1){
      $status = "<button onclick='toggle_status($row[id],0)' class='btn btn-dark btn-sm shadow-none'>active</button>";
    }
    else{
      $status = "<button onclick='toggle_status($row[id],1)' class='btn btn-warning btn-sm shadow-none'>inactive</button>";
    }

    $data.="
      <tr class='align-middle'>
        <td>$i</td>
        <td>$row[name]</td>
        <td>$row[area] sq. ft.</td>
        <td>
          <span class='badge rounded-pill bg-light text-dark'>
            Adult: $row[adult]
          </span><br>
          <span class='badge rounded-pill bg-light text-dark'>
            Children: $row[children]
          </span>
        </td>
        <td>₱$row[price]</td>
        <td>$row[quantity]</td>
        <td>$status</td>
        <td>
          <button type='button' onclick='edit_details($row[id])' class='btn btn-primary shadow-none btn-sm' data-bs-toggle='modal' data-bs-target='#edit-room'>
            <i class='bi bi-pencil-square'></i> 
          </button>
          <button type='button' onclick=\"room_images($row[id],'$row[name]')\" class='btn btn-info shadow-none btn-sm' data-bs-toggle='modal' data-bs-target='#room-images'>
            <i class='bi bi-images'></i> 
          </button>
          <button type='button' onclick='remove_room($row[id])' class='btn btn-danger shadow-none btn-sm'>
            <i class='bi bi-trash'></i> 
          </button>
        </td>
      </tr>
    ";
    $i++;
  }

  echo $data;
}

if(isset($_POST['get_room']))
{
  $frm_data = filteration($_POST);

  $res1 = select("SELECT * FROM `rooms` WHERE `id`=?",[$frm_data['get_room']],'i');
  $res2 = select("SELECT * FROM `room_features` WHERE `room_id`=?",[$frm_data['get_room']],'i');
  $res3 = select("SELECT * FROM `room_facilities` WHERE `room_id`=?",[$frm_data['get_room']],'i');

  $roomdata = mysqli_fetch_assoc($res1);
  $features = [];
  $facilities = [];

  if(mysqli_num_rows($res2)>0)
  {
    while($row = mysqli_fetch_assoc($res2)){
      array_push($features,$row['features_id']);
    }
  }

  if(mysqli_num_rows($res3)>0)
  {
    while($row = mysqli_fetch_assoc($res3)){
      array_push($facilities,$row['facilities_id']);
    }
  }

  $data = ["roomdata" => $roomdata, "features" => $features, "facilities" => $facilities];
  
  $data = json_encode($data);

  echo $data;
}

if(isset($_POST['edit_room']))
{
  $features = filteration(json_decode($_POST['features']));
  $facilities = filteration(json_decode($_POST['facilities']));

  $frm_data = filteration($_POST);
  $flag = 0;

  $q1 = "UPDATE `rooms` SET `name`=?,`area`=?,`price`=?,`quantity`=?,
    `adult`=?,`children`=?,`description`=? WHERE `id`=?";
  $values = [$frm_data['name'],$frm_data['area'],$frm_data['price'],$frm_data['quantity'],$frm_data['adult'],$frm_data['children'],$frm_data['desc'],$frm_data['room_id']];
  
  if(update($q1,$values,'siiiiisi')){
    $flag = 1;
  }

  $del_features = delete("DELETE FROM `room_features` WHERE `room_id`=?", [$frm_data['room_id']],'i');
  $del_facilities = delete("DELETE FROM `room_facilities` WHERE `room_id`=?", [$frm_data['room_id']],'i');

  if(!($del_facilities && $del_features)){
    $flag = 0;
  }

  $q2 = "INSERT INTO `room_facilities`(`room_id`, `facilities_id`) VALUES (?,?)";
  if($stmt = mysqli_prepare($con,$q2))
  {
    foreach($facilities as $f){
      mysqli_stmt_bind_param($stmt,'ii',$frm_data['room_id'],$f);
      mysqli_stmt_execute($stmt);
    }
    $flag = 1;
    mysqli_stmt_close($stmt);
  }
  else{
    $flag = 0;
    die('query cannot be prepared - insert');
  }

  
  $q3 = "INSERT INTO `room_features`(`room_id`, `features_id`) VALUES (?,?)";
  if($stmt = mysqli_prepare($con,$q3))
  {
    foreach($features as $f){
      mysqli_stmt_bind_param($stmt,'ii',$frm_data['room_id'],$f);
      mysqli_stmt_execute($stmt);
    }
    $flag = 1;
    mysqli_stmt_close($stmt);
  }
  else{
    $flag = 0;
    die('query cannot be prepared - insert');
  }
  
  if($flag){
    echo 1;
  }
  else{
    echo 0;
  }
}

if(isset($_POST['toggle_status']))
{
  $frm_data = filteration($_POST);

  $q = "UPDATE `rooms` SET `status`=? WHERE `id`=?";
  $v = [$frm_data['value'],$frm_data['toggle_status']];

  if(update($q,$v,'ii')){
    echo 1;
  }
  else{
    echo 0;
  }
}

if(isset($_POST['add_image']))
{
  $frm_data = filteration($_POST);

  $img_r = uploadImage($_FILES['image'],ROOMS_FOLDER);

  if($img_r == 'inv_img'){
    echo $img_r;
  }
  else if($img_r == 'inv_size'){
    echo $img_r;
  }
  else if($img_r == 'upd_failed'){
    echo $img_r;
  }
  else{
    $q = "INSERT INTO `room_images`(`room_id`, `image`) VALUES (?,?)";
    $values = [$frm_data['room_id'],$img_r];
    $res = insert($q,$values,'is');
    echo $res;
  }
}

if(isset($_POST['get_room_images']))
{
  $frm_data = filteration($_POST);
  $res = select("SELECT * FROM `room_images` WHERE `room_id`=?",[$frm_data['get_room_images']],'i');

  $path = ROOMS_IMG_PATH;

  while($row = mysqli_fetch_assoc($res))
  {
    if($row['thumb']==1){
      $thumb_btn = "<i class='bi bi-check-lg text-light bg-success px-2 py-1 rounded fs-5'></i>";
    }
    else{
      $thumb_btn = "<button onclick='thumb_image($row[sr_no],$row[room_id])' class='btn btn-secondary shadow-none'>
        <i class='bi bi-check-lg'></i>
      </button>";
    }

    echo<<<data
      <tr class='align-middle'>
        <td><img src='$path$row[image]' class='img-fluid'></td>
        <td>$thumb_btn</td>
        <td>
          <button onclick='rem_image($row[sr_no],$row[room_id])' class='btn btn-danger shadow-none'>
            <i class='bi bi-trash'></i>
          </button>
        </td>
      </tr>
    data;
  }
}

if(isset($_POST['rem_image']))
{
  $frm_data = filteration($_POST);

  $values = [$frm_data['image_id'],$frm_data['room_id']];

  $pre_q = "SELECT * FROM `room_images` WHERE `sr_no`=? AND `room_id`=?";
  $res = select($pre_q,$values,'ii');
  $img = mysqli_fetch_assoc($res);

  if(deleteImage($img['image'],ROOMS_FOLDER)){
    $q = "DELETE FROM `room_images` WHERE `sr_no`=? AND `room_id`=?";
    $res = delete($q,$values,'ii');
    echo $res;
  }
  else{
    echo 0;
  }
}

if(isset($_POST['thumb_image']))
{
  $frm_data = filteration($_POST);

  $pre_q = "UPDATE `room_images` SET `thumb`=? WHERE `room_id`=?";
  $pre_v = [0,$frm_data['room_id']];
  $pre_res = update($pre_q,$pre_v,'ii');

  $q = "UPDATE `room_images` SET `thumb`=? WHERE `sr_no`=? AND `room_id`=?";
  $v = [1,$frm_data['image_id'],$frm_data['room_id']];
  $res = update($q,$v,'iii');
  echo $res;
}

if(isset($_POST['remove_room']))
{
  header('Content-Type: application/json');
  
  $frm_data = filteration($_POST);
  $room_id = $frm_data['room_id'];
  
  try {
    // 1. Create archived_rooms table if it doesn't exist
    $create_table = "CREATE TABLE IF NOT EXISTS `archived_rooms` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `room_id` int(11) NOT NULL,
      `name` varchar(150) NOT NULL,
      `area` int(11) NOT NULL,
      `price` int(11) NOT NULL,
      `quantity` int(11) NOT NULL,
      `adult` int(11) NOT NULL,
      `children` int(11) NOT NULL,
      `description` mediumtext NOT NULL,
      `status` tinyint(4) NOT NULL DEFAULT 1,
      `removed` tinyint(4) NOT NULL DEFAULT 0,
      `is_archived` tinyint(1) NOT NULL DEFAULT 1,
      `archived_at` datetime NOT NULL DEFAULT current_timestamp(),
      PRIMARY KEY (`id`),
      KEY `room_id` (`room_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    if(!mysqli_query($con, $create_table)) {
      throw new Exception("Failed to create archived_rooms table: " . mysqli_error($con));
    }
    
    // 2. Create archived_room_images table if it doesn't exist
    $create_images_table = "CREATE TABLE IF NOT EXISTS `archived_room_images` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `room_id` int(11) NOT NULL,
      `image` varchar(150) NOT NULL,
      `thumb` tinyint(4) NOT NULL DEFAULT 0,
      PRIMARY KEY (`id`),
      KEY `room_id` (`room_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    if(!mysqli_query($con, $create_images_table)) {
      throw new Exception("Failed to create archived_room_images table: " . mysqli_error($con));
    }
    
    // 3. Create archived_room_features table if it doesn't exist
    $create_features_table = "CREATE TABLE IF NOT EXISTS `archived_room_features` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `room_id` int(11) NOT NULL,
      `features_id` int(11) NOT NULL,
      PRIMARY KEY (`id`),
      KEY `room_id` (`room_id`),
      KEY `features_id` (`features_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    if(!mysqli_query($con, $create_features_table)) {
      throw new Exception("Failed to create archived_room_features table: " . mysqli_error($con));
    }
    
    // 4. Create archived_room_facilities table if it doesn't exist
    $create_facilities_table = "CREATE TABLE IF NOT EXISTS `archived_room_facilities` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `room_id` int(11) NOT NULL,
      `facilities_id` int(11) NOT NULL,
      PRIMARY KEY (`id`),
      KEY `room_id` (`room_id`),
      KEY `facilities_id` (`facilities_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    if(!mysqli_query($con, $create_facilities_table)) {
      throw new Exception("Failed to create archived_room_facilities table: " . mysqli_error($con));
    }
    
    // Start transaction
    mysqli_begin_transaction($con);
    
    // 5. Mark the room as archived and removed in the rooms table
    $update_query = "UPDATE `rooms` SET `is_archived` = 1, `removed` = 1, `status` = 0 WHERE `id` = ?";
    $update_stmt = mysqli_prepare($con, $update_query);
    
    if(!$update_stmt) {
      throw new Exception("Prepare failed: " . mysqli_error($con));
    }
    
    mysqli_stmt_bind_param($update_stmt, 'i', $room_id);
    
    if(!mysqli_stmt_execute($update_stmt)) {
      throw new Exception("Failed to update room status: " . mysqli_error($con));
    }
    
    // 6. Copy room data to archived_rooms table
    $copy_query = "INSERT INTO `archived_rooms` 
                 (`room_id`, `name`, `area`, `price`, `quantity`, `adult`, `children`, `description`, `status`, `removed`)
                 SELECT `id`, `name`, `area`, `price`, `quantity`, `adult`, `children`, `description`, `status`, `removed` 
                 FROM `rooms` 
                 WHERE `id` = ?";
    $copy_stmt = mysqli_prepare($con, $copy_query);
    
    if(!$copy_stmt) {
      throw new Exception("Prepare failed: " . mysqli_error($con));
    }
    
    mysqli_stmt_bind_param($copy_stmt, 'i', $room_id);
    
    if(!mysqli_stmt_execute($copy_stmt)) {
      throw new Exception("Failed to archive room data: " . mysqli_error($con));
    }
    
    $archived_id = mysqli_insert_id($con);
    
    // 7. Copy room images to archived_room_images
    $copy_images = "INSERT INTO `archived_room_images` (`room_id`, `image`, `thumb`)
                   SELECT ?, `image`, `thumb` FROM `room_images` WHERE `room_id` = ?";
    $stmt = mysqli_prepare($con, $copy_images);
    
    if($stmt) {
      mysqli_stmt_bind_param($stmt, 'ii', $archived_id, $room_id);
      if(!mysqli_stmt_execute($stmt)) {
        error_log("Warning: Failed to archive room images: " . mysqli_error($con));
      }
    } else {
      error_log("Warning: Failed to prepare image archive query: " . mysqli_error($con));
    }
    
    // 8. Copy room features to archived_room_features
    $copy_features = "INSERT INTO `archived_room_features` (`room_id`, `features_id`)
                     SELECT ?, `features_id` FROM `room_features` WHERE `room_id` = ?";
    $stmt = mysqli_prepare($con, $copy_features);
    
    if($stmt) {
      mysqli_stmt_bind_param($stmt, 'ii', $archived_id, $room_id);
      if(!mysqli_stmt_execute($stmt)) {
        error_log("Warning: Failed to archive room features: " . mysqli_error($con));
      }
    } else {
      error_log("Warning: Failed to prepare features archive query: " . mysqli_error($con));
    }
    
    // 9. Copy room facilities to archived_room_facilities
    $copy_facilities = "INSERT INTO `archived_room_facilities` (`room_id`, `facilities_id`)
                       SELECT ?, `facilities_id` FROM `room_facilities` WHERE `room_id` = ?";
    $stmt = mysqli_prepare($con, $copy_facilities);
    
    if($stmt) {
      mysqli_stmt_bind_param($stmt, 'ii', $archived_id, $room_id);
      if(!mysqli_stmt_execute($stmt)) {
        error_log("Warning: Failed to archive room facilities: " . mysqli_error($con));
      }
    } else {
      error_log("Warning: Failed to prepare facilities archive query: " . mysqli_error($con));
    }
    
    // 10. Commit the transaction
    if(mysqli_commit($con)) {
      echo json_encode(['success' => true, 'message' => 'Room archived successfully']);
    } else {
      throw new Exception("Failed to commit transaction");
    }
    
  } catch (Exception $e) {
    // Rollback transaction on error
    if(isset($con)) {
      mysqli_rollback($con);
    }
    error_log("Room archiving failed: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
  }
  exit;
}
