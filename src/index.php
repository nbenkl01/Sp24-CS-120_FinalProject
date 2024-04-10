<?php
session_start();
// Connect to MySQL database
include 'functions.php';
$pdo = connect_mysql();

// Set default time zone.
date_default_timezone_set('America/New_York');

// Set page to home by default.
$page = isset($_GET['page']) && file_exists($_GET['page'] . '.php') ? $_GET['page'] : 'home';
// Show requested page
include $page . '.php';
?>