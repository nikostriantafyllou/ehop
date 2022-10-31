<?php
session_start(); //Άνοιγμα session

require_once('includes/helper_functions.php');

$page_title = 'Επεξεργασία προϊόντος';
include('includes/header.php');
require_once('mysqli_connect.php');

print "<h1>Επεξεργασία Προϊόντος</h1><br>";

//Έλεγχος ότι ήρθε το id του προϊόντος από την επιλογή επεξεργασία προϊόντος στην αρχική σελίδα
$id = filter_input(INPUT_GET, 'proid', FILTER_VALIDATE_INT);
if (!$id) {
    $id = filter_input(INPUT_POST, 'proid');
    if (!$id) {
        print_system_error();
    }
}

if (filter_input(INPUT_POST, 'saveEdits')) {
    
    $errors = array();
    if (!$proname = filter_input(INPUT_POST, 'proname', FILTER_SANITIZE_STRING)) {
        $errors[] = 'Το όνομα που δηλώσατε δεν είναι έγκυρο';
    }
    if (!$price = filter_input(INPUT_POST, 'price', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION)) {
        $errors[] = 'Η τιμή που δώσατε δεν είναι έγκυρη';
    }
    if ($price<=0){
        $errors[] = 'Η τιμή που δώσατε δεν είναι έγκυρη';
    }
    $noedit = "";
    $sure = filter_input(INPUT_POST, 'sure');
    $confirm = ($sure == 'yes')? 1: 0;
   
    
    if (empty($errors)) {

            $q1 = "UPDATE products SET proname=?, price=?, isactive=? WHERE proid=?";
            $stmt = my_mysqli_prepare($dbc, $q1);
            my_mysqli_stmt_bind_param($stmt, 'sdii', $proname, $price, $confirm, $id);
            my_mysqli_stmt_execute($stmt);
            if (mysqli_stmt_affected_rows($stmt) == 0) {
                $noedit = 'Δεν πραγματοποιήθηκε κάποια μεταβολή.';
            } else { ?>

                <div class='row container justify-content-center'>
                <div class="card">
                    <div class="card-header">Η επεξεργασία του προϊόντος ήταν επιτυχημένη!</div>
                    <div class="card-body"></div>
                    <div class="card-footer">Επιστροφή στον <a href="index.php">κατάλογο προϊόντων</a></div>
                </div>
    
        <?php
           
        }
        mysqli_stmt_close($stmt);

    
  }
  if($noedit){
?>
  <div class='row container justify-content-center'>
  <div class="card">
      <div class="card-header">Δεν πραγματοποιήθηκε κάποια μεταβολή</div>
      <div class="card-body"></div>
      <div class="card-footer">Επιστροφή στον <a href="index.php">κατάλογο προϊόντων</a></div>
  </div>
  
  <?php
  }
    print_error_messages($errors);
    if(!empty($errors)){
        print "<a href='edit_product.php?proid=$id' class='btn btn-secondary' role='button' aria-pressed='true'>
        Επιστροφή</a>";
  
    
}
include('includes/footer.php');
exit();
}


//Προετοιμασία ερωτήματος για τα προϊόντα
$q = "SELECT proname, price, proimg, isactive FROM products "
    . " WHERE proid=?";

$stmt = my_mysqli_prepare($dbc, $q);
my_mysqli_stmt_bind_param($stmt, 'i', $id);
my_mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);
my_mysqli_stmt_bind_result($stmt, $proname, $price, $proimg, $isactive);
mysqli_stmt_fetch($stmt);
?>

<div class="row">
    <div class='col-6'>
        <form method="post" action="">
            <div class='card ' style='width: 18rem;'>
                <img class='card-img-top' src='includes\photos\<?php echo $proimg; ?>' alt='Εικόνα Προϊόντος'>
                <hr>
                <div class='card-body'>
                    <p>Όνομα:
                        <input type="text" name="proname" id="proname" class="form-control" style="text-align:center;" value="<?php echo $proname; ?>" size="20" maxlength="80" required></p>
                    <p>Κωδικός:
                        <input type="number" id="id" name="proid" style="text-align:center;" value="<?php echo $id; ?>" size="10" maxlength="20" class="form-control" required disabled></p>
                    <p>Τιμή:
                        <input type="number" id="price" name="price" step="0.01" style="text-align:center;" value="<?php echo $price; ?>" size="10" maxlength="20" class="form-control" required></p>
                    <input type="hidden" name="proid_old" value="<?php print $id; ?>">
                    <p>Διαθεσιμότητα<br>
                        <input type="radio" name="sure" value="yes" <?php if($isactive==1) { print "checked='checked'";} ?>>Ναι
                        <input type="radio" name="sure" value="no" <?php if($isactive==0) { print "checked='checked'";} ?>>Όχι</p>
                </div>
            </div>
    </div>
    <div class='col-6'>
        <div class="btn-group-vertical" role="group">
            <input type="submit" class="btn btn-outline-dark" name="saveEdits" value="Αποθήκευση Αλλαγών">
            <a href='index.php' class='btn btn-outline-dark' role='button' aria-pressed='true'>
                Επιστροφή στον κατάλογο</a>
        </div>
        </form>
    </div>
</div>






<?php

include('includes/footer.php>');
?>