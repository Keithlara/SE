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
      // Log room creation
      $room_id = mysqli_insert_id($con);
      $room_data = [
        'name' => $frm_data['name'],
        'area' => $frm_data['area'],
        'price' => $frm_data['price'],
        'quantity' => $frm_data['quantity'],
        'adult' => $frm_data['adult'],
        'children' => $frm_data['children']
      ];
      logCreation('room', $room_id, $room_data);
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
      logError('Failed to add room', json_encode($frm_data));
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
      // Log room update with changes
      $new_data = [
        'name' => $frm_data['name'],
        'area' => $frm_data['area'],
        'price' => $frm_data['price'],
        'quantity' => $frm_data['quantity'],
        'adult' => $frm_data['adult'],
        'children' => $frm_data['children'],
        'description' => $frm_data['desc']
      ];
      
      $old_data = [];
      $stmt = mysqli_prepare($con, "SELECT * FROM `rooms` WHERE `id`=?");
      mysqli_stmt_bind_param($stmt, 'i', $frm_data['room_id']);
      mysqli_stmt_execute($stmt);
      $res = mysqli_stmt_get_result($stmt);
      if($res && mysqli_num_rows($res) > 0) {
        $old_data = mysqli_fetch_assoc($res);
      }
      
      if(!empty($old_data)) {
        logUpdate('room', $frm_data['room_id'], $old_data, $new_data);
      }
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
    } else {
      logError('Failed to update room', json_encode($frm_data));
      echo 0;
    }

  }

  if(isset($_POST['toggle_status']))
  {
    $frm_data = filteration($_POST);
    $status = $frm_data['status'] ? 'Active' : 'Inactive';

    // Get current room data before update
    $room_data = [];
    $stmt = mysqli_prepare($con, "SELECT * FROM `rooms` WHERE `id`=?");
    mysqli_stmt_bind_param($stmt, 'i', $frm_data['room_id']);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    if($res && mysqli_num_rows($res) > 0) {
      $room_data = mysqli_fetch_assoc($res);
    }

    $q = "UPDATE `rooms` SET `status`=? WHERE `id`=?";
    if(update($q,[$frm_data['value'],$frm_data['toggle_status']],'ii')){
      // Log status change
      if(!empty($room_data)) {
        $details = [
          'room_id' => $frm_data['toggle_status'],
          'room_name' => $room_data['name'],
          'old_status' => $room_data['status'] ? 'Active' : 'Inactive',
          'new_status' => $status,
          'changed_by' => $_SESSION['adminName'] . ' (ID: ' . $_SESSION['adminId'] . ')'
        ];
        AuditLogger::logCRUD('update', 'room_status', $frm_data['toggle_status'], json_encode($details));
      }
      echo 1;
    } else {
      logError('Failed to update room status', json_encode($frm_data));
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
    $room_id = (int)$frm_data['room_id'];
    
    try {
      if ($room_id <= 0) {
        throw new Exception("Invalid room ID");
      }

      if (function_exists('ensureAppSchema')) {
        ensureAppSchema();
      }

      // Get room details before archiving for logging
      $room_query = "SELECT * FROM `rooms` WHERE `id` = ?";
      $room_stmt = mysqli_prepare($con, $room_query);
      mysqli_stmt_bind_param($room_stmt, 'i', $room_id);
      mysqli_stmt_execute($room_stmt);
      $room_data = mysqli_fetch_assoc(mysqli_stmt_get_result($room_stmt));
      mysqli_stmt_close($room_stmt);

      if(!$room_data) {
        throw new Exception("Room not found");
      }
      
      $existing_archive = mysqli_prepare($con, "SELECT `id` FROM `archived_rooms` WHERE `room_id` = ? ORDER BY `id` DESC LIMIT 1");
      mysqli_stmt_bind_param($existing_archive, 'i', $room_id);
      mysqli_stmt_execute($existing_archive);
      $archive_row = mysqli_fetch_assoc(mysqli_stmt_get_result($existing_archive));
      mysqli_stmt_close($existing_archive);

      // Start transaction
      mysqli_begin_transaction($con);

      if ($archive_row) {
        $archived_id = (int)$archive_row['id'];
        $refresh_archive = mysqli_prepare(
          $con,
          "UPDATE `archived_rooms`
           SET `name`=?, `area`=?, `price`=?, `quantity`=?, `adult`=?, `children`=?, `description`=?, `status`=?, `removed`=?, `is_archived`=1, `archived_at`=NOW()
           WHERE `id`=?"
        );
        if (!$refresh_archive) {
          throw new Exception("Failed to prepare archived room refresh: " . mysqli_error($con));
        }
        mysqli_stmt_bind_param(
          $refresh_archive,
          'siiiiisiii',
          $room_data['name'],
          $room_data['area'],
          $room_data['price'],
          $room_data['quantity'],
          $room_data['adult'],
          $room_data['children'],
          $room_data['description'],
          $room_data['status'],
          $room_data['removed'],
          $archived_id
        );
        if (!mysqli_stmt_execute($refresh_archive)) {
          $err = mysqli_error($con);
          mysqli_stmt_close($refresh_archive);
          throw new Exception("Failed to refresh archived room snapshot: " . $err);
        }
        mysqli_stmt_close($refresh_archive);
      } else {
        $archive_room = mysqli_prepare(
          $con,
          "INSERT INTO `archived_rooms`
            (`room_id`,`name`,`area`,`price`,`quantity`,`adult`,`children`,`description`,`status`,`removed`,`is_archived`,`archived_at`)
           VALUES (?,?,?,?,?,?,?,?,?,?,1,NOW())"
        );

        if (!$archive_room) {
          throw new Exception("Failed to prepare archived room insert: " . mysqli_error($con));
        }

        mysqli_stmt_bind_param(
          $archive_room,
          'isiiiiisii',
          $room_id,
          $room_data['name'],
          $room_data['area'],
          $room_data['price'],
          $room_data['quantity'],
          $room_data['adult'],
          $room_data['children'],
          $room_data['description'],
          $room_data['status'],
          $room_data['removed']
        );

        if (!mysqli_stmt_execute($archive_room)) {
          $err = mysqli_error($con);
          mysqli_stmt_close($archive_room);
          throw new Exception("Failed to create archived room snapshot: " . $err);
        }

        $archived_id = (int)mysqli_insert_id($con);
        mysqli_stmt_close($archive_room);
      }

      mysqli_query($con, "DELETE FROM `archived_room_images` WHERE `room_id` = {$archived_id}");
      mysqli_query($con, "DELETE FROM `archived_room_features` WHERE `room_id` = {$archived_id}");
      mysqli_query($con, "DELETE FROM `archived_room_facilities` WHERE `room_id` = {$archived_id}");
      mysqli_query($con, "DELETE FROM `archived_ratings_reviews` WHERE `room_id` = {$archived_id}");
      mysqli_query($con, "DELETE FROM `archived_room_block_dates` WHERE `room_id` = {$archived_id}");

      mysqli_query($con, "INSERT INTO `archived_room_images` (`room_id`, `image`, `thumb`)
        SELECT {$archived_id}, `image`, `thumb`
        FROM `room_images`
        WHERE `room_id` = {$room_id}");

      mysqli_query($con, "INSERT INTO `archived_room_features` (`room_id`, `features_id`)
        SELECT {$archived_id}, `features_id`
        FROM `room_features`
        WHERE `room_id` = {$room_id}");

      mysqli_query($con, "INSERT INTO `archived_room_facilities` (`room_id`, `facilities_id`)
        SELECT {$archived_id}, `facilities_id`
        FROM `room_facilities`
        WHERE `room_id` = {$room_id}");

      archiveRefreshRoomRelations($room_id, $archived_id);

      $update_room = mysqli_prepare($con, "UPDATE `rooms` SET `removed` = 1, `is_archived` = 1, `status` = 0, `archived_at` = NOW() WHERE `id` = ?");
      if (!$update_room) {
        throw new Exception("Failed to prepare room archive update: " . mysqli_error($con));
      }

      mysqli_stmt_bind_param($update_room, 'i', $room_id);
      if (!mysqli_stmt_execute($update_room)) {
        $err = mysqli_error($con);
        mysqli_stmt_close($update_room);
        throw new Exception("Failed to update room status: " . $err);
      }
      mysqli_stmt_close($update_room);

      if($room_data) {
        $details = [
          'room_id' => $room_id,
          'archive_id' => $archived_id,
          'room_name' => $room_data['name'],
          'archived_by' => $_SESSION['adminName'] . ' (ID: ' . $_SESSION['adminId'] . ')',
          'archived_at' => date('Y-m-d H:i:s'),
          'room_data' => [
            'name' => $room_data['name'],
            'area' => $room_data['area'],
            'price' => $room_data['price'],
            'status' => $room_data['status'] ? 'Active' : 'Inactive'
          ]
        ];
        AuditLogger::logCRUD('archive', 'room', $room_id, json_encode($details));
      }

      mysqli_commit($con);
      echo json_encode(['success' => true, 'message' => 'Room archived successfully']);
    } catch (Exception $e) {
      if(isset($con)) {
        mysqli_rollback($con);
      }
      error_log("Room archiving failed: " . $e->getMessage());
      http_response_code(500);
      echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
  }
  
?>
