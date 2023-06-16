<?php

include 'components/connect.php';

session_start();

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    $user_id = '';
}

if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $name = filter_var($name, FILTER_SANITIZE_STRING);
    $email = $_POST['email'];
    $email = filter_var($email, FILTER_SANITIZE_STRING);
    $pass = sha1($_POST['pass']);
    $pass = filter_var($pass, FILTER_SANITIZE_STRING);
    $cpass = sha1($_POST['cpass']);
    $cpass = filter_var($cpass, FILTER_SANITIZE_STRING);

    $select_user = $conn->prepare("SELECT * FROM `users` WHERE email = ?");
    $select_user->execute([$email]);
    $row = $select_user->fetch(PDO::FETCH_ASSOC);

    if ($select_user->rowCount() > 0) {
        $message[] = 'This email is already in use';
    } else {
        if ($pass != $cpass) {
            $message[] = 'Confirm password does not match';
        } else {
            $reset_token = bin2hex(random_bytes(16)); // Generate a random reset token
            $insert_user = $conn->prepare("INSERT INTO `users` (name, email, password, reset_token) VALUES (?, ?, ?, ?)");
            $insert_user->execute([$name, $email, $cpass, $reset_token]);
            $select_user = $conn->prepare("SELECT * FROM `users` WHERE email = ? AND password = ?");
            $select_user->execute([$email, $pass]);
            $row = $select_user->fetch(PDO::FETCH_ASSOC);
            if ($select_user->rowCount() > 0) {
                $_SESSION['user_id'] = $row['id'];
                echo '
                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        swal({
                            title: "Successfully Registered",
                            text: "You have been successfully registered.",
                            icon: "success",
                            button: "Close",
                        }).then(function() {
                            window.location.href = "login.php";
                        });
                    });
                </script>
                ';
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign up</title>

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
            margin-bottom: 5px;
        }

        .input-container input {
            padding-right: 5px;
        }

        .input-container .password-toggle {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            cursor: pointer;
        }

        .password-toggle.large {
            font-size: 20px;
        }
    </style>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const passwordToggle = document.getElementById('password-toggle');
            const confirmPasswordToggle = document.getElementById('confirm-password-toggle');
            const passwordField = document.getElementById('password');
            const confirmPasswordField = document.getElementById('confirm-password');

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

            confirmPasswordToggle.addEventListener('click', function() {
                const type = confirmPasswordField.getAttribute('type');
                if (type === 'password') {
                    confirmPasswordField.setAttribute('type', 'text');
                    confirmPasswordToggle.classList.remove('fa-eye');
                    confirmPasswordToggle.classList.add('fa-eye-slash');
                } else {
                    confirmPasswordField.setAttribute('type', 'password');
                    confirmPasswordToggle.classList.remove('fa-eye-slash');
                    confirmPasswordToggle.classList.add('fa-eye');
                }
            });
        });
    </script>

</head>
<body>
   

    <!-- header section starts  -->
    <?php include 'components/user_header.php'; ?>
    <!-- header section ends -->

    <br><br><br><br><br><br><br><br>


    <section class="form-container">
        <form action="" method="post">
            <h3>Sign up for discounts, rewards, and vouchers.</h3>
            <div class="input-container">
                <input type="text" name="name" required placeholder="Full Name" class="box" maxlength="50">
            </div>
            <div class="input-container">
                <input type="email" name="email" required placeholder="Email Address" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
            </div>
            <div class="input-container">
                <input type="password" name="pass" required placeholder="Password" class="box" id="password" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
                <i id="password-toggle" class="fas fa-eye password-toggle large"></i>
            </div>
            <div class="input-container">
                <input type="password" name="cpass" required placeholder="Confirm your Password" class="box" id="confirm-password" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
                <i id="confirm-password-toggle" class="fas fa-eye password-toggle large"></i>
            </div>
            <input type="submit" value="register now" name="submit" class="btn">
            <p>Loyal customer? <a href="login.php">Login Now</a></p>
        </form>

    </section>

    <?php
    if (isset($message)) {
        foreach ($message as $message) {
            echo '
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    swal({
                        title: "Please try again",
                        text: "'.$message.'",
                        icon: "warning",
                        button: "Close",
                    });
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
