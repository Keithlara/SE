<?php

  require('../inc/db_config.php');
  require('../inc/essentials.php');
  date_default_timezone_set("Asia/Manila");
  adminLogin();

  // Staff access: allow core reports endpoints (metrics, exports, occupancy map)
  if(($_SESSION['adminRole'] ?? 'admin') === 'staff'){
    $allowed_staff_actions =
      isset($_POST['get_occupancy_map']) ||
      isset($_POST['get_metrics']) ||
      (isset($_GET['export']) && in_array($_GET['export'],['csv','pdf'], true));

    if(!$allowed_staff_actions){
      http_response_code(403);
      header('Content-Type: application/json');
      echo json_encode(['status'=>0,'msg'=>'Forbidden']);
      exit;
    }
  }

  function parse_range($frm){
    $granularity = $frm['granularity'] ?? 'daily';
    $today = new DateTime();
    if(($frm['from'] ?? '') && ($frm['to'] ?? '')){
      $from = new DateTime($frm['from']);
      $to = new DateTime($frm['to']);
    } else if($granularity==='monthly'){
      $from = (new DateTime($today->format('Y-m-01')));
      $to = clone $today; 
    } else if($granularity==='yearly'){
      $from = new DateTime($today->format('Y-01-01'));
      $to = clone $today;
    } else {
      $from = clone $today; $to = clone $today;
    }
    $to->setTime(23,59,59);
    return [$from,$to,$granularity];
  }

  function kpis($from,$to){
    $con = $GLOBALS['con'];
    $fromStr = $from->format('Y-m-d');
    $toStr = $to->format('Y-m-d');

    $resCount = mysqli_fetch_assoc(select(
      "SELECT COUNT(*) c FROM booking_order WHERE booking_status='booked' AND check_in>=? AND check_out<=?",
      [$fromStr,$toStr],'ss'))['c'];

    $cancelCount = mysqli_fetch_assoc(select(
      "SELECT COUNT(*) c FROM booking_order WHERE booking_status='cancelled' AND check_in>=? AND check_out<=?",
      [$fromStr,$toStr],'ss'))['c'];

    $revenue = mysqli_fetch_assoc(select(
      "SELECT COALESCE(SUM(trans_amt),0) s FROM booking_order WHERE trans_status='TXN_SUCCESS' AND check_in>=? AND check_out<=?",
      [$fromStr,$toStr],'ss'))['s'];

    $refundProcessed = mysqli_fetch_assoc(select(
      "SELECT COUNT(*) c FROM booking_order WHERE refund=1 AND check_in>=? AND check_out<=?",
      [$fromStr,$toStr],'ss'))['c'];

    $repeatGuests = mysqli_fetch_assoc(select(
      "SELECT COUNT(*) c FROM (
        SELECT user_id FROM booking_order
        WHERE check_in>=? AND check_out<=?
        GROUP BY user_id
        HAVING COUNT(*) > 1
      ) repeaters",
      [$fromStr,$toStr],'ss'))['c'];

    $topRoomRes = select(
      "SELECT bd.room_name, COUNT(*) c
       FROM booking_order bo
       INNER JOIN booking_details bd ON bd.booking_id = bo.booking_id
       WHERE bo.check_in>=? AND bo.check_out<=?
       GROUP BY bd.room_name
       ORDER BY c DESC, bd.room_name ASC
       LIMIT 1",
      [$fromStr,$toStr],
      'ss'
    );
    $topRoomRow = mysqli_fetch_assoc($topRoomRes);

    $topExtra = 'None';
    if(function_exists('appSchemaTableExists') && appSchemaTableExists($con, 'booking_extras')){
      $topExtraRes = select(
        "SELECT be.name, SUM(be.quantity) qty
         FROM booking_extras be
         INNER JOIN booking_order bo ON bo.booking_id = be.booking_id
         WHERE bo.check_in>=? AND bo.check_out<=?
         GROUP BY be.name
         ORDER BY qty DESC, be.name ASC
         LIMIT 1",
        [$fromStr,$toStr],
        'ss'
      );
      $topExtraRow = mysqli_fetch_assoc($topExtraRes);
      if(!empty($topExtraRow['name'])){
        $topExtra = $topExtraRow['name'] . ' (' . (int)$topExtraRow['qty'] . ')';
      }
    }

    // occupancy
    $nightsBookedRes = select(
      "SELECT SUM(DATEDIFF(check_out,check_in)) nights FROM booking_order WHERE booking_status='booked' AND check_in>=? AND check_out<=?",
      [$fromStr,$toStr],'ss');
    $nightsBooked = (int)(mysqli_fetch_assoc($nightsBookedRes)['nights'] ?? 0);

    $days = max(1,(int)$from->diff($to)->days + 1);
    // no-params query: use mysqli_query directly
    $capRes = mysqli_query($con, "SELECT COALESCE(SUM(quantity),0) q FROM rooms WHERE status=1 AND removed=0");
    $totalRooms = 0;
    if($capRes){ $rowCap = mysqli_fetch_assoc($capRes); $totalRooms = (int)($rowCap['q'] ?? 0); }
    $capacityNights = $totalRooms * $days;
    $occupancy = $capacityNights>0 ? round(($nightsBooked/$capacityNights)*100,2) : 0;

    // payment breakdown by status
    $pb = [];
    $pbRes = select("SELECT trans_status, COUNT(*) c FROM booking_order WHERE check_in>=? AND check_out<=? GROUP BY trans_status",[$fromStr,$toStr],'ss');
    while($r=mysqli_fetch_assoc($pbRes)){ $pb[$r['trans_status']] = (int)$r['c']; }

    return [
      'reservations'=>(int)$resCount,
      'cancelled'=>(int)$cancelCount,
      'revenue'=>(int)$revenue,
      'occupancy'=>$occupancy,
      'refund_rate'=>($resCount + $cancelCount) > 0 ? round((((int)$refundProcessed) / max(1, ((int)$resCount + (int)$cancelCount))) * 100, 2) : 0,
      'repeat_guests'=>(int)$repeatGuests,
      'top_room'=>$topRoomRow['room_name'] ?? 'No bookings yet',
      'top_extra'=>$topExtra,
      'payment_breakdown'=>$pb
    ];
  }

  if(isset($_POST['get_metrics'])){
    header('Content-Type: application/json');
    $frm = filteration($_POST);
    [$from,$to,$gran] = parse_range($frm);

    $metrics = kpis($from,$to);

    // series for charts
    $series = [];
    $labels = [];
    $cursor = clone $from;
    while($cursor <= $to){
      if($gran==='yearly'){
        $start = (clone $cursor)->modify('first day of january');
        $label = $cursor->format('Y');
        $cursor->modify('+1 year');
        $end = (clone $cursor)->modify('-1 day');
      } else if($gran==='monthly'){
        $start = (clone $cursor)->modify('first day of this month');
        $label = $cursor->format('M Y');
        $cursor->modify('first day of next month');
        $end = (clone $cursor)->modify('-1 day');
      } else {
        $start = clone $cursor; $label = $cursor->format('Y-m-d'); $end = clone $cursor; $cursor->modify('+1 day');
      }
      if($end>$to) $end = clone $to;
      $labels[] = $label;
      $cnt = mysqli_fetch_assoc(select(
        "SELECT COUNT(*) c FROM booking_order WHERE booking_status='booked' AND check_in>=? AND check_out<=?",
        [$start->format('Y-m-d'), $end->format('Y-m-d')],'ss'))['c'];
      $series[] = (int)$cnt;
    }

    echo json_encode(['metrics'=>$metrics,'labels'=>$labels,'series'=>$series]);
    exit;
  }

  if(isset($_GET['export']) && in_array($_GET['export'],['csv','pdf'])){
    $frm = filteration($_GET);
    [$from,$to,$gran] = parse_range($frm);
    $metrics = kpis($from,$to);
    if($_GET['export']=='csv'){
      header('Content-Type: text/csv');
      header('Content-Disposition: attachment; filename="reports_'.date('Ymd_His').'.csv"');
      $out = fopen('php://output','w');
      fputcsv($out,['Metric','Value']);
      foreach($metrics as $k=>$v){ if($k=='payment_breakdown'){ continue; } fputcsv($out,[$k,$v]); }
      foreach($metrics['payment_breakdown'] as $k=>$v){ fputcsv($out,["payment_".$k,$v]); }
      fclose($out); exit;
    }
    if($_GET['export']=='pdf'){
      require_once('../inc/mpdf/vendor/autoload.php');
      $rows = '';
      foreach($metrics as $k=>$v){
        if($k=='payment_breakdown') continue;
        $rows .= "<tr><td>".htmlspecialchars($k)."</td><td>".htmlspecialchars((string)$v)."</td></tr>";
      }
      foreach($metrics['payment_breakdown'] as $k=>$v){
        $rows .= "<tr><td>payment_".htmlspecialchars($k)."</td><td>".htmlspecialchars((string)$v)."</td></tr>";
      }
      $html = "<h3>Reports</h3><table border='1' cellpadding='6' cellspacing='0' width='100%'><thead><tr><th>Metric</th><th>Value</th></tr></thead><tbody>".$rows."</tbody></table>";
      $mpdf = new \Mpdf\Mpdf();
      $mpdf->WriteHTML($html);
      $mpdf->Output('reports_'.date('Ymd_His').'.pdf','D');
      exit;
    }
  }

  if(isset($_POST['get_occupancy_map'])){
    header('Content-Type: application/json');
    $frm = filteration($_POST);
    $dateStr = $frm['date'] ?? (new DateTime())->format('Y-m-d');

    // load rooms that are active and not removed
    $rooms = [];
    $roomsRes = mysqli_query($GLOBALS['con'], "SELECT id,name,quantity FROM rooms WHERE status=1 AND removed=0");
    if($roomsRes){
      while($r = mysqli_fetch_assoc($roomsRes)){
        $rooms[] = $r;
      }
    }

    $out = [];

    foreach($rooms as $room){
      $roomId = (int)$room['id'];
      $qty = (int)$room['quantity'];
      $seats = [];
      for($i=1;$i<=$qty;$i++){
        $seats[$i] = [
          'label' => (string)$i,
          'status' => 'available', // available | pending | occupied
          'booking_id' => null,
          'room_no' => null
        ];
      }

      // bookings overlapping the date: check_in <= date < check_out, booked and arrived
      $bRes = select(
        "SELECT bo.booking_id, bo.booking_status, bo.arrival
         FROM booking_order bo
         WHERE bo.room_id=? 
           AND bo.booking_status='booked' 
           AND bo.arrival IN (0,1)
           AND bo.check_in <= ? AND bo.check_out > ?",
        [$roomId,$dateStr,$dateStr],
        'iss'
      );

      $bookingIds = [];
      $bookingStatusMap = [];
      while($b = mysqli_fetch_assoc($bRes)){
        $bid = (int)$b['booking_id'];
        $bookingIds[] = $bid;
        $status = ($b['booking_status']==='booked' && (int)$b['arrival']===1) ? 'occupied' : 'pending';
        $bookingStatusMap[$bid] = $status;
      }

      if(count($bookingIds)){
        $in = implode(',', array_fill(0, count($bookingIds), '?'));
        $types = str_repeat('i', count($bookingIds));
        $params = $bookingIds;

        // Assign explicit room numbers first
        $detailsRes = select(
          "SELECT booking_id, room_no 
           FROM booking_details 
           WHERE booking_id IN ($in) AND room_no IS NOT NULL AND room_no<>''",
          $params,
          $types
        );

        $occupiedByNo = [];

        while($d = mysqli_fetch_assoc($detailsRes)){
          $roomNoRaw = trim((string)$d['room_no']);
          $label = null;
          if(ctype_digit($roomNoRaw)){
            $num = (int)$roomNoRaw;
            if($num>=1 && $num<=$qty){ $label = $num; }
          }
          if($label === null){
            for($i=1;$i<=$qty;$i++){
              if((string)$i === $roomNoRaw){ $label = $i; break; }
            }
          }
          if($label !== null && isset($seats[$label]) && $seats[$label]['status']==='available'){
            $seats[$label]['status'] = $bookingStatusMap[(int)$d['booking_id']] ?? 'occupied';
            $seats[$label]['booking_id'] = (int)$d['booking_id'];
            $seats[$label]['room_no'] = $roomNoRaw;
            $occupiedByNo[$label] = true;
          }
        }

        // Fill remaining seats by status counts
        $needOccupied = 0; $needPending = 0;
        foreach($bookingIds as $bid){
          $status = $bookingStatusMap[$bid] ?? 'occupied';
          if($status==='occupied') $needOccupied++; else $needPending++;
        }
        // subtract already assigned explicit
        foreach($occupiedByNo as $idx=>$_v){
          $assignedStatus = $seats[$idx]['status'];
          if($assignedStatus==='occupied') $needOccupied--; else if($assignedStatus==='pending') $needPending--;
        }
        for($i=1;$i<=$qty && ($needOccupied>0 || $needPending>0);$i++){
          if($seats[$i]['status']==='available'){
            if($needOccupied>0){ $seats[$i]['status'] = 'occupied'; $needOccupied--; }
            else if($needPending>0){ $seats[$i]['status'] = 'pending'; $needPending--; }
          }
        }
      }

      $flat = [];
      for($i=1;$i<=$qty;$i++){ $flat[] = $seats[$i]; }

      $out[] = [
        'room_id' => $roomId,
        'name' => $room['name'],
        'quantity' => $qty,
        'seats' => $flat
      ];
    }

    echo json_encode(['date'=>$dateStr,'rooms'=>$out]);
    exit;
  }

  header('Content-Type: application/json');
  echo json_encode(['status'=>0,'msg'=>'Invalid request']);
?>
