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

@mysqli_query($con, "ALTER TABLE `archived_user_cred` ADD COLUMN `username` varchar(100) DEFAULT NULL AFTER `email`");
@mysqli_query($con, "ALTER TABLE `archived_user_cred` ADD COLUMN `verification_code` varchar(255) DEFAULT NULL AFTER `is_verified`");

// Begin archive process
try {
    mysqli_begin_transaction($con);

    $live_user_res = select("SELECT * FROM `user_cred` WHERE `id`=? LIMIT 1", [$user_id], 'i');
    if (!$live_user_res || mysqli_num_rows($live_user_res) === 0) {
        throw new Exception('User not found');
    }

    $live_user = mysqli_fetch_assoc($live_user_res);

    $create = "CREATE TABLE IF NOT EXISTS `archived_user_cred` (
      `id` int(11) NOT NULL,
      `name` varchar(100) NOT NULL,
      `email` varchar(150) NOT NULL,
      `username` varchar(100) DEFAULT NULL,
      `address` varchar(120) NOT NULL,
      `phonenum` varchar(100) NOT NULL,
      `pincode` int(11) NOT NULL,
      `dob` date NOT NULL,
      `password` varchar(200) NOT NULL,
      `is_verified` int(11) NOT NULL DEFAULT 0,
      `verification_code` varchar(255) DEFAULT NULL,
      `token` varchar(200) DEFAULT NULL,
      `t_expire` date DEFAULT NULL,
      `datentime` datetime NOT NULL DEFAULT current_timestamp(),
      `status` int(11) NOT NULL DEFAULT 1,
      `profile` varchar(100) DEFAULT NULL,
      `archived_at` datetime NOT NULL DEFAULT current_timestamp(),
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

    if (!mysqli_query($con, $create)) {
        throw new Exception('Failed to prepare archive storage');
    }

    $archive_exists = select("SELECT `id` FROM `archived_user_cred` WHERE `id`=? LIMIT 1", [$user_id], 'i');
    if ($archive_exists && mysqli_num_rows($archive_exists) > 0) {
        $archive_stmt = mysqli_prepare($con,
            "UPDATE `archived_user_cred`
             SET `name`=?, `email`=?, `username`=?, `address`=?, `phonenum`=?, `pincode`=?, `dob`=?,
                 `password`=?, `is_verified`=?, `verification_code`=?, `token`=?, `t_expire`=?, `datentime`=?, `status`=?, `profile`=?, `archived_at`=NOW()
             WHERE `id`=?"
        );
        if (!$archive_stmt) {
            throw new Exception('Failed to prepare archived user update');
        }
        mysqli_stmt_bind_param($archive_stmt, 'sssssississssisi',
            $live_user['name'],
            $live_user['email'],
            $live_user['username'],
            $live_user['address'],
            $live_user['phonenum'],
            $live_user['pincode'],
            $live_user['dob'],
            $live_user['password'],
            $live_user['is_verified'],
            $live_user['verification_code'],
            $live_user['token'],
            $live_user['t_expire'],
            $live_user['datentime'],
            $live_user['status'],
            $live_user['profile'],
            $live_user['id']
        );
    } else {
        $archive_stmt = mysqli_prepare($con,
            "INSERT INTO `archived_user_cred`
             (`id`,`name`,`email`,`username`,`address`,`phonenum`,`pincode`,`dob`,`password`,`is_verified`,`verification_code`,`token`,`t_expire`,`datentime`,`status`,`profile`)
             VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)"
        );
        if (!$archive_stmt) {
            throw new Exception('Failed to prepare archived user insert');
        }
        mysqli_stmt_bind_param($archive_stmt, 'isssssississssis',
            $live_user['id'],
            $live_user['name'],
            $live_user['email'],
            $live_user['username'],
            $live_user['address'],
            $live_user['phonenum'],
            $live_user['pincode'],
            $live_user['dob'],
            $live_user['password'],
            $live_user['is_verified'],
            $live_user['verification_code'],
            $live_user['token'],
            $live_user['t_expire'],
            $live_user['datentime'],
            $live_user['status'],
            $live_user['profile']
        );
    }

    if (!mysqli_stmt_execute($archive_stmt)) {
        $error = mysqli_stmt_error($archive_stmt);
        mysqli_stmt_close($archive_stmt);
        throw new Exception('Failed to archive user: ' . $error);
    }
    mysqli_stmt_close($archive_stmt);

    archiveRefreshUserChildren($user_id);
    archiveDeleteLiveUserChildren($user_id);

    $archive_live_user = update(
        "UPDATE `user_cred` SET `is_archived`=1, `status`=0 WHERE `id`=?",
        [$user_id],
        'i'
    );

    if (!$archive_live_user) {
        throw new Exception('Failed to archive live user account');
    }

    mysqli_commit($con);

    if (!empty($user_data['profile'])) {
        // Keep the row reference and file intact for archive/restore.
    }

    if ($archive_live_user > 0) {
        session_unset();
        session_destroy();

        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }

        $response['success'] = true;
        $response['message'] = 'Account archived successfully';
    } else {
        $response['message'] = 'Failed to archive account';
    }
} catch (Exception $e) {
    mysqli_rollback($con);
    $response['message'] = 'An error occurred during deletion';
    error_log('Delete account error: ' . $e->getMessage());
}

// Return JSON response
echo json_encode($response);
exit;
?>
