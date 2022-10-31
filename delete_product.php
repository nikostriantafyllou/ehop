<?php
session_start(); //Άνοιγμα session

require_once('includes/helper_functions.php');

$page_title = 'Διαγραφή προϊόντος';
include('includes/header.php');
require_once('mysqli_connect.php');

print "<h1>Διαγραφή Προϊόντος</h1><br>";

//Έλεγχος ότι ήρθε το id του προϊόντος από την επιλογή διαγραφή προϊόντος στην αρχική σελίδα
$id = filter_input(INPUT_GET, 'proid', FILTER_VALIDATE_INT);
if (!$id) {
    $id = filter_input(INPUT_POST, 'proid');
    if (!$id) {
        print_system_error();
    }
}


if (filter_input(INPUT_POST, 'delete')) {

    $q = "UPDATE products SET isactive=0 WHERE proid=?";
    $stmt = my_mysqli_prepare($dbc, $q);
    my_mysqli_stmt_bind_param($stmt, 'i', $id);
    my_mysqli_stmt_execute($stmt);
    if (mysqli_stmt_affected_rows($stmt) == 0) {
        print_system_error();
    } else {
?>
        <div class='row container justify-content-center'>
            <div class="card">
                <div class="card-header">Το προϊόν διαγράφτηκε επιτυχώς</div>
                <div class="card-body"></div>
                <div class="card-footer">Επιστροφή στον <a href="index.php">κατάλογο προϊόντων</a></div>
            </div>

    <?php
    }

    include('includes/footer.php');
    exit();
}

//Προετοιμασία ερωτήματος για τα προϊόντα
$q = "SELECT proid, proname, price, proimg FROM products "
    . " WHERE proid=?";

$stmt = my_mysqli_prepare($dbc, $q);
my_mysqli_stmt_bind_param($stmt, 'i', $id);
my_mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);
my_mysqli_stmt_bind_result($stmt, $proid, $proname, $price, $proimg);
mysqli_stmt_fetch($stmt);

    ?>
    <h4>Είστε σίγουροι για τη διαγραφή του κάτωθι προϊόντος;</h4><br>

    <div class="row">
        <div class='col-6'>
            <div class='card ' style='width: 18rem;'>
                <img class='card-img-top' src='includes\photos\<?php echo $proimg; ?>' alt='Εικόνα Προϊόντος'>
                <hr>
                <div class='card-body'>
                    <h5 class='card-text'><?php echo $proname; ?></h5>
                    <p>Κωδικός: <?php echo $proid; ?></p>
                    <p style='color: blue;'>Τιμή: <?php echo $price; ?> ευρώ</p>
                </div>
            </div>
        </div>
        <div class='col-6'>
            <form method="post" action="">
                <div class="btn-group-vertical" role="group">
                    <input type="submit" class="btn btn-outline-dark" name="delete" value="Διαγραφή">
                    <a href='index.php' class='btn btn-outline-dark' role='button' aria-pressed='true'>
                        Επιστροφή στον κατάλογο</a>
                    <input type="hidden" name="proid" value="<?php print $proid; ?>">
                </div>
            </form>
        </div>
    </div>

    <?php

    include('includes/footer.php>');
    ?>