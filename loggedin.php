<?php
session_start();
require_once('includes/helper_functions.php');
check_session();
unset($_SESSION['cart']);
$_SESSION['total_quantity']=0;
$page_title = 'Logged In';
include('includes/header.php');
print "<h1>Logged in</h1>\n";
print "<p>Είστε συνδεδεμένος!</p>\n";
print "<p><a href='logout.php'>Logout</a></p>\n";
include('includes/footer.php');
?>
