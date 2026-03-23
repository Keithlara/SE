<?php
  // CLI/cron entrypoint to create a daily backup.
  // Run at midnight via Task Scheduler or cron:
  // php C:\xampp\htdocs\SE\admin\cron\daily_backup.php

  require('../inc/db_config.php');
  date_default_timezone_set("Asia/Kolkata");

  $backupDir = getenv('SE_BACKUP_PATH');
  if(!$backupDir || trim($backupDir)==''){
    $backupDir = $_SERVER['DOCUMENT_ROOT'].'/SE/backups';
  }
  if(!is_dir($backupDir)){
    @mkdir($backupDir,0775,true);
  }

  $db = $GLOBALS['db'];
  $filename = $db.'_'.date('Ymd_His').'.sql';
  $path = rtrim($backupDir,'/\\').DIRECTORY_SEPARATOR.$filename;

  $hname = $GLOBALS['hname'];
  $uname = $GLOBALS['uname'];
  $pass = $GLOBALS['pass'];
  $db = $GLOBALS['db'];

  $mysqldump = 'mysqldump';
  $cmd = "$mysqldump --user=\"$uname\" --password=\"$pass\" --host=\"$hname\" --databases \"$db\" --add-drop-database --routines --events --triggers > \"$path\"";
  $result = null; $output = [];
  @exec($cmd, $output, $result);

  if($result===0 && file_exists($path) && filesize($path)>0){
    echo "Backup created: $path\n";
    exit(0);
  }

  // Fallback (basic export)
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
  echo file_exists($path) ? "Backup created: $path\n" : "Backup failed\n";
?>


