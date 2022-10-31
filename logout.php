<?php
session_start();
require_once('includes/helper_functions.php');
check_session();
$_SESSION = array();
session_destroy();
setcookie('PHPSESSID', '', time()-3600, '/', '', 0, 0);
$page_title = 'Αποσύνδεση';
include('includes/header.php');
print "<h1>Logged out</h1>\n";
print "<p>Έχετε αποσυνδεθεί.</p>\n";
include('includes/footer.php');
?>

