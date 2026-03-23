<?php

  require('../inc/db_config.php');
  require('../inc/essentials.php');
  date_default_timezone_set("Asia/Kolkata");
  adminLogin();

  // environment variable for backup path
  $backupDir = getenv('SE_BACKUP_PATH');
  if(!$backupDir || trim($backupDir)==''){
    // fallback to project backups directory
    $backupDir = $_SERVER['DOCUMENT_ROOT'].'/SE/backups';
  }
  if(!is_dir($backupDir)){
    @mkdir($backupDir,0775,true);
  }

  function list_backups($backupDir){
    $files = [];
    foreach(glob(rtrim($backupDir,'/\\').'/*.sql') as $file){
      $files[] = [
        'name' => basename($file),
        'size' => filesize($file),
        'mtime' => filemtime($file)
      ];
    }
    usort($files,function($a,$b){ return $b['mtime'] <=> $a['mtime']; });
    return $files;
  }

  if(isset($_POST['list_backups']))
  {
    $rows = list_backups($backupDir);
    echo json_encode(['status'=>1,'files'=>$rows,'dir'=>$backupDir]);
    exit;
  }

  if(isset($_POST['create_backup']))
  {
    $db = $GLOBALS['db'];
    $filename = $db.'_'.date('Ymd_His').'.sql';
    $path = rtrim($backupDir,'/\\').DIRECTORY_SEPARATOR.$filename;

    $hname = $GLOBALS['hname'];
    $uname = $GLOBALS['uname'];
    $pass = $GLOBALS['pass'];
    $db = $GLOBALS['db'];

    // Use mysqldump if available for reliability
    $mysqldump = 'mysqldump';
    $cmd = "$mysqldump --user=\"$uname\" --password=\"$pass\" --host=\"$hname\" --databases \"$db\" --add-drop-database --routines --events --triggers > \"$path\"";
    $result = null;
    $output = [];
    @exec($cmd, $output, $result);

    if($result===0 && file_exists($path) && filesize($path)>0){
      echo json_encode(['status'=>1,'file'=>basename($path)]);
      exit;
    }

    // Fallback: PHP-based export (no routines)
    $con = $GLOBALS['con'];
    $tables = [];
    $tres = mysqli_query($con, 'SHOW TABLES');
    while($r = mysqli_fetch_array($tres)) $tables[] = $r[0];

    $dump = "SET FOREIGN_KEY_CHECKS=0;\n";
    foreach($tables as $table){
      $cr = mysqli_fetch_row(mysqli_query($con, "SHOW CREATE TABLE `$table`"));
      $dump .= "\nDROP TABLE IF EXISTS `$table`;\n".$cr[1].";\n\n";
      $res = mysqli_query($con, "SELECT * FROM `$table`");
      while($row = mysqli_fetch_assoc($res)){
        $cols = array_map(function($c){ return "`".$c."`"; }, array_keys($row));
        $vals = array_map(function($v) use ($con){
          if($v===NULL) return 'NULL';
          return "'".mysqli_real_escape_string($con,$v)."'";
        }, array_values($row));
        $dump .= "INSERT INTO `$table` (".implode(',', $cols).") VALUES (".implode(',', $vals).");\n";
      }
    }
    $dump .= "\nSET FOREIGN_KEY_CHECKS=1;\n";
    file_put_contents($path,$dump);
    if(file_exists($path)){
      echo json_encode(['status'=>1,'file'=>basename($path)]);
    } else {
      echo json_encode(['status'=>0,'msg'=>'Backup failed']);
    }
    exit;
  }

  if(isset($_GET['download']))
  {
    $name = basename($_GET['download']);
    $path = rtrim($backupDir,'/\\').DIRECTORY_SEPARATOR.$name;
    if(is_file($path)){
      header('Content-Type: application/sql');
      header('Content-Disposition: attachment; filename="'.$name.'"');
      header('Content-Length: '.filesize($path));
      readfile($path);
      exit;
    }
    http_response_code(404);
    echo 'Not found';
    exit;
  }

  if(isset($_POST['restore_backup']))
  {
    $frm = filteration($_POST);
    if(!isset($frm['confirm']) || $frm['confirm']!='YES'){
      echo json_encode(['status'=>0,'msg'=>'Confirmation required']);
      exit;
    }
    $name = basename($frm['file']);
    $path = rtrim($backupDir,'/\\').DIRECTORY_SEPARATOR.$name;
    if(!is_file($path)){
      echo json_encode(['status'=>0,'msg'=>'Backup file not found']);
      exit;
    }

    $hname = $GLOBALS['hname'];
    $uname = $GLOBALS['uname'];
    $pass = $GLOBALS['pass'];
    $db = $GLOBALS['db'];

    $mysql = 'mysql';
    $cmd = "$mysql --user=\"$uname\" --password=\"$pass\" --host=\"$hname\" \"$db\" < \"$path\"";
    $result = null; $output = [];
    @exec($cmd, $output, $result);
    if($result===0){
      echo json_encode(['status'=>1]);
      exit;
    }

    // Fallback restore via PHP
    $sql = file_get_contents($path);
    $con = $GLOBALS['con'];
    $statements = array_filter(array_map('trim', preg_split('/;\s*\n/', $sql)));
    mysqli_begin_transaction($con);
    $ok = true;
    foreach($statements as $st){
      if($st==='') continue;
      if(!mysqli_query($con, $st.';')){ $ok=false; break; }
    }
    if($ok){
      mysqli_commit($con);
      echo json_encode(['status'=>1]);
    } else {
      mysqli_rollback($con);
      echo json_encode(['status'=>0,'msg'=>'Restore failed']);
    }
    exit;
  }

  echo json_encode(['status'=>0,'msg'=>'Invalid request']);
?>


