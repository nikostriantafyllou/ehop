<?php
session_start(); //Άνοιγμα session

require_once('includes/helper_functions.php');
// check_session();

//Έλεγχος εισερχόμενων τιμών όταν προστίθεται κάποιο προϊόν στο καλάθι
if (filter_input(INPUT_POST, 'addToCart')) {

    if (!$proname = filter_input(INPUT_POST, 'proname', FILTER_SANITIZE_STRING)) {
        $errors = "Λυπούμαστε, προέκυψε κάποιο σφάλμα! Παρακαλώ δοκιμάστε ξανά!";
    }
    if ($proid = filter_input(INPUT_POST, 'proid', FILTER_SANITIZE_STRING)) {
        $errors = "Λυπούμαστε, προέκυψε κάποιο σφάλμα! Παρακαλώ δοκιμάστε ξανά!";
    }
    if (!$price = filter_input(INPUT_POST, 'price', FILTER_SANITIZE_STRING)) {
        $errors = "Λυπούμαστε, προέκυψε κάποιο σφάλμα! Παρακαλώ δοκιμάστε ξανά!";
    }
    if (!$proimg = filter_input(INPUT_POST, 'proimg', FILTER_SANITIZE_STRING)) {
        $errors = "Λυπούμαστε, προέκυψε κάποιο σφάλμα! Παρακαλώ δοκιμάστε ξανά!";
    }
    if (!$quantity = filter_input(INPUT_POST, 'quantity', FILTER_SANITIZE_STRING)) {
        $errors = "Λυπούμαστε, προέκυψε κάποιο σφάλμα! Παρακαλώ δοκιμάστε ξανά!";
    }

    //Έλεγχος αν το έχει δημιουργηθεί το array για το καλάθι, αν όχι δημιουργείται
    if (!isset($_SESSION['cart']) && empty($_SESSION['cart'])) {
        $_SESSION['cart'] = array();
    }
    //Εύρεση συνολικής τιμής για κάθε προϊόν
    $total_price = $price * $quantity;

    //Εύρεση συνολικής ποσότητας προϊόντων
    $_SESSION['total_quantity'] += $quantity;

    /* Χρησιμοποιείται στον έλεγχο αν το προϊόν υπάρχει ήδη στο καλάθι. Ελέγχεται το καλάθι για συγκεκριμένο
 προιόν και αν υπάρχει ξανά αυξάνεται η δηλωμένη ποσότητα και τιμή, αλλιώς προστίθεται παρακάτω */
    $flag = true;
    foreach ($_SESSION['cart'] as $key => &$val) {
        if ($val["proname"] == $proname) {
            $_SESSION["cart"][$key]['quantity'] +=  $quantity;
            $_SESSION["cart"][$key]['total_price'] +=  $total_price;
            $flag = false;
        }
    }
    if ($flag) {
        $new_item = array("proimg"=> $proimg, "proname" => $proname, "proid" => $proid, "quantity" => $quantity, "total_price" => $total_price);

        array_push($_SESSION['cart'], $new_item); // Προσθήκη προϊόντων στο καλάθι
    }
}

$page_title = 'Κατάλογος Προϊόντων';
include('includes/header.php');

print "<h1>Κατάλογος Προϊόντων</h1><b r>";
require_once('mysqli_connect.php');

//Εύρεση πλήθους προιόντων με σκοπό τη σελιδοποίηση
$display = 4;
$q = "SELECT COUNT(proid) FROM products WHERE 1 ";
// Αν δεν είναι ο admin μέτρησε μόνο τα διαθέσιμα προϊόντα
if (!isset($_SESSION['user_id'])){
    $q .= " AND isactive=1 ";
}
//Αν είναι ερώτημα αναζήτησης προϊόντος μέτρησε μόνο αυτά που ταιριάζουν στην αναζήτη
if (filter_input(INPUT_POST, 'search')) {
    if (filter_input(INPUT_POST, 'searchProduct', FILTER_SANITIZE_STRING)) {
        $searchProduct = $_REQUEST['searchProduct'];
    }
    $q .= " AND proname like '%$searchProduct%' OR proid like '%$searchProduct%' ";
}
$stmt = my_mysqli_prepare($dbc, $q);
my_mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);
my_mysqli_stmt_bind_result($stmt, $count);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);




if (($count) > $display) {
    $pages = ceil(($count) / $display);
} else {
    $pages = 1;
}

if (!$start = filter_input(INPUT_GET, 'start', FILTER_VALIDATE_INT, array('min_range' => 0))) {
    $start = 0;
}
if (!$sort = filter_input(INPUT_GET, 'sort', FILTER_SANITIZE_STRING)) {
    $sort = 'id';
}
switch ($sort) {
    case 'id':
        $order_by = 'proid ASC';
        break;
    case 'nm':
        $order_by = 'proname ASC';
        break;
    case 'idr':
        $order_by = 'proid DESC';
        break;
    case 'nmr':
        $order_by = 'proname DESC';
        break;
}

//Προετοιμασία ερωτήματος για τα προϊόντα
$q = "SELECT proid, proname, price, proimg, isactive FROM products ";

// Αν είναι ο admin φέρε όλα τα προϊόντα αλλιώς φέρε μόνο τα διαθέσιμα (ενεργά)
if (isset($_SESSION['user_id'])){
    $q .= "WHERE (isactive=1 OR isactive=0) ";
}else{
    $q .= "WHERE isactive=1 ";
}

//Αν είναι ερώτημα αναζήτησης προϊόντος
if (filter_input(INPUT_POST, 'search')) {
    if (filter_input(INPUT_POST, 'searchProduct', FILTER_SANITIZE_STRING)) {
        $searchProduct = $_REQUEST['searchProduct'];
    }
    $q .= " AND proname like '%$searchProduct%' OR proid like '%$searchProduct%' ";
}
// Αν είναι κανονικό ερώτημα για εμφάνιση όλων των προϊόντων 
else {
    $q .= " ORDER BY $order_by "
        . " LIMIT $start, $display";
}
$stmt = my_mysqli_prepare($dbc, $q);
my_mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);
my_mysqli_stmt_bind_result($stmt, $proid, $proname, $price, $proimg, $isactive);

if(!$count){
    ?>
        <br><div class='row container justify-content-center'>
                    <div class="card">
                        <div class="card-header">Λυπούμαστε!</div>
                        <div class="card-body">Δε βρέθηκε κάποιο προϊόν</div>
                        <div class="card-footer">Επιστροφή στον <a href="index.php">κατάλογο προϊόντων</a></div>
                    </div>
</div>
    <?php
    }

if($count){
//  Κουμπιά ταξινόμησης και προσθήκης νέου προϊόντος
print "<div class='row justify-content-between'>";
// Έλεγχος χρήστη, στον admin εμφάνισε επιλογή για δημιουργία προϊόντος
if (isset($_SESSION['user_id'])) {
print "<a href='create_product.php' class='btn btn-primary btn-sm' role='button 'aria-pressed='true'>Προσθήκη Προϊόντος</a>";
}
//Εμφάνιση κουμπιών για ταξινόμηση
print "<div class='btn-group dropright'>";
print "<button type='button' class='btn btn-primary dropdown-toggle btn-sm' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>";
print "Ταξινόμηση";
print "</button>";
print "<div class='dropdown-menu'>";
$link = ($sort == 'id' ? 'idr' : 'id');
print "<a href='index.php?sort=$link' class=' dropdown-item'  aria-pressed='true'>κατά κωδικό</a>";
$link = ($sort == 'nm' ? 'nmr' : 'nm');
print "<a href='index.php?sort=$link' class='dropdown-item' aria-pressed='true'>κατά ονομασία</a>";    
print "</div>";
print "</div></div><br><br>";
}
// Μεταβλητή για έλεγχο αν είναι μονός ή ζυγός αριθμός αριθμός όταν έρχεται ένα προϊόν για να δημιουργεί νεά σειρά
$isOdd = true;

// Τα αποτελέσματα τοποθετούνται στη δόμη καρτών που ακολουθεί
while (mysqli_stmt_fetch($stmt)) {
    if ($isOdd) {
        print "<div class='row'>";
    }
?>
    <div class='col-6'>
        <form method="post" action="" class="addToCart">
            <div class='card ' style='width: 18rem;'>
            <?php
              // Έλεγχος χρήστη, στον admin εμφάνισε  αν είναι διαθέσιμο το προϊόν ή όχι
              if (isset($_SESSION['user_id'])) {
                  ?>
                <div class="btn-group btn-group-sm ml-auto justify-content-between" role="group">
                    <a href='edit_product.php?proid=<?php echo $proid ?>' class='btn btn-outline-success btn-sm' role='button' data-toggle="tooltip" data-placement="top" title="Επεξεργασία" aria-pressed='true'>
                        <img src='includes\photos\edit.svg' width='25' height='25' alt='trash icon'></a>
                        <?php
                            if($isactive){
                        ?>
                    <!-- Αν το προϊόν είναι ήδη διεγραμμένο μην εμφανίζεις την επιλογή διαγραφής -->
                    <a href='delete_product.php?proid=<?php echo $proid ?>' class='btn btn-outline-success btn-sm' role='button' data-toggle="tooltip" data-placement="top" title="Διαγραφή" aria-pressed='true'>
                        <img src='includes\photos\trash.svg' width='25' height='25' alt='trash icon'></a>
                        <?php
                            }
                        ?>
                </div>
              <?php } ?>
                <img class='card-img-top' src='includes\photos\<?php echo $proimg; ?>' alt='Card image cap'>
                <hr>
                <div class='card-body'>
                    <h5 class='card-text'><?php echo $proname; ?></h5>
                    <p>Κωδικός: <?php echo $proid; ?></p>
                    <p style='color: blue;'>Τιμή: <?php echo $price; ?> ευρώ</p>
                    <?php
              // Έλεγχος αν το προϊόν είναι διαθέσιμο, αν είναι εμφάνισε δυνατότητα προσθήκης στο καλάθι
              if ($isactive) {
                  ?>
                    <span class="">Ποσότητα:</span>
                    <div class="btn-group mr-2" role="group" aria-label="First group">
                        <button type="button" class="btn btn-secondary removeItem">-</button>
                        <input class="form-control quantity" type="number" name="quantity" readonly="readonly" value="0" style="width:60px; text-align:center;">
                        <button type="button" class="btn btn-secondary addItem">+</button>
                    </div>
                    <br><br>
                    <input type="text" name="proname" hidden value="<?php echo $proname; ?>">
                    <input type="text" name="proid" hidden value="<?php echo $proid; ?>">
                    <input type="text" name="price" hidden value="<?php echo $price; ?>">
                    <input type="text" name="proimg" hidden value="<?php echo $proimg; ?>">
                    <input class='btn btn-outline-success my-2 my-sm-0' id='addToCart' type='submit' name='addToCart' value='Προσθήκη στο καλάθι'>
                    <?php
              }
                    // Έλεγχος χρήστη, στον admin εμφάνισε  αν είναι διαθέσιμο το προϊόν ή όχι
                    if (isset($_SESSION['user_id'])) {
                        if($isactive){
                            print "<p style='color: green;'>Διαθέσιμο</p>";
                        } else {
                            print "<p style='color: red;'>Μη Διαθέσιμο</p>";
                        }
                    }
                    ?>
                </div>
            </div>
        </form>
    </div>

<?php
    if (!$isOdd) {
        print "</div><br>";
    }
    $isOdd = !$isOdd;
}

mysqli_stmt_free_result($stmt);
mysqli_stmt_close($stmt);
mysqli_close($dbc);

if ($pages > 1) {
?>
<div class='row justify-content-start'>
    <nav aria-label="Pages">
        <ul class="pagination">

            <?php
            $current = ($start / $display) + 1;
            if ($current != 1) {
                $link = $start - $display;
                print " <li class='page-item'>
                <a class='page-link' href='index.php?start=$link&sort=$sort'>Προηγούμενη</a></li>";
            }
            for ($i = 1; $i <= $pages; $i++) {
                if ($i != $current) {
                    $link = ($i - 1) * $display;
                    print " <li class='page-item'>
                    <a class='page-link' href='index.php?start=$link&sort=$sort'>$i</a></li>";
                } else {
                    print " <li class='page-item'>
                    <a class='page-link'>$i</a></li>";
                }
            }

            if ($current != $pages) {
                $link = $start + $display;
                print "<li class='page-item' >
                 <a class='page-link' href='index.php?start=$link&sort=$sort'>Επόμενη</a>
                 </li>";
            }

            ?>

        </ul>
    </nav>
    </div>

<?php

}
include('includes/footer.php');
?>