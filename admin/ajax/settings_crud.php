<?php 

require('../inc/db_config.php');
require('../inc/essentials.php');
adminLogin();

function ensurePaymentColumns($con){
  $required = [
    'payment_gcash_number' => "VARCHAR(100) DEFAULT NULL",
    'payment_maya_number' => "VARCHAR(100) DEFAULT NULL",
    'payment_gcash_qr' => "VARCHAR(255) DEFAULT NULL",
    'payment_maya_qr' => "VARCHAR(255) DEFAULT NULL"
  ];
  foreach($required as $col => $definition){
    $col_res = mysqli_query($con, "SHOW COLUMNS FROM `settings` LIKE '$col'");
    if(!$col_res || mysqli_num_rows($col_res)==0){
      mysqli_query($con, "ALTER TABLE `settings` ADD `$col` $definition");
    }
  }
}

ensurePaymentColumns($con);

  if(isset($_POST['get_general']))
  {
    $q = "SELECT * FROM `settings` WHERE `sr_no`=?";
    $values = [1];
    $res = select($q,$values,"i");
    $data = mysqli_fetch_assoc($res);
    $json_data = json_encode($data);
    echo $json_data;
  }

  if(isset($_POST['upd_general']))
  {
    $frm_data = filteration($_POST);

    $q = "UPDATE `settings` SET `site_title`=?, `site_about`=? WHERE `sr_no`=?";
    $values = [$frm_data['site_title'],$frm_data['site_about'],1];
    $res = update($q,$values,'ssi');
    echo $res;
  }

  if(isset($_POST['upd_shutdown']))
  {
    $frm_data = ($_POST['upd_shutdown']==0) ? 1 : 0;

    $q = "UPDATE `settings` SET `shutdown`=? WHERE `sr_no`=?";
    $values = [$frm_data,1];
    $res = update($q,$values,'ii');
    echo $res;
  }

  if(isset($_POST['get_contacts']))
  {
    $q = "SELECT * FROM `contact_details` WHERE `sr_no`=?";
    $values = [1];
    $res = select($q,$values,"i");
    $data = mysqli_fetch_assoc($res);
    $json_data = json_encode($data);
    echo $json_data;
  }

  if(isset($_POST['upd_contacts']))
  {
    $frm_data = filteration($_POST);

    $q = "UPDATE `contact_details` SET `address`=?,`gmap`=?,`pn1`=?,`pn2`=?,`email`=?,`fb`=?,`insta`=?,`tw`=?,`iframe`=? WHERE `sr_no`=?";
    $values = [$frm_data['address'],$frm_data['gmap'],$frm_data['pn1'],$frm_data['pn2'],$frm_data['email'],$frm_data['fb'],$frm_data['insta'],$frm_data['tw'],$frm_data['iframe'],1];
    $res = update($q,$values,'sssssssssi');
    echo $res;
  }

  if(isset($_POST['get_payment_settings']))
  {
    $q = "SELECT `payment_gcash_number`,`payment_maya_number`,`payment_gcash_qr`,`payment_maya_qr` FROM `settings` WHERE `sr_no`=1 LIMIT 1";
    $res = mysqli_query($con, $q);
    if($res && mysqli_num_rows($res) > 0){
      echo json_encode(mysqli_fetch_assoc($res));
    }else{
      echo json_encode([
        'payment_gcash_number' => '',
        'payment_maya_number' => '',
        'payment_gcash_qr' => '',
        'payment_maya_qr' => ''
      ]);
    }
  }

  if(isset($_POST['upd_payment_settings']))
  {
    $frm_data = filteration($_POST);

    $fields = [
      'payment_gcash_number' => $frm_data['payment_gcash_number'] ?? '',
      'payment_maya_number' => $frm_data['payment_maya_number'] ?? ''
    ];

    $qr_fields = [
      'payment_gcash_qr' => $_FILES['payment_gcash_qr'] ?? null,
      'payment_maya_qr' => $_FILES['payment_maya_qr'] ?? null
    ];

    $upload_dir = UPLOAD_IMAGE_PATH.'payments/';
    if(!is_dir($upload_dir)){
      mkdir($upload_dir, 0777, true);
    }

    foreach($qr_fields as $column => $file){
      if($file && isset($file['name']) && $file['error'] === UPLOAD_ERR_OK){
        $allowed = ['image/jpeg','image/png','image/webp'];
        if(!in_array($file['type'], $allowed)){
          echo json_encode(['status'=>'error','message'=>'Invalid QR image type.']);
          exit;
        }
        if($file['size'] > 2*1024*1024){
          echo json_encode(['status'=>'error','message'=>'QR image too large (max 2MB).']);
          exit;
        }
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = strtoupper($column).'_'.time().random_int(100,999).'.'.$ext;
        $dest = $upload_dir.$filename;
        if(!move_uploaded_file($file['tmp_name'], $dest)){
          echo json_encode(['status'=>'error','message'=>'Failed to upload QR image.']);
          exit;
        }

        $old_q = "SELECT `$column` FROM `settings` WHERE `sr_no`=1 LIMIT 1";
        $old_res = mysqli_query($con, $old_q);
        $old_file = '';
        if($old_res && mysqli_num_rows($old_res) > 0){
          $old_row = mysqli_fetch_assoc($old_res);
          $old_file = $old_row[$column];
        }

        $fields[$column] = SITE_URL.'images/payments/'.$filename;

        if($old_file){
          $path = str_replace(SITE_URL, $_SERVER['DOCUMENT_ROOT'].'/', $old_file);
          if(file_exists($path)){
            unlink($path);
          }
        }
      }
    }

    $set_clause = [];
    $values = [];
    foreach($fields as $column => $value){
      $set_clause[] = "`$column`=?";
      $values[] = $value;
    }
    $values[] = 1;

    $q = "UPDATE `settings` SET ".implode(',', $set_clause)." WHERE `sr_no`=?";
    $res = update($q, $values, str_repeat('s', count($fields)).'i');
    echo json_encode(['status'=>$res !== false ? 'success' : 'error']);
  }

  if(isset($_POST['add_member']))
  {
    $frm_data = filteration($_POST);

    $img_r = uploadImage($_FILES['picture'],ABOUT_FOLDER);

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
      $q = "INSERT INTO `team_details`(`name`, `picture`) VALUES (?,?)";
      $values = [$frm_data['name'],$img_r];
      $res = insert($q,$values,'ss');
      echo $res;
    }
  }

  if(isset($_POST['get_members']))
  {
    $res = selectAll('team_details');

    while($row = mysqli_fetch_assoc($res))
    {
      $path = ABOUT_IMG_PATH;
      echo <<<data
        <div class="col-md-2 mb-3">
          <div class="card bg-dark text-white">
            <img src="$path$row[picture]" class="card-img">
            <div class="card-img-overlay text-end">
              <button type="button" onclick="rem_member($row[sr_no])" class="btn btn-danger btn-sm shadow-none">
                <i class="bi bi-trash"></i> Delete
              </button>
            </div>
            <p class="card-text text-center px-3 py-2">$row[name]</p>
          </div>
        </div>
      data;
    }
  }

  if(isset($_POST['rem_member']))
  {
    $frm_data = filteration($_POST);
    $values = [$frm_data['rem_member']];

    $pre_q = "SELECT * FROM `team_details` WHERE `sr_no`=?";
    $res = select($pre_q,$values,'i');
    $img = mysqli_fetch_assoc($res);

    if(deleteImage($img['picture'],ABOUT_FOLDER)){
      $q = "DELETE FROM `team_details` WHERE `sr_no`=?";
      $res = delete($q,$values,'i');
      echo $res;
    }
    else{
      echo 0;
    }

  }

?>