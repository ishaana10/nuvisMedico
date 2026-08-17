<?php
/**
 * ClinicFlow Logout Action Handler
 */
session_start();
session_unset();
session_destroy();

session_start();
require_once __DIR__ . '/../config/database.php';
setToast("Logged Out", "You have been logged out securely.");

header("Location: ../login.php");
exit;
