<?php
session_start(); //Άνοιγμα session

require_once('includes/helper_functions.php');
// check_session();

$page_title = 'Προσθήκη προϊόντος';
include('includes/header.php');

print "<h1>Προσθήκη προϊόντος</h1><br>";
require_once('mysqli_connect.php');

// Ενέργειες για την αποθήκευση νέου προϊόντος
if (filter_input(INPUT_POST, 'saveEdits')) {

    $errors = array();

    // Επιτρεπόμενοι τύποι jpeg ή png
    $allowed = array(
        'image/pjpeg', 'image/jpeg', 'image/jpg', 'image/JPG',
        'image/X-PNG', 'image/PNG', 'image/png', 'image/x-png'
    );
    if (in_array($_FILES['upload']['type'], $allowed)) {
        move_uploaded_file(
            $_FILES['upload']['tmp_name'],
            "includes/photos/{$_FILES['upload']['name']}");
        $img = $_FILES['upload']['name'];
    } else {
        print "<p class='error'>Παρακαλώ ανεβάσετε μία εικόνα PNG ή JPEG.</p>\n";
    }
    if ($_FILES['upload']['error'] > 0) {
        // print "<p class='error'>Το αρχείο δεν μπόρεσε να ανέβει: <br>\n";
        switch ($_FILES['photos']['error']) {
            case UPLOAD_ERR_INI_SIZE:
                $errors[] = "Η φωτογραφία υπερβαίνει το μέγιστο επιτρεπτό όριο";
                break;
            case UPLOAD_ERR_FORM_SIZE:
                $errors[] = "Η φωτογραφία υπερβαίνει το μέγιστο επιτρεπτό όριο";
                break;
            case UPLOAD_ERR_PARTIAL:
                $errors[] = "Η φωτοφραφία δεν ανέβηκε ολόκληρη";
                break;
            case UPLOAD_ERR_NO_FILE:
                $errors[] = "Δεν ανέβηκε φωτογραφία";
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $errors[] = "Σφάλμα κατά το upload";
                break;
            case UPLOAD_ERR_CANT_WRITE:
                $errors[] = "Αποτυχία εγγραφής στο δίσκο";
                break;
            case UPLOAD_ERR_EXTENSION:
                $errors[] = "Αποτυχία upload";
                break;
            default:
                $errors[] = "Σφάλμα κατά το upload";
                break;
        }
    }

    if (
        file_exists($_FILES['upload']['tmp_name']) &&
        is_file($_FILES['upload']['tmp_name'])
    ) {
        unlink($_FILES['upload']['tmp_name']);
    }


    if (!$proname = filter_input(INPUT_POST, 'proname', FILTER_SANITIZE_STRING)) {
        $errors[] = 'Το όνομα που δηλώσατε δεν είναι έγκυρο';
    }
    if (!$proid = filter_input(INPUT_POST, 'proid', FILTER_SANITIZE_NUMBER_INT)) {
        $errors[] = 'Ο κωδικός που δηλώσατε δεν είναι έγκυρος';
    }
    if ($proid <= 0) {
        $errors[] = 'Ο κωδικός που δηλώσατε δεν είναι έγκυρος';
    }
    if (!$price = filter_input(INPUT_POST, 'price', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION)) {
        $errors[] = 'Η τιμή που δώσατε δεν είναι έγκυρη';
    }
    if ($price <= 0) {
        $errors[] = 'Η τιμή που δώσατε δεν είναι έγκυρη';
    }

    
    if (empty($errors) && empty($message)) {
           //Έλεγχος αν υπάρχει ο συγκεκριμένος κωδικός
           $q = "SELECT proid FROM products WHERE proid=?";
           $stmt = my_mysqli_prepare($dbc, $q);
           my_mysqli_stmt_bind_param($stmt, 'i', $proid);
           my_mysqli_stmt_execute($stmt);
           mysqli_stmt_store_result($stmt);
           //Αν υπάρχει θα εμφανιστεί σφάλμα
           if (mysqli_stmt_num_rows($stmt) != 0) {
               $errors[] = 'O Κωδικός αυτός αντιστοιχεί σε άλλο προϊόν';
           } 
           //Αν δεν υπάρχει θα γίνει η καταχώρηση των στοιχείων για το νέο προϊόν
           else {
            $q = "INSERT INTO products (proid, proname, price, proimg, isactive) "
                    . "VALUES(?, ?, ?, ?, 1)";
            $stmt1 = my_mysqli_prepare($dbc, $q);
            my_mysqli_stmt_bind_param($stmt1, 'isds', $proid, $proname, $price, $img);
            my_mysqli_stmt_execute($stmt1);
            if (mysqli_stmt_affected_rows($stmt1) == 0) {
                $errors[] = 'Προέκυψε σφάλμα κατά την καταχώριση';
            } else {?>

                <div class='row container justify-content-center'>
                <div class="card">
                    <div class="card-header">Η καταχώριση του προϊόντος ήταν επιτυχημένη!</div>
                    <div class="card-body"></div>
                    <div class="card-footer">Επιστροφή στον <a href="index.php">κατάλογο προϊόντων</a></div>
                </div>
    
        <?php

           }
           mysqli_stmt_close($stmt1);
           
    }
    mysqli_stmt_close($stmt);
    
}

print_error_messages($errors);
if(!empty($errors)){
    print "<a href='create_product.php' class='btn btn-secondary' role='button' aria-pressed='true'>
    Επιστροφή</a>";


}
include('includes/footer.php');
    exit();
}
?>


<div class="row">
    <div class='col-6'>
        <form method="post" action="" enctype="multipart/form-data">
            <input type="hidden" name="MAX_FILE_SIZE" value="1000000">
            <b>Αρχείο εικόνας: </b><input type="file" name="upload"></p>
            <hr>
            <p><b>Όνομα Προϊόντος:</b>
                <input type="text" name="proname" id="proname" class="form-control" style="text-align:center;" value="" size="20" maxlength="80" required></p>
            <hr>
            <p><b>Κωδικός:</b>
                <input type="number" id="proid" name="proid" style="text-align:center;" value="" size="10" maxlength="20" class="form-control" required></p>
            <hr>
            <p><b>Τιμή:</b>
                <input type="number" id="price" name="price" step="0.01" style="text-align:center;" value="" size="10" maxlength="20" class="form-control" required></p>
    </div>
    <div class='col-6'>
        <div class="btn-group-vertical" role="group">
            <input type="submit" class="btn btn-outline-dark" name="saveEdits" value="Καταχώριση Προϊόντος">
            <a href='index.php' class='btn btn-outline-dark' role='button' aria-pressed='true'>
                Άκυρο</a>
        </div>
        </form>
    </div>
</div>






<?php

include('includes/footer.php>');
?>