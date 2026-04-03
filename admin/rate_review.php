<?php
  require('inc/essentials.php');
  require('inc/db_config.php');
  adminLogin();

  function render_rating_stars($rating_raw){
    $rating = is_numeric($rating_raw) ? (float)$rating_raw : 0.0;
    if($rating < 0) $rating = 0;
    if($rating > 5) $rating = 5;

    $full = (int)floor($rating);
    $frac = $rating - $full;
    $half = ($frac >= 0.25 && $frac < 0.75) ? 1 : 0;
    if($frac >= 0.75){
      $full = min(5, $full + 1);
      $half = 0;
    }
    $empty = max(0, 5 - $full - $half);

    $title = htmlspecialchars(number_format($rating, 1), ENT_QUOTES);
    $html = "<span class='text-warning' title='{$title}/5' aria-label='Rating {$title} out of 5'>";
    for($i=0;$i<$full;$i++){ $html .= "<i class='bi bi-star-fill'></i>"; }
    if($half){ $html .= "<i class='bi bi-star-half'></i>"; }
    for($i=0;$i<$empty;$i++){ $html .= "<i class='bi bi-star'></i>"; }
    $html .= "</span> <span class='text-muted small'>({$title})</span>";
    return $html;
  }

  if(isset($_GET['seen']))
  {
    $frm_data = filteration($_GET);

    if($frm_data['seen']=='all'){
      $q = "UPDATE `rating_review` SET `seen`=?";
      $values = [1];
      if(update($q,$values,'i')){
        alert('success','Marked all as read!');
      }
      else{
        alert('error','Operation Failed!');
      }
    }
    else{
      $q = "UPDATE `rating_review` SET `seen`=? WHERE `sr_no`=?";
      $values = [1,$frm_data['seen']];
      if(update($q,$values,'ii')){
        alert('success','Marked as read!');
      }
      else{
        alert('error','Operation Failed!');
      }
    }
  }

  if(isset($_GET['del']))
  {
    $frm_data = filteration($_GET);

    if($frm_data['del']=='all'){
      $q = "DELETE FROM `rating_review`";
      if(mysqli_query($con,$q)){
        alert('success','All data deleted!');
      }
      else{
        alert('error','Operation failed!');
      }
    }
    else{
      $q = "DELETE FROM `rating_review` WHERE `sr_no`=?";
      $values = [$frm_data['del']];
      if(delete($q,$values,'i')){
        alert('success','Data deleted!');
      }
      else{
        alert('error','Operation failed!');
      }
    }
  }

  if(isset($_GET['archive']))
  {
    if (function_exists('ensureAppSchema')) {
      ensureAppSchema();
    }

    $frm_data = filteration($_GET);

    if($frm_data['archive']=='all'){
      $res = mysqli_query($con, "SELECT `sr_no` FROM `rating_review` WHERE `is_archived` = 0");
      $ok = true;
      if($res){
        while($row = mysqli_fetch_assoc($res)){
          $reviewId = (int)$row['sr_no'];
          mysqli_query($con, "DELETE FROM `archived_reviews` WHERE `id` = {$reviewId}");
          $copy = mysqli_query($con, "INSERT INTO `archived_reviews` (`id`,`booking_id`,`room_id`,`user_id`,`rating`,`review`,`seen`,`datentime`,`archived_at`) SELECT `sr_no`,`booking_id`,`room_id`,`user_id`,`rating`,`review`,`seen`,`datentime`, NOW() FROM `rating_review` WHERE `sr_no` = {$reviewId} LIMIT 1");
          $upd = mysqli_query($con, "UPDATE `rating_review` SET `is_archived` = 1, `archived_at` = NOW() WHERE `sr_no` = {$reviewId}");
          if(!$copy || !$upd){ $ok = false; break; }
        }
      } else {
        $ok = false;
      }

      if($ok){
        alert('success','All reviews archived!');
      } else {
        alert('error','Failed to archive reviews.');
      }
    }
    else{
      $reviewId = (int)$frm_data['archive'];
      $copy = mysqli_query($con, "INSERT INTO `archived_reviews` (`id`,`booking_id`,`room_id`,`user_id`,`rating`,`review`,`seen`,`datentime`,`archived_at`) SELECT `sr_no`,`booking_id`,`room_id`,`user_id`,`rating`,`review`,`seen`,`datentime`, NOW() FROM `rating_review` WHERE `sr_no` = {$reviewId} AND `is_archived` = 0 LIMIT 1");
      $upd = mysqli_query($con, "UPDATE `rating_review` SET `is_archived` = 1, `archived_at` = NOW() WHERE `sr_no` = {$reviewId} AND `is_archived` = 0");
      if($copy && $upd && mysqli_affected_rows($con) > 0){
        alert('success','Review archived!');
      }
      else{
        alert('error','Operation failed!');
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
  <title>Admin Panel - Ratings & Reviews</title>
  <?php require('inc/links.php'); ?>
</head>
<body class="bg-light">

  <?php require('inc/header.php'); ?>

  <div class="container-fluid" id="main-content">
    <div class="row">
      <div class="col-lg-10 ms-auto p-4 overflow-hidden">
        <h3 class="mb-4">RATINGS & REVIEWS</h3>

        <div class="card border-0 shadow-sm mb-4">
          <div class="card-body">

            <div class="text-end mb-4">
              <a href="?seen=all" class="btn btn-dark rounded-pill shadow-none btn-sm">
                <i class="bi bi-check-all"></i> Mark all read
              </a>
              <a href="?archive=all" class="btn btn-warning rounded-pill shadow-none btn-sm">
                <i class="bi bi-archive"></i> Archive all
              </a>
            </div>

            <div class="table-responsive-md">
              <table class="table table-hover border">
                <thead>
                  <tr class="bg-dark text-light">
                    <th scope="col">#</th>
                    <th scope="col">Room Name</th>
                    <th scope="col">User Name</th>
                    <th scope="col">Rating</th>
                    <th scope="col" width="30%">Review</th>
                    <th scope="col">Date</th>
                    <th scope="col">Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php 
                    $q = "SELECT rr.*,uc.name AS uname, r.name AS rname FROM `rating_review` rr
                      INNER JOIN `user_cred` uc ON rr.user_id = uc.id
                      INNER JOIN `rooms` r ON rr.room_id = r.id
                      WHERE rr.is_archived = 0
                      ORDER BY `sr_no` DESC";

                    $data = mysqli_query($con,$q);
                    $i=1;

                    while($row = mysqli_fetch_assoc($data))
                    {
                      $date = date('d-m-Y',strtotime($row['datentime']));
                      $rating_stars = render_rating_stars($row['rating'] ?? 0);

                      $seen='';
                      if($row['seen']!=1){
                        $seen = "<a href='?seen=$row[sr_no]' class='btn btn-sm rounded-pill btn-primary mb-2'>Mark as read</a> <br>";
                      }
                      $seen.="<a href='?archive=$row[sr_no]' class='btn btn-sm rounded-pill btn-warning'>Archive</a>";

                      echo<<<query
                        <tr>
                          <td>$i</td>
                          <td>$row[rname]</td>
                          <td>$row[uname]</td>
                          <td>$rating_stars</td>
                          <td>$row[review]</td>
                          <td>$date</td>
                          <td>$seen</td>
                        </tr>
                      query;
                      $i++;
                    }
                  ?>
                </tbody>
              </table>
            </div>

          </div>
        </div>


      </div>
    </div>
  </div>
  

  <?php require('inc/scripts.php'); ?>

</body>
</html>
