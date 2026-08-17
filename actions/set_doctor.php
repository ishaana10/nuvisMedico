<?php
/**
 * Switch Active Doctor
 */
session_start();

$doctorId = $_POST['doctor_id'] ?? 'doc-1';
$_SESSION['current_doctor_id'] = $doctorId;

$referer = $_SERVER['HTTP_REFERER'] ?? '../index.php';
header("Location: " . $referer);
exit;
