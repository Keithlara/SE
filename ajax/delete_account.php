<?php
/**
 * Delete Account Handler
 * Securely deletes the currently logged-in user's account
 */

require_once '../admin/inc/db_config.php';
require_once '../admin/inc/essentials.php';

// Set JSON response header
header('Content-Type: application/json');

// Initialize response array
$response = [
    'success' => false,
    'message' => ''
];

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    $response['message'] = 'User not authenticated';
    echo json_encode($response);
    exit;
}

// Check if user ID exists in session
if (!isset($_SESSION['uId'])) {
    $response['message'] = 'Invalid session';
    echo json_encode($response);
    exit;
}

// Get user ID from session
$user_id = $_SESSION['uId'];

// Verify user exists before deletion
$user_check = select("SELECT `id`, `profile` FROM `user_cred` WHERE `id`=? LIMIT 1", [$user_id], 's');

if (mysqli_num_rows($user_check) == 0) {
    $response['message'] = 'User not found';
    echo json_encode($response);
    exit;
}

$user_data = mysqli_fetch_assoc($user_check);

// Begin deletion process
try {
    // Delete user's profile image if exists
    if (!empty($user_data['profile'])) {
        deleteImage($user_data['profile'], USERS_FOLDER);
    }
    
    // Delete user from database using prepared statement
    $delete_query = "DELETE FROM `user_cred` WHERE `id`=? LIMIT 1";
    $delete_result = delete($delete_query, [$user_id], 's');
    
    if ($delete_result > 0) {
        // Successfully deleted - destroy session
        session_unset();
        session_destroy();
        
        // Clear session cookie if exists
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }
        
        $response['success'] = true;
        $response['message'] = 'Account deleted successfully';
    } else {
        $response['message'] = 'Failed to delete account';
    }
} catch (Exception $e) {
    $response['message'] = 'An error occurred during deletion';
    error_log('Delete account error: ' . $e->getMessage());
}

// Return JSON response
echo json_encode($response);
exit;
?>
