<?php
session_start();
$page_title = 'Login';
$errors = array();
require_once('includes/helper_functions.php');
include('includes/header.php');
print "<h1 class='h3 mb-3 font-weight-normal'>Sign in</h1><br>";
if (filter_input(INPUT_POST, 'submit')) {
    require_once('mysqli_connect.php');
    if (!$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL)) {
        $errors[] = 'Παρακαλώ δηλώστε έγκυρο email.';
    }
    if (!$pass = filter_input(INPUT_POST, 'pass', FILTER_SANITIZE_STRING)) {
        $errors[] = 'Παρακαλώ δηλώστε έγκυρο password.';
    }
    if (empty($errors)) {
        list($status, $data) = check_data($dbc, $email, $pass);
        if ($status) {
            //setcookie('user_id', $data, time()+3600, '/', '', 0, 0);
            $_SESSION['user_id'] = $data;
            $_SESSION['agent'] = md5($_SERVER['HTTP_USER_AGENT']);
            header("Location: loggedin.php");
            exit();
        } else {
            $errors = $data;
        }
    }
    mysqli_close($dbc);

 
print_error_messages($errors);
print "<br>";

}

?>

<!-- Φόρμα login -->
<div class='col-6 container'>
<form class="form-signin" action="" method="post">
      <label for="inputEmail" class="sr-only">Email</label>
      <input type="email" name="email" id="inputEmail" class="form-control" placeholder="Email address" size="20" maxlength="80" required autofocus><br>
      <label for="inputPassword" class="sr-only">Password</label>
      <input type="password" id="inputPassword" name="pass" size="20" maxlength="20" class="form-control" placeholder="Password" required><br>
      <input class="btn btn-lg btn-primary btn-block" name="submit" type="submit" value="Είσοδος">
    </form>
</div>
<?php
include('includes/footer.php');
?>