<?php
session_start(); //Άνοιγμα session

require_once('includes/helper_functions.php');

$page_title = 'Καλάθι Προϊόντων';
include('includes/header.php');
require_once('mysqli_connect.php');

print "<h1>Ckeckout</h1><br>";

// Μεταβλητές για πιθανά σφάλματα
$firstnameErr = $lastnameErr = $emailErr = $cityErr = $addressErr = "";
$add_numberErr = $postcodeErr = null;

//Έλεγχος α έχει υποβληθεί η φόρμα
if (filter_input(INPUT_POST, 'submit')) {
  
  if (!$firstname = filter_input(INPUT_POST, 'firstname', FILTER_SANITIZE_STRING)) {
    $firstnameErr = 'Μη έγκυρο όνομα';
  }
  if (!$lastname = filter_input(INPUT_POST, 'lastname', FILTER_SANITIZE_STRING)) {
    $lastnameErr = 'Μη έγκυρο επώνυμο';
  }
  if ($email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL)) {
    $email = filter_var($email, FILTER_VALIDATE_EMAIL);
  }
  if (!$email) {
    $emailErr = 'Μη έγκυρο email';
  }
  if (!$city = filter_input(INPUT_POST, 'city', FILTER_SANITIZE_STRING)) {
    $cityErr = 'Μη έγκυρο όνομα πόλης';
  }
  if (!$address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING)) {
    $addressErr = 'Μη έγκυρη διεύθυνση';
  }
  if (!$add_number = filter_input(INPUT_POST, 'number', FILTER_SANITIZE_NUMBER_INT)) {
    if($add_number<=0){
    $add_numberErr = 'Μη έγκυρος αριθμός διεύθυνσης';
    }
  }
  if (!$postcode = filter_input(INPUT_POST, 'postcode', FILTER_SANITIZE_NUMBER_INT)) {
    if($postcode<0 || strlen((string)$postcode)){
    $postcodeErr = 'Μη έγκυρος Τ.Κ.';
    }
  }
  //Αν δεν υπάρχουν σφάλματα γίνεται η καταχώριση
  if ($firstname && $lastname && $email && $city && $address && $add_number && $postcode && isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    $order_date = date("Y-m-d H:i:s");
    require_once('mysqli_connect.php');
     //Ετοιμασία query για την καταχώριση νέας παραγγελίας
    $q = "INSERT INTO orders (cust_email, firstname, lastname, city, address, add_number, postcode, order_date, active) "
      . "VALUES(?, ?, ?, ?, ?, ?, ?, ?, 1)";
    $stmt = my_mysqli_prepare($dbc, $q);
    my_mysqli_stmt_bind_param($stmt, 'sssssiis', $email, $firstname, $lastname, $city, $address, $add_number, $postcode, $order_date);
    my_mysqli_stmt_execute($stmt);
    //Εύρεση του τελευταίου id που μπήκε στον πίνακα orders
    $last_id = mysqli_insert_id($dbc);
    //Έλεγχος ότι το η καταχώριση παραγγελίας έγινε με επιτυχία
    if (mysqli_stmt_affected_rows($stmt) == 1) {
     
      //Ετοιμασία query για την εισαγωγή λεπτομερειών κάθε παραγγελίας
      $q = "INSERT INTO orders_content (orderid, productid, quantity, price) "
      . "VALUES(?, ?, ?, ?)";
      $stmt1 = my_mysqli_prepare($dbc, $q);
      //Καταχώριση κάθε προϊόντος του καλαθιού στον πίνακα με τις λεπτομέρειες της παραγγελίας
      foreach ($_SESSION['cart'] as $key => &$val) {
        $proid =  $_SESSION["cart"][$key]['proid'];
        $quantity =  $_SESSION["cart"][$key]['quantity'];
        $price =  $_SESSION["cart"][$key]['total_price'];
        my_mysqli_stmt_bind_param($stmt1, 'iiid', $last_id, $proid, $quantity, $price);
        my_mysqli_stmt_execute($stmt1);
     }
     if (mysqli_stmt_affected_rows($stmt) > 0) {
      unset($_SESSION['cart']);
      $_SESSION['total_quantity'] = 0;
      ?>
      <div class='row container justify-content-center'>
            <div class="card">
                <div class="card-header">Σας ευχαριστούμε πολύ!<br>Η παραγγελία σας καταχωρήθηκε με επιτυχία!</div>
                <div class="card-body">Σύντομα θα λάβετε email στο <b><?php echo $email;?></b> με λεπτομέρειες σχετικά με την παραγγελία σας</div>
                <div class="card-footer">Ξεχάσατε κάτι;<br>Επιστρέψτε στον <a href="index.php">κατάλογο προϊόντων</a></div>
            </div>
      <?php
    } else {
      ?>
      <div class='row container justify-content-center'>
            <div class="card">
                <div class="card-header">Κάτι πήγε στραβά!</div>
                <div class="card-body"><a href="checkout.php">Δοκιμάστε ξανά</a></div>
            </div>
      <?php
    }
    mysqli_stmt_close($stmt1);
  }
    mysqli_stmt_close($stmt);
    mysqli_close($dbc);
   
  }
  include('includes/footer.php>');
  exit();
}

?>

  <form action="" method="post">
    <div class="form-row">
      <div class="col-md-4 mb-3">
        <label for="validationCustom01">Όνομα</label>
        <input type="text" class="form-control" name="firstname" value="<?php print isset($_POST["firstname"]) ? htmlentities($_POST["firstname"]) : ''; ?>" placeholder="Όνομα" value="" required>
        <span class="error"><?php print($firstnameErr); ?></span>
      </div>
      <div class="col-md-4 mb-3">
        <label for="validationCustom02">Επώνυμο</label>
        <input type="text" class="form-control" name="lastname" value="<?php print isset($_POST["lastname"]) ? htmlentities($_POST["lastname"]) : ''; ?>" placeholder="Επώνυμο" value="" required>
        <span class="error"><?php print($lastnameErr); ?></span>
      </div>
      <div class="col-md-4 mb-3">
        <label for="validationCustomUsername">Email</label>
        <div class="input-group">
          <div class="input-group-prepend">
            <span class="input-group-text" id="inputGroupPrepend">@</span>
          </div>
          <input type="text" class="form-control" name="email" value="<?php print isset($_POST["email"]) ? htmlentities($_POST["email"]) : ''; ?>" placeholder="Email" aria-describedby="inputGroupPrepend" required>
          <span class="error"><?php print($emailErr); ?></span>
        </div>
      </div>
    </div>
    <div class="form-row">
      <div class="col-md-3 mb-3">
        <label for="validationCustom03" class="justify-content-start">City</label>
        <input type="text" class="form-control" value="<?php print isset($_POST["city"]) ? htmlentities($_POST["city"]) : ''; ?>" name="city" placeholder="City" required>
        <span class="error"><?php print($cityErr); ?></span>
      </div>
      <div class="col-md-6 mb-3">
        <label for="validationCustom04">Διεύθυνση</label>
        <input type="text" class="form-control" name="address" value="<?php print isset($_POST["address"]) ? htmlentities($_POST["address"]) : ''; ?>" placeholder="Διεύθυνση" required>
        <span class="error"><?php print($addressErr); ?></span>
      </div>
      <div class="col-md-3 mb-3">
        <label for="validationCustom05">Αριθμός</label>
        <input type="number" class="form-control" name="number" value="<?php print isset($_POST["number"]) ? htmlentities($_POST["number"]) : ''; ?>" placeholder="Αριθμός" required>
        <span class="error"><?php print($add_numberErr); ?></span>
      </div>
    </div>
    <div class="form-row">
      <div class="col-md-3 mb-3">
        <label for="validationCustom05">T.K.</label>
        <input type="number" class="form-control" name="postcode" value="<?php print isset($_POST["postcode"]) ? htmlentities($_POST["postcode"]) : ''; ?>" placeholder="T.K." required>
        <span class="error"><?php print($postcodeErr); ?></span>
      </div>
    </div>
    <input type="submit" class="btn btn-primary"  name="submit" value="Υποβολή παραγγελίας">
  </form>


<?php

include('includes/footer.php');
?>