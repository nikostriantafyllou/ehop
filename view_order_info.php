<?php
session_start(); //Άνοιγμα session

require_once('includes/helper_functions.php');
check_session();

$page_title = 'Λεπτομέρειες Παραγγελίας';
include('includes/header.php');
$errors = array();

//Έκχώρηση του order id που ήρθε από τη σελίδα view_orders
if(!$orderid = filter_input(INPUT_GET, 'orderid', FILTER_VALIDATE_INT)){
if (!$orderid) {
    $orderid = filter_input(INPUT_POST, 'orderid');
    if (!$orderid) {
        $error[]="Προέκυψε σφάλμα, Παρακαλώ δοκιμάστε ξανά";
    }
}
}
//Έκχώρηση της κατάστασης παραγγελίας που ήρθε από τη σελίδα view_orders
if(!$active = filter_input(INPUT_GET, 'active', FILTER_VALIDATE_INT)){
    if (!$active) {
        $active = filter_input(INPUT_POST, 'active');
        if (!$active) {
            $error[]="Προέκυψε σφάλμα, Παρακαλώ δοκιμάστε ξανά";
        }
    }
}
print "<h1>Λεπτομέρειες Παραγγελίας</h1>";
require_once('mysqli_connect.php');
$status = ($active == 1 ? "Μη Ολοκληρωμενη" : "Ολοκληρωμένη");
print " <div class='row container justify-content-between'>
        <div class='card'>
        <div class='card-header'>";
print "Κωδικός παραγγελίας: <b>$orderid</b><br>";
print "Κατάσταση: <b>$status</b><br><br>";

// Ολοκλήρωση παραγγελίας
if($active==1){
    print "<a href='view_orders.php?active=$active' class='btn btn-primary btn-sm' role='button 'aria-pressed='true'>Ολοκλήρωση Παραγγελίας</a><br><br>";
    }
print "<a href='view_orders.php?' class='btn btn-primary btn-sm' role='button 'aria-pressed='true'>Επιστροφή στις Παραγγελίες</a>";

print "</div></div></div><br>";

//Έλεγχος ότι πρέπει να ολοκληρωθεί μία παραγγελία
if ($active = filter_input(INPUT_GET, 'active', FILTER_VALIDATE_INT)){
   
    if (empty($errors) && $active==1) {

        $q1 = "UPDATE orders SET active=0 WHERE orderid=?";
        $stmt = my_mysqli_prepare($dbc, $q1);
        my_mysqli_stmt_bind_param($stmt, 'i', $orderid);
        my_mysqli_stmt_execute($stmt);
        if (mysqli_stmt_affected_rows($stmt) == 0) {
            $errors[] = 'Δεν πραγματοποιήθηκε κάποια μεταβολή.';
        }
    }
}
    
//Προετοιμασία ερωτήματος για την παραγγελία
$q = "SELECT productid, proimg, proname, oc.quantity, oc.price, date(o.order_date), o.active "
        ." FROM orders_content oc INNER JOIN products p ON p.proid=oc.productid "
        ." INNER JOIN orders o ON o.orderid=oc.orderid "
        . "WHERE o.orderid=?";
$stmt = my_mysqli_prepare($dbc, $q);
my_mysqli_stmt_bind_param($stmt, 'i', $orderid);
my_mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);
my_mysqli_stmt_bind_result($stmt, $productid, $img, $proname, $quantity, $price, $order_date, $active);


?>

<table class="table table-hover" style="text-align:center;">
  <thead class="thead-dark">
    <tr>
       <th scope="col">#</th> 
      <th scope="col">Κωδικός Προϊόντος</th>
      <th scope="col">Εικόνα</th>
      <th scope="col">Όνομα</th>
      <th scope="col">Ποσότητα</th>
      <th scope="col">Τιμή</th>
      <th scope="col">Ημερομηνία Παραγγελίας</th>
    </tr>
  </thead>
  <tbody>
<?php
$cnt = 1;
while (mysqli_stmt_fetch($stmt)) {
?>
    <tr style="text-align:center;">
       <td><?php echo $cnt++; ?></td> 
      <td><?php echo $productid ?></td>
      <td> <img src='includes\photos\<?php echo $img ?>' width='60' height='60' alt='info icon'></td>
      <td><?php echo $proname ?></td>
      <td><?php echo $quantity ?></td>
      <td><?php echo $price ?></td>
      <td><?php echo $order_date ?></td>
    </tr>
    <?php

}
print "</tbody>";
print "</table>";   

mysqli_stmt_free_result($stmt);
mysqli_stmt_close($stmt);
mysqli_close($dbc);



include('includes/footer.php');
?>