<?php
session_start(); //Άνοιγμα session

require_once('includes/helper_functions.php');

$page_title = 'Καλάθι Προϊόντων';
include('includes/header.php');
require_once('mysqli_connect.php');

print "<h1>Καλάθι Προϊόντων</h1><br>";

//Άδειασμα καλαθιού
if (filter_input(INPUT_POST, 'dropCartBtn')) {

    unset($_SESSION['cart']);
    $_SESSION['total_quantity'] = 0; //μηδενισμός συνολικής ποσότητας προϊόντων που χρησιμοποιούνται στο navbar του καλαθιού
}

//Έλεγχος αν το καλάθι είναι άδειο
if (!isset($_SESSION['cart']) && empty($_SESSION['cart'])) {
?>
    <div class='row container justify-content-center'>
        <div class="card">
            <div class="card-header">Λυπούμαστε!</div>
            <div class="card-body">Το καλάθι σας είναι άδειο!</div>
            <div class="card-footer">Επισκεφθείτε τον <a href="index.php">κατάλογο προϊόντων</a> μας!</div>
        </div>

    <?php
} else {
    $t_price = 0.0;
    foreach ($_SESSION['cart'] as $key => &$val) {
        $t_price += $_SESSION["cart"][$key]['total_price'];
    }
    
    ?>
        
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Εικόνα</th>
                    <th scope="col">Προϊόν</th>
                    <th scope="col">Κωδικός</th>
                    <th scope="col">Ποσότητα</th>
                    <th scope="col">Τιμή</th>
                </tr>
            </thead>
            <tbody>
                <?php
                
                $cnt = 1;
                foreach ($_SESSION['cart'] as $key => &$val) {
                    print "<tr>";
                    print "<td>$cnt</td>";
                    
                    
                ?>
                        <td><img src='includes\photos\<?php echo $_SESSION["cart"][$key]['proimg'] ?>' width='60' height='60' alt='info icon'></a></td>
                        <td><?php echo $_SESSION["cart"][$key]['proname']; ?></td>
                        <td><?php echo $_SESSION["cart"][$key]['proid']; ?></td>
                        <td><?php echo $_SESSION["cart"][$key]['quantity']; ?></td>
                        <td><?php echo $_SESSION["cart"][$key]['total_price']; ?></td>

                <?php
                    
                  
                    $cnt = $cnt + 1;
                    print "</tr>";
                }
                ?>
            </tbody>
            <!-- <tr>
            <td colspan="3" style="background-color: lightgray;"><?php echo $t_price ?></td> 
        </tr> -->
        </table>

        <div class='row container justify-content-between'>
            <div class='col-6'>
                <form action="" method="post" name="dropCart" id="dropCart">
                    <input class="btn btn-primary btn-sm active" id="dropCartBtn" type="submit" name="dropCartBtn" value="Άδειασμα Καλαθιού">
                    <form>
            </div>
            <div class='col-6'>
                <div class="card">
                    <div class="card-body">
                    <h5 class='card-text'>  Σύνολο: <?php echo $t_price ; ?> ευρώ</h5>
                  
            </div>
                        <!-- <form action="checkout.php" method="post" name="checkout" id="checkout"> -->
                            <a href="checkout.php" class="btn btn-primary btn-sm active" id="checkoutBtn" type="submit" name="checkoutBtn" value="Checkout">Συνέχεια στην Υποβολή Παραγγελίας</a>
                        <!-- <form> -->

                    </div>
                </div>
            </div>

        </div>

    <?php
}

include('includes/footer.php');
    ?>