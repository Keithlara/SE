<?php 

  require('admin/inc/essentials.php');
  require('admin/inc/db_config.php');
  require('admin/inc/mpdf/vendor/autoload.php');

  session_start();

  if(!(isset($_SESSION['login']) && $_SESSION['login']==true)){
    redirect('index.php');
  }

  if(isset($_GET['gen_pdf']) && isset($_GET['id']))
  {
    $frm_data = filteration($_GET);

    $query = "SELECT bo.*, bd.*,uc.email FROM `booking_order` bo
      INNER JOIN `booking_details` bd ON bo.booking_id = bd.booking_id
      INNER JOIN `user_cred` uc ON bo.user_id = uc.id
      WHERE ((bo.booking_status='booked' AND bo.arrival=1) 
      OR (bo.booking_status='cancelled' AND bo.refund=1)
      OR (bo.booking_status='payment failed')) 
      AND bo.booking_id = '$frm_data[id]'";

    $res = mysqli_query($con,$query);
    $total_rows = mysqli_num_rows($res);

    if($total_rows==0){
      header('location: index.php');
      exit;
    }

    $data = mysqli_fetch_assoc($res);

    $date = date("h:ia | d-m-Y",strtotime($data['datentime']));
    $checkin = date("d-m-Y",strtotime($data['check_in']));
    $checkout = date("d-m-Y",strtotime($data['check_out']));

    // Fetch extras for this booking
    $extras_res = mysqli_query($con, "SELECT * FROM `booking_extras` WHERE `booking_id`='".(int)$data['booking_id']."'");
    $extras_rows = [];
    while($ex = mysqli_fetch_assoc($extras_res)) $extras_rows[] = $ex;

    $table_data = "
    <h2>BOOKING RECEIPT</h2>
    <table border='1'>
      <tr>
        <td>Order ID: $data[order_id]</td>
        <td>Booking Date: $date</td>
      </tr>
      <tr>
        <td colspan='2'>Status: $data[booking_status]</td>
      </tr>
      <tr>
        <td>Name: $data[user_name]</td>
        <td>Email: $data[email]</td>
      </tr>
      <tr>
        <td>Phone Number: $data[phonenum]</td>
        <td>Address: $data[address]</td>
      </tr>
      <tr>
        <td>Room Name: $data[room_name]</td>
        <td>Cost: &#8369;$data[price] per night</td>
      </tr>
      <tr>
        <td>Check-in: $checkin</td>
        <td>Check-out: $checkout</td>
      </tr>
    ";

    if($data['booking_status']=='cancelled')
    {
      $refund = ($data['refund']) ? "Amount Refunded" : "Not Yet Refunded";

      $table_data.="<tr>
        <td>Amount Paid: &#8369;$data[trans_amt]</td>
        <td>Refund: $refund</td>
      </tr>";
    }
    else if($data['booking_status']=='payment failed')
    {
      $table_data.="<tr>
        <td>Transaction Amount: &#8369;$data[trans_amt]</td>
        <td>Failure Response: $data[trans_resp_msg]</td>
      </tr>";
    }
    else
    {
      $table_data.="<tr>
        <td>Room Number: $data[room_no]</td>
        <td>Amount Paid: &#8369;$data[trans_amt]</td>
      </tr>";
    }

    $table_data.="</table>";

    // Extras section
    if(!empty($extras_rows)){
      $count_days = (new DateTime($data['check_in']))->diff(new DateTime($data['check_out']))->days;
      $extras_total = 0;
      $table_data .= "
      <h3 style='margin-top:16px;'>Add-on Extras</h3>
      <table border='1'>
        <tr>
          <th>#</th>
          <th>Extra</th>
          <th>Qty</th>
          <th>Unit Price/night</th>
          <th>Subtotal</th>
        </tr>
      ";
      $ei = 1;
      foreach($extras_rows as $ex){
        $ex_total = $ex['unit_price'] * $ex['quantity'] * $count_days;
        $extras_total += $ex_total;
        $table_data .= "<tr>
          <td>$ei</td>
          <td>".htmlspecialchars($ex['name'])."</td>
          <td>$ex[quantity]</td>
          <td>&#8369;".number_format($ex['unit_price'],2)."</td>
          <td>&#8369;".number_format($ex_total,2)."</td>
        </tr>";
        $ei++;
      }
      $table_data .= "<tr>
        <td colspan='4'><b>Extras Total</b></td>
        <td><b>&#8369;".number_format($extras_total,2)."</b></td>
      </tr>
      </table>";
    }

    // Set a custom temporary directory with proper permissions
    $tempDir = sys_get_temp_dir() . '/mpdf';
    if (!file_exists($tempDir)) {
        mkdir($tempDir, 0777, true);
    }
    
    $mpdf = new \Mpdf\Mpdf([
        'tempDir' => $tempDir,
        'default_font' => 'dejavusans'
    ]);
    $mpdf->WriteHTML($table_data);
    $mpdf->Output($data['order_id'].'.pdf','D');

  }
  else{
    header('location: index.php');
  }
  
?>