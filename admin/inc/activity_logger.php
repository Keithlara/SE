<?php

function logActivity($action, $details = '') {
    $con = $GLOBALS['con'];
    $user_id = $_SESSION['adminId'] ?? 0;
    $user_name = $_SESSION['adminName'] ?? 'System';
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    
    $query = "INSERT INTO activity_logs (user_id, user_name, action, details, ip_address, user_agent) 
              VALUES (?, ?, ?, ?, ?, ?)";
    
    $values = [$user_id, $user_name, $action, $details, $ip_address, $user_agent];
    
    $stmt = mysqli_prepare($con, $query);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'isssss', ...$values);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return true;
    }
    return false;
}

// Function to get recent activities (for dashboard or activity feed)
function getRecentActivities($limit = 10) {
    $con = $GLOBALS['con'];
    $query = "SELECT * FROM activity_logs ORDER BY created_at DESC LIMIT ?";
    $stmt = mysqli_prepare($con, $query);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'i', $limit);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        mysqli_stmt_close($stmt);
        return $result;
    }
    return false;
}
