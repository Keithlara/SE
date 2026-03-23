<?php 
require('../inc/db_config.php');
require('../inc/essentials.php');
adminLogin();

if(isset($_POST['get_archived_bookings']))
{
  $query = "SELECT abo.*, abd.*, r.name as room_name 
            FROM `archived_booking_order` abo 
            INNER JOIN `archived_booking_details` abd ON abo.booking_id = abd.booking_id
            LEFT JOIN `rooms` r ON abo.room_id = r.id
            ORDER BY abo.archived_at DESC";
  
  $res = mysqli_query($con, $query);
  
  $i = 1;
  $table_data = "";

  if(mysqli_num_rows($res) == 0) {
    echo "<tr><td colspan='6' class='text-center py-4'>No archived bookings found</td></tr>";
    exit;
  }

  while($data = mysqli_fetch_assoc($res))
  {
    $date = date("d-m-Y", strtotime($data['datentime']));
    $checkin = date("d-m-Y", strtotime($data['check_in']));
    $checkout = date("d-m-Y", strtotime($data['check_out']));
    $archived_date = date("d-m-Y H:i", strtotime($data['archived_at']));

    $status_badge = "";
    if($data['booking_status'] == 'cancelled') {
      $status_badge = "<span class='badge bg-danger'>Cancelled</span>";
    } elseif($data['booking_status'] == 'booked') {
      $status_badge = "<span class='badge bg-success'>Completed</span>";
    } else {
      $status_badge = "<span class='badge bg-secondary'>" . ucfirst($data['booking_status']) . "</span>";
    }

    $note_html = '';
    if(isset($data['booking_note']) && trim((string)$data['booking_note']) !== ''){
      $note_html = "<br><b>Note:</b> <span class='text-muted small'>".nl2br(htmlspecialchars((string)$data['booking_note'], ENT_QUOTES, 'UTF-8'))."</span>";
    }

    $table_data .= "
      <tr>
        <td>$i</td>
        <td>
          <span class='badge bg-primary'>$data[order_id]</span>
        </td>
        <td>
          <b>Name:</b> $data[user_name]<br>
          <b>Phone:</b> $data[phonenum]<br>
          <b>Address:</b> " . substr($data['address'], 0, 30) . "..."
          . $note_html .
        "</td>
        <td>
          <b>Room:</b> " . ($data['room_name'] ?? 'N/A') . "<br>
          <b>Room No:</b> " . ($data['room_no'] ?? 'N/A') . "<br>
          <b>Price:</b> ₱$data[price] per night
        </td>
        <td>
          <b>Check-in:</b> $checkin<br>
          <b>Check-out:</b> $checkout<br>
          <b>Booked On:</b> $date<br>
          <b>Archived On:</b> $archived_date
        </td>
        <td>
          $status_badge<br>
          <small class='text-muted'>$data[trans_status]</small>
        </td>
      </tr>";

    $i++;
  }

  echo $table_data;
}

// Add more archive-related handlers here as needed

?>
