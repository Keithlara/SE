<?php
  require('../inc/db_config.php');
  require('../inc/essentials.php');
  date_default_timezone_set("Asia/Manila");
  adminLogin();

  // Ensure table exists (idempotent)
  mysqli_query($con, "CREATE TABLE IF NOT EXISTS transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NULL,
    guest_name VARCHAR(100) NOT NULL,
    room_no VARCHAR(50) DEFAULT NULL,
    amount INT NOT NULL,
    method VARCHAR(50) NOT NULL,
    status VARCHAR(50) NOT NULL,
    type VARCHAR(50) NOT NULL,
    admin_id INT NOT NULL,
    datentime DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  function column_exists($con, $table, $column){
    $table = mysqli_real_escape_string($con, $table);
    $column = mysqli_real_escape_string($con, $column);
    $res = mysqli_query($con, "SHOW COLUMNS FROM `$table` LIKE '$column'");
    return ($res && mysqli_num_rows($res) > 0);
  }

  /**
   * Keep transactions history aligned with booking payments/refunds.
   * - Inserts missing "payment" transactions for bookings with trans_amt > 0.
   * - Inserts missing "refund" transactions for bookings marked refund=1.
   * Idempotent: only inserts if a matching booking_id+type row doesn't exist.
   */
  function sync_transactions_from_bookings($con){
    $has_refund_amount = column_exists($con, 'booking_order', 'refund_amount');

    // Insert payment transactions (paid/pending/failed) from booking_order + booking_details
    mysqli_query($con, "
      INSERT INTO transactions (booking_id, guest_name, room_no, amount, method, status, type, admin_id, datentime)
      SELECT
        bo.booking_id,
        COALESCE(bd.user_name, CONCAT('Guest #', bo.user_id)) AS guest_name,
        bd.room_no,
        bo.trans_amt,
        'online' AS method,
        CASE
          WHEN bo.trans_status = 'TXN_SUCCESS' THEN 'paid'
          WHEN bo.trans_status = 'pending' THEN 'pending'
          WHEN bo.trans_status IS NULL OR bo.trans_status = '' THEN 'pending'
          ELSE 'failed'
        END AS status,
        'payment' AS type,
        0 AS admin_id,
        bo.datentime
      FROM booking_order bo
      LEFT JOIN booking_details bd ON bd.booking_id = bo.booking_id
      LEFT JOIN transactions t ON t.booking_id = bo.booking_id AND t.type = 'payment'
      WHERE bo.trans_amt > 0 AND t.id IS NULL
    ");

    // Insert refund transactions for refunded bookings
    $refundAmountExpr = $has_refund_amount ? "COALESCE(bo.refund_amount, ROUND(bo.trans_amt * 0.5))" : "ROUND(bo.trans_amt * 0.5)";
    mysqli_query($con, "
      INSERT INTO transactions (booking_id, guest_name, room_no, amount, method, status, type, admin_id, datentime)
      SELECT
        bo.booking_id,
        COALESCE(bd.user_name, CONCAT('Guest #', bo.user_id)) AS guest_name,
        bd.room_no,
        $refundAmountExpr AS amount,
        'online' AS method,
        'refunded' AS status,
        'refund' AS type,
        0 AS admin_id,
        NOW()
      FROM booking_order bo
      LEFT JOIN booking_details bd ON bd.booking_id = bo.booking_id
      LEFT JOIN transactions t ON t.booking_id = bo.booking_id AND t.type = 'refund'
      WHERE bo.refund = 1 AND t.id IS NULL
    ");
  }

  if(isset($_POST['list'])){
    // Make sure we have something to show even if nothing ever called "record"
    sync_transactions_from_bookings($con);

    $frm = filteration($_POST);
    $page = max(1, (int)($frm['page'] ?? 1));
    $limit = max(1, (int)($frm['limit'] ?? 10));
    $start = ($page-1)*$limit;

    $where = [];$params=[];$types='';
    if(($frm['from'] ?? '') && ($frm['to'] ?? '')){ $where[] = 'DATE(datentime) BETWEEN ? AND ?'; $params[]=$frm['from']; $params[]=$frm['to']; $types.='ss'; }
    if(($frm['method'] ?? '')!=''){ $where[]='method LIKE ?'; $params[]='%'.$frm['method'].'%'; $types.='s'; }
    if(($frm['status'] ?? '')!=''){ $where[]='status=?'; $params[]=$frm['status']; $types.='s'; }
    if(($frm['search'] ?? '')!=''){ $where[]='(guest_name LIKE ? OR room_no LIKE ?)'; $params[]='%'.$frm['search'].'%'; $params[]='%'.$frm['search'].'%'; $types.='ss'; }
    $whereSql = count($where)? (' WHERE '.implode(' AND ',$where)) : '';

    $count = mysqli_fetch_assoc(select('SELECT COUNT(*) c FROM transactions'.$whereSql,$params,$types))['c'];
    $rows = select('SELECT * FROM transactions'.$whereSql.' ORDER BY id DESC LIMIT '.$start.','.$limit,$params,$types);

    $table=''; $i=$start+1;
    while($r = mysqli_fetch_assoc($rows)){
      $table .= "<tr><td>$i</td><td>".htmlspecialchars($r['guest_name'])."<br><small>Room: ".htmlspecialchars((string)$r['room_no'])."</small></td><td>".date('Y-m-d H:i',strtotime($r['datentime']))."</td><td>₱".$r['amount']."</td><td>".htmlspecialchars($r['method'])."</td><td><span class='badge bg-".($r['status']=='paid'?'success':($r['status']=='refunded'?'secondary':'warning text-dark'))."'>".htmlspecialchars($r['status'])."</span></td><td>".htmlspecialchars($r['type'])."</td></tr>";
      $i++;
    }

    $pagination='';
    if($count>$limit){
      $totalPages = ceil($count/$limit);
      $prev = max(1,$page-1); $next=min($totalPages,$page+1);
      $pagination = "<li class='page-item ".($page==1?'disabled':'')."'><button onclick='change_page(1)' class='page-link shadow-none'>First</button></li>";
      $pagination .= "<li class='page-item ".($page==1?'disabled':'')."'><button onclick='change_page($prev)' class='page-link shadow-none'>Prev</button></li>";
      $pagination .= "<li class='page-item ".($page==$totalPages?'disabled':'')."'><button onclick='change_page($next)' class='page-link shadow-none'>Next</button></li>";
      $pagination .= "<li class='page-item ".($page==$totalPages?'disabled':'')."'><button onclick='change_page($totalPages)' class='page-link shadow-none'>Last</button></li>";
    }

    echo json_encode(['table'=>$table,'pagination'=>$pagination,'total'=>(int)$count]);
    exit;
  }

  if(isset($_GET['export']) && in_array($_GET['export'],['csv','pdf'])){
    // Ensure exports also include derived booking transactions
    sync_transactions_from_bookings($con);

    $frm = filteration($_GET);
    $where = [];$params=[];$types='';
    if(($frm['from'] ?? '') && ($frm['to'] ?? '')){ $where[] = 'DATE(datentime) BETWEEN ? AND ?'; $params[]=$frm['from']; $params[]=$frm['to']; $types.='ss'; }
    if(($frm['method'] ?? '')!=''){ $where[]='method LIKE ?'; $params[]='%'.$frm['method'].'%'; $types.='s'; }
    if(($frm['status'] ?? '')!=''){ $where[]='status=?'; $params[]=$frm['status']; $types.='s'; }
    if(($frm['search'] ?? '')!=''){ $where[]='(guest_name LIKE ? OR room_no LIKE ?)'; $params[]='%'.$frm['search'].'%'; $params[]='%'.$frm['search'].'%'; $types.='ss'; }
    $whereSql = count($where)? (' WHERE '.implode(' AND ',$where)) : '';
    $rows = select('SELECT * FROM transactions'.$whereSql.' ORDER BY id DESC',$params,$types);

    if($_GET['export']=='csv'){
      header('Content-Type: text/csv');
      header('Content-Disposition: attachment; filename="transactions_'.date('Ymd_His').'.csv"');
      $out = fopen('php://output','w');
      fputcsv($out,['ID','Guest','Room','Date','Amount','Method','Status','Type']);
      while($r=mysqli_fetch_assoc($rows)){
        fputcsv($out,[$r['id'],$r['guest_name'],$r['room_no'],$r['datentime'],$r['amount'],$r['method'],$r['status'],$r['type']]);
      }
      fclose($out); exit;
    }
    if($_GET['export']=='pdf'){
      require_once('../inc/mpdf/vendor/autoload.php');
      $htmlRows='';
      while($r=mysqli_fetch_assoc($rows)){
        $htmlRows .= "<tr><td>{$r['id']}</td><td>".htmlspecialchars($r['guest_name'])."</td><td>".htmlspecialchars((string)$r['room_no'])."</td><td>{$r['datentime']}</td><td>₱{$r['amount']}</td><td>".htmlspecialchars($r['method'])."</td><td>".htmlspecialchars($r['status'])."</td><td>".htmlspecialchars($r['type'])."</td></tr>";
      }
      $html = "<h3>Transactions</h3><table border='1' cellpadding='6' cellspacing='0' width='100%'><thead><tr><th>ID</th><th>Guest</th><th>Room</th><th>Date</th><th>Amount</th><th>Method</th><th>Status</th><th>Type</th></tr></thead><tbody>$htmlRows</tbody></table>";
      $mpdf = new \Mpdf\Mpdf();
      $mpdf->WriteHTML($html);
      $mpdf->Output('transactions_'.date('Ymd_His').'.pdf','D');
      exit;
    }
  }

  // helper to record a transaction; example usage when refund issued
  if(isset($_POST['record'])){
    $frm = filteration($_POST);
    $adminId = $_SESSION['adminId'];
    $ins = insert(
      "INSERT INTO transactions(booking_id,guest_name,room_no,amount,method,status,type,admin_id) VALUES (?,?,?,?,?,?,?,?)",
      [
        $frm['booking_id'] ?? null,
        $frm['guest_name'],
        $frm['room_no'] ?? null,
        (int)$frm['amount'],
        $frm['method'],
        $frm['status'],
        $frm['type'],
        $adminId
      ],
      'ississsi'
    );
    echo $ins?1:0; exit;
  }

  echo json_encode(['status'=>0,'msg'=>'Invalid request']);
?>

