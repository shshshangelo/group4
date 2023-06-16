<?php
include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
};

if(isset($_POST['submit'])){

   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);
   $pass = sha1($_POST['pass']);
   $pass = filter_var($pass, FILTER_SANITIZE_STRING);

   $select_user = $conn->prepare("SELECT * FROM `users` WHERE email = ? AND password = ?");
   $select_user->execute([$email, $pass]);
   $row = $select_user->fetch(PDO::FETCH_ASSOC);

   if($select_user->rowCount() > 0){
      $_SESSION['user_id'] = $row['id'];
      header('location:home.php');
   }else{
      $message[] = 'You`ve entered wrong email or password.';
   }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign in</title>

    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="css/style.css">

    <style>
        .form-container {
            text-align: center;
        }

        .form-container h3 {
            margin-top: 0;
            text-align: center;
        }

        .input-container {
            position: relative;
            margin-bottom: 10px;
        }

        .input-container input {
            padding-right: 20px;
        }

        .input-container .password-toggle {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            cursor: pointer;
        }

        .password-toggle.large {
            font-size: 24px;
        }
    </style>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const passwordToggle = document.getElementById('password-toggle');
            const passwordField = document.getElementById('password');

            passwordToggle.addEventListener('click', function() {
                const type = passwordField.getAttribute('type');
                if (type === 'password') {
                    passwordField.setAttribute('type', 'text');
                    passwordToggle.classList.remove('fa-eye');
                    passwordToggle.classList.add('fa-eye-slash');
                } else {
                    passwordField.setAttribute('type', 'password');
                    passwordToggle.classList.remove('fa-eye-slash');
                    passwordToggle.classList.add('fa-eye');
                }
            });
        });
    </script>

</head>
<body>
   
    <!-- header section starts  -->
    <?php include 'components/user_header.php'; ?>
    <!-- header section ends -->

    <br><br><br><br><br><br><br><br><br><br><br>


    <section class="form-container">
        <form action="" method="post">
            <h3>Sign in</h3>
            <div class="input-container">
                <input type="email" name="email" required placeholder="Email Address" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
            </div>
            <div class="input-container">
                <input type="password" name="pass" required placeholder="Password" class="box" id="password" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
                <i id="password-toggle" class="fas fa-eye password-toggle large"></i>
            </div>
            <input type="submit" value="login now" name="submit" class="btn">
            <a href="reset_password.php" class="btn">Reset Password</a>
            <p>Don't have an account? <a href="register.php">Sign up</a></p>
        </form>

    </section>

    <?php
    if (isset($message)) {
        foreach ($message as $message) {
            echo '
            <script>
                swal({
                    title: "Please try again.",
                    text: "'.$message.'",
                    icon: "error",
                    button: "Close",
                });
            </script>
            ';
        }
    }
    ?>

    <!-- custom js file link  -->
    <script src="js/script.js"></script>

</body>
</html>