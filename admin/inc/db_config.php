<?php 

  // Database configuration
  // You can override these via environment variables:
  // DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PASS, DB_SOCK
  $hname = getenv('DB_HOST') ?: 'localhost';
  $port  = (int)(getenv('DB_PORT') ?: 3306);
  $uname = getenv('DB_USER') ?: 'root';
  $pass  = getenv('DB_PASS') ?: '';
  $db    = getenv('DB_NAME') ?: 'travelers_DB';
  $sock  = getenv('DB_SOCK') ?: '/tmp/mysql.sock';
  
  // File upload settings
  define('UPLOADS_PATH', $_SERVER['DOCUMENT_ROOT'] . '/uploads');
  
  // Email settings
  define('SMTP_HOST', 'smtp.gmail.com');
  define('SMTP_EMAIL', 'your-email@gmail.com'); // Replace with your email
  define('SMTP_PASSWORD', 'your-email-password'); // Replace with your email password
  define('SMTP_PORT', 587);
  define('SITE_NAME', 'Your Resort Name');

  // MySQLi connection for existing code
  mysqli_report(MYSQLI_REPORT_OFF);
  try {
    $con = mysqli_init();
    if(!$con){
      throw new Exception('mysqli_init failed');
    }
    // A small timeout helps avoid hanging pages when DB is down
    @mysqli_options($con, MYSQLI_OPT_CONNECT_TIMEOUT, 5);

    if(!@mysqli_real_connect($con, $hname, $uname, $pass, $db, $port, $sock)){
      $err = mysqli_connect_error();
      throw new Exception($err ?: 'Unknown connection error');
    }
  } catch (Throwable $e) {
    http_response_code(500);
    $safeErr = htmlspecialchars($e->getMessage(), ENT_QUOTES);
    die(
      "<div style='font-family:system-ui,Segoe UI,Arial;max-width:780px;margin:40px auto;padding:16px;border:1px solid #e5e7eb;border-radius:12px;background:#fff'>"
      ."<h2 style='margin:0 0 8px'>Database connection failed</h2>"
      ."<p style='margin:0 0 12px;color:#374151'>The app can’t connect to MySQL right now.</p>"
      ."<div style='padding:12px;background:#f9fafb;border:1px solid #eef2f7;border-radius:10px;color:#111827'><b>Error:</b> {$safeErr}</div>"
      ."<ul style='margin:12px 0 0;color:#374151'>"
      ."<li>Start <b>MySQL</b> in the XAMPP Control Panel</li>"
      ."<li>Verify the MySQL port (default <b>3306</b>) matches <code>DB_PORT</code> if you changed it</li>"
      ."<li>Confirm the database <b>{$db}</b> exists</li>"
      ."</ul>"
      ."</div>"
    );
  }

  // PDO connection for rooms map
  try {
    $dsn = $sock ? "mysql:unix_socket=$sock;dbname=$db;charset=utf8mb4" : "mysql:host=$hname;port=$port;dbname=$db;charset=utf8mb4";
    $pdo = new PDO($dsn, $uname, $pass, [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      PDO::ATTR_EMULATE_PREPARES => false,
    ]);
  } catch(PDOException $e) {
    http_response_code(500);
    $safeErr = htmlspecialchars($e->getMessage(), ENT_QUOTES);
    die("PDO Connection failed: " . $safeErr);
  }

  function filteration($data){
    foreach($data as $key => $value){
      $value = trim($value);
      $value = stripslashes($value);
      $value = strip_tags($value);
      $value = htmlspecialchars($value);
      $data[$key] = $value;
    }
    return $data;
  }

  function selectAll($table)
  {
    $con = $GLOBALS['con'];
    $res = mysqli_query($con,"SELECT * FROM $table");
    return $res;
  }

  function select($sql,$values,$datatypes)
  {
    $con = $GLOBALS['con'];
    if($stmt = mysqli_prepare($con,$sql))
    {
      if(!empty($values)){
        if($datatypes === '' || strlen($datatypes) !== count($values)){
          mysqli_stmt_close($stmt);
          die("Query cannot be executed - Select (invalid bind params)");
        }
        mysqli_stmt_bind_param($stmt,$datatypes,...$values);
      }
      if(mysqli_stmt_execute($stmt)){
        $res = mysqli_stmt_get_result($stmt);
        mysqli_stmt_close($stmt);
        return $res;
      }
      else{
        mysqli_stmt_close($stmt);
        die("Query cannot be executed - Select");
      }
    }
    else{
      die("Query cannot be prepared - Select");
    }
  }

  function update($sql,$values,$datatypes)
  {
    $con = $GLOBALS['con'];
    if($stmt = mysqli_prepare($con,$sql))
    {
      if(!empty($values)){
        if($datatypes === '' || strlen($datatypes) !== count($values)){
          mysqli_stmt_close($stmt);
          die("Query cannot be executed - Update (invalid bind params)");
        }
        mysqli_stmt_bind_param($stmt,$datatypes,...$values);
      }
      if(mysqli_stmt_execute($stmt)){
        $res = mysqli_stmt_affected_rows($stmt);
        mysqli_stmt_close($stmt);
        return $res;
      }
      else{
        mysqli_stmt_close($stmt);
        die("Query cannot be executed - Update");
      }
    }
    else{
      die("Query cannot be prepared - Update");
    }
  }

  function insert($sql,$values,$datatypes)
  {
    $con = $GLOBALS['con'];
    if($stmt = mysqli_prepare($con,$sql))
    {
      if(!empty($values)){
        if($datatypes === '' || strlen($datatypes) !== count($values)){
          mysqli_stmt_close($stmt);
          die("Query cannot be executed - Insert (invalid bind params)");
        }
        mysqli_stmt_bind_param($stmt,$datatypes,...$values);
      }
      if(mysqli_stmt_execute($stmt)){
        $res = mysqli_stmt_affected_rows($stmt);
        mysqli_stmt_close($stmt);
        return $res;
      }
      else{
        mysqli_stmt_close($stmt);
        die("Query cannot be executed - Insert");
      }
    }
    else{
      die("Query cannot be prepared - Insert");
    }
  }

  function delete($sql,$values,$datatypes)
  {
    $con = $GLOBALS['con'];
    if($stmt = mysqli_prepare($con,$sql))
    {
      if(!empty($values)){
        if($datatypes === '' || strlen($datatypes) !== count($values)){
          mysqli_stmt_close($stmt);
          die("Query cannot be executed - Delete (invalid bind params)");
        }
        mysqli_stmt_bind_param($stmt,$datatypes,...$values);
      }
      if(mysqli_stmt_execute($stmt)){
        $res = mysqli_stmt_affected_rows($stmt);
        mysqli_stmt_close($stmt);
        return $res;
      }
      else{
        mysqli_stmt_close($stmt);
        die("Query cannot be executed - Delete");
      }
    }
    else{
      die("Query cannot be prepared - Delete");
    }
  }

?>