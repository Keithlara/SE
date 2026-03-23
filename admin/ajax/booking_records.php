<?php 

  require('../inc/db_config.php');
  require('../inc/essentials.php');
  date_default_timezone_set("Asia/Kolkata");
  adminLogin();

  if(isset($_POST['get_bookings']))
  {
    $frm_data = filteration($_POST);

    // Number of records per page for pagination
    $limit = 5;
    $page = isset($frm_data['page']) ? (int)$frm_data['page'] : 1;
    if($page < 1){ $page = 1; }
    $start = ($page-1) * $limit;

    $search = $frm_data['search'] ?? '';
    $status = $frm_data['status'] ?? 'all';
    $month = isset($frm_data['month']) ? (int)$frm_data['month'] : 0;
    $year = isset($frm_data['year']) ? (int)$frm_data['year'] : 0;

    // Base status condition
    $statusCondition = "((bo.booking_status='booked' AND bo.arrival=1) 
      OR (bo.booking_status='cancelled' AND bo.refund=1)
      OR (bo.booking_status='payment failed'))";

    if($status === 'booked'){
      $statusCondition = "(bo.booking_status='booked' AND bo.arrival=1)";
    } elseif($status === 'cancelled'){
      $statusCondition = "(bo.booking_status='cancelled' AND bo.refund=1)";
    } elseif($status === 'payment_failed'){
      $statusCondition = "(bo.booking_status='payment failed')";
    }

    $query = "SELECT bo.*, bd.* FROM `booking_order` bo
      INNER JOIN `booking_details` bd ON bo.booking_id = bd.booking_id
      WHERE $statusCondition 
      AND (bo.order_id LIKE ? OR bd.phonenum LIKE ? OR bd.user_name LIKE ?)";

    $values = ["%$search%","%$search%","%$search%"];
    $datatypes = 'sss';

    // Optional month/year filter on booking datetime
    if($month >= 1 && $month <= 12 && $year >= 2000){
      $date_start = sprintf('%04d-%02d-01', $year, $month);
      $date_end = date('Y-m-t', strtotime($date_start));
      $query .= " AND DATE(bo.datentime) BETWEEN ? AND ?";
      $values[] = $date_start;
      $values[] = $date_end;
      $datatypes .= 'ss';
    }

    $query .= " ORDER BY bo.booking_id DESC";

    $res = select($query,$values,$datatypes);
    
    $limit_query = $query ." LIMIT $start,$limit";
    $limit_res = select($limit_query,$values,$datatypes);

    $total_rows = mysqli_num_rows($res);

    if($total_rows==0){
      $output = json_encode(["table_data"=>"<b>No Data Found!</b>", "pagination"=>'']);
      echo $output;
      exit;
    }

    $i=$start+1;
    $table_data = "";

    while($data = mysqli_fetch_assoc($limit_res))
    {
      $date = date("d-m-Y",strtotime($data['datentime']));
      $checkin = date("d-m-Y",strtotime($data['check_in']));
      $checkout = date("d-m-Y",strtotime($data['check_out']));

      if($data['booking_status']=='booked'){
        $status_bg = 'bg-success';
      }
      else if($data['booking_status']=='cancelled'){
        $status_bg = 'bg-danger';
      }
      else{
        $status_bg = 'bg-warning text-dark';
      }

      // Build proof-of-billing display from payment_proof field
      $proofFile = $data['payment_proof'] ?? '';
      $proofUrl = '';
      if($proofFile){
        if(filter_var($proofFile, FILTER_VALIDATE_URL)){
          $proofUrl = $proofFile;
        } elseif(strpos($proofFile, 'uploads/') === 0){
          $proofUrl = SITE_URL . ltrim($proofFile, '/');
        } elseif(strpos($proofFile, '/') === 0){
          $proofUrl = SITE_URL . ltrim($proofFile, '/');
        } else {
          // Default to billing_proofs folder (offline proofs);
          // other flows can still store a full/relative path in payment_proof.
          $proofUrl = SITE_URL.'uploads/billing_proofs/'.$proofFile;
        }
      }

      if($proofFile && $proofUrl){
        $safeProofUrl = htmlspecialchars($proofUrl, ENT_QUOTES);
        $proofHtml = "
          <span class='badge bg-info text-dark'>Proof of billing on file</span>
          <br>
          <a href='{$safeProofUrl}' target='_blank' class='btn btn-outline-primary btn-sm fw-bold shadow-none mt-1'>
            <i class='bi bi-receipt-cutoff me-1'></i> View Proof
          </a>
        ";
      } else {
        $proofHtml = "<span class='badge bg-secondary'>No proof of billing</span>";
      }
      
      $table_data .="
        <tr>
          <td>$i</td>
          <td>
            <span class='badge bg-primary'>
              Order ID: $data[order_id]
            </span>
            <br>
            <b>Name:</b> $data[user_name]
            <br>
            <b>Phone No:</b> $data[phonenum]
          </td>
          <td>
            <b>Room:</b> $data[room_name]
            <br>
            <b>Price:</b> ₱$data[price]
          </td>
          <td>
            <b>Amount:</b> ₱$data[trans_amt]
            <br>
            <b>Date:</b> $date
            <br>
            $proofHtml
          </td>
          <td>
            <span class='badge $status_bg'>$data[booking_status]</span>
          </td>
          <td>
            <button type='button' onclick='download($data[booking_id])' class='btn btn-outline-success btn-sm fw-bold shadow-none'>
              <i class='bi bi-file-earmark-arrow-down-fill'></i>
            </button>
          </td>
        </tr>
      ";

      $i++;
    }

    $pagination = "";

    if($total_rows>$limit)
    {
      $total_pages = ceil($total_rows/$limit); 

      if($page!=1){
        $pagination .="<li class='page-item'>
          <button onclick='change_page(1)' class='page-link shadow-none'>First</button>
        </li>";
      }

      $disabled = ($page==1) ? "disabled" : "";
      $prev= $page-1;
      $pagination .="<li class='page-item $disabled'>
        <button onclick='change_page($prev)' class='page-link shadow-none'>Prev</button>
      </li>";


      $disabled = ($page==$total_pages) ? "disabled" : "";
      $next = $page+1;
      $pagination .="<li class='page-item $disabled'>
        <button onclick='change_page($next)' class='page-link shadow-none'>Next</button>
      </li>";

      if($page!=$total_pages){
        $pagination .="<li class='page-item'>
          <button onclick='change_page($total_pages)' class='page-link shadow-none'>Last</button>
        </li>";
      }

    }

    $output = json_encode(["table_data"=>$table_data,"pagination"=>$pagination]);

    echo $output;
  }

?>