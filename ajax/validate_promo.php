<?php
  if (session_status() === PHP_SESSION_NONE) { session_start(); }

  require('../admin/inc/db_config.php');
  require('../admin/inc/essentials.php');

  header('Content-Type: application/json');

  if (!(isset($_SESSION['login']) && $_SESSION['login'] == true)) {
    echo json_encode(['status' => 'error', 'message' => 'Please log in first.']);
    exit;
  }

  $code = trim((string)($_POST['code'] ?? ''));
  $subtotal = (float)($_POST['subtotal'] ?? 0);
  if ($code === '' || $subtotal <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Please enter a valid promo code.']);
    exit;
  }

  $result = validatePromoForAmount($code, $subtotal, (int)$_SESSION['uId']);
  if (!$result['ok']) {
    echo json_encode(['status' => 'error', 'message' => $result['message']]);
    exit;
  }

  echo json_encode([
    'status' => 'success',
    'message' => $result['message'],
    'promo_code' => strtoupper($code),
    'discount' => round((float)$result['discount'], 2),
    'new_total' => round((float)$result['new_total'], 2),
  ]);
