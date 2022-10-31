<?php
session_start(); //Άνοιγμα session

require_once('includes/helper_functions.php');
check_session();

$page_title = 'Παραγγελίες';
include('includes/header.php');

print "<h1>Τρέχουσες Παραγγελίες</h1>";
require_once('mysqli_connect.php');

//Ανάλογα με την τιμή του view θα έρθουν όλες οι παραγγελίες ή μόνο οι ολοκληρωμένες ή μόνο οι ενεργές - default να έρθουν όλες
if (!$view = filter_input(INPUT_GET, 'view', FILTER_SANITIZE_NUMBER_INT)) {
    $view = '3';
}

// Αρχικοποίηση παραμέτρων για σελιδοποίηση
$display = 5;
$q = "SELECT COUNT(orderid) FROM orders WHERE 1";
//Έλεγχος αν θα μετρήσει όλες τις παραγγελίες ή μόνο τις ενεργές ή μόνο τις ολοκληρωμένες
if($view==1){
    $q .= " AND active=1 ";
} elseif($view==2){
    $q .= " AND active=0 ";
}
$stmt = my_mysqli_prepare($dbc, $q);
my_mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);
my_mysqli_stmt_bind_result($stmt, $count);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

if ($count > $display) {
    $pages = ceil($count / $display);
} else {
    $pages = 1;
}

if (!$start = filter_input(INPUT_GET, 'start', FILTER_VALIDATE_INT, array('min_range' => 0))) {
    $start = 0;
}



//Προετοιμασία ερωτήματος για τις παραγγελίες
$q = "SELECT orderid, firstname, lastname, cust_email, city, date(order_date), active FROM orders ";
//Έλεγχος αν θα φέρει όλες τις παραγγελίες ή μόνο τις ενεργές ή μόνο τις ολοκληρωμένες
if($view==1){
    $q .= " WHERE active=1 ";
} elseif($view==2){
    $q .= " WHERE active=0 ";
}
    $q .= " LIMIT $start, $display";
$stmt = my_mysqli_prepare($dbc, $q);
my_mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);
my_mysqli_stmt_bind_result($stmt, $orderid, $firstname, $lastname, $email, $city, $order_date, $active);
?>

<!-- Κουμπιά φιλτραρίσματος παραγγελιών -->
<div class='row justify-content-end'>
<div class='btn-group dropright'>
<button type='button' class='btn btn-primary dropdown-toggle btn-sm' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
Εμφάνιση παραγγελιών
</button>
<div class='dropdown-menu'>
<a href='view_orders.php?view=1' class=' dropdown-item'  aria-pressed='true'>Μη ολοκληρωμενων</a>
<a href='view_orders.php?view=2' class='dropdown-item' aria-pressed='true'>Ολοκληρωμένων</a>
<a href='view_orders.php?view=3' class='dropdown-item' aria-pressed='true'>Σύνολο</a>   
</div>
</div>
</div>
<br>
<div class='row justify-content-center'>
<table class="table table-hover ">
  <thead class="thead-dark">
    <tr>
      <th scope="col">Κωδικός Παραγγελίας</th>
      <th scope="col">Όνομα</th>
      <th scope="col">Επώνυμο</th>
      <th scope="col">Email</th>
      <th scope="col">Πόλη</th>
      <th scope="col">Ημερομηνία Παραγγελίας</th>
      <th scope="col">Κατάσταση</th>
      <th scope="col">Λεπτομέρειες</th>
    </tr>
  </thead>
  <tbody>
<?php
$cnt=1;
while (mysqli_stmt_fetch($stmt)) {
    $cnt++;
?>
    <tr> 
      <td><?php echo $orderid ?></td>
      <td><?php echo $firstname ?></td>
      <td><?php echo $lastname ?></td>
      <td><?php echo $email ?></td>
      <td><?php echo $city ?></td>
      <td><?php echo $order_date ?></td>
      <td><?php if($active==1) { echo 'Ενεργή'; } else { echo "Ολοκληρωμένη";} ?></td>
      <td><a href='view_order_info.php?orderid=<?php echo $orderid ?>&active=<?php echo $active ?>' class='btn btn-primary btn-sm' role='button' data-toggle="tooltip" data-placement="left" title="Λεπτομέρειες" aria-pressed='true'>
      <img src='includes\photos\info.svg' width='25' height='25' alt='info icon'></a></td>
    </tr>
 

<?php

}
// Αν δεν υπάρχουν αποτελέσματα για το συγκεκριμένο είδος πληροφορίας εμφάνισε το παρακάτω
if($cnt==1){
    if($view==1) { $status="μη ολοκληρωμένες"; }
    if($view==2) { $status="ολοκληρωμένες"; }
    if($view==3) { $status=""; }
    print "<td colspan=8>Δεν υπάρχουν $status παραγγελίες</td>";
}
print "</tbody>";
print "</table>"; 
print "</div>";  

mysqli_stmt_free_result($stmt);
mysqli_stmt_close($stmt);
mysqli_close($dbc);

// Η σελιδοποίηση εμφανίζεται εάν υπάρχουν άνω από 1 σελίδες και πάνω από 1 προϊόντα στην κατηγόρια που έχει επιλεχθεί
if ($pages > 1 && $cnt>1) {
    ?>
        <nav aria-label="Pages">
            <ul class="pagination">
    
                <?php
                $current = ($start / $display) + 1;
                if ($current != 1) {
                    $link = $start - $display;
                    print " <li class='page-item'>
                    <a class='page-link' href='view_orders.php?start=$link'>Προηγούμενη</a></li>";
                }
                for ($i = 1; $i <= $pages; $i++) {
                    if ($i != $current) {
                        $link = ($i - 1) * $display;
                        print " <li class='page-item'>
                        <a class='page-link' href='view_orders.php?start=$link'>$i</a></li>";
                    } else {
                        print " <li class='page-item'>
                        <a class='page-link'>$i</a></li>";
                    }
                }
    
                if ($current != $pages) {
                    $link = $start + $display;
                    print "<li class='page-item' >
                     <a class='page-link' href='view_orders.php?start=$link'>Επόμενη</a>
                     </li>";
                }
    
                ?>
    
            </ul>
        </nav>
    
    <?php
    
    }

include('includes/footer.php');
?>