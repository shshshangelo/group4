   <?php
   include 'components/connect.php';

   session_start();

   if(isset($_SESSION['id'])){
      $user_id = $_SESSION['id'];
   }else{
      $user_id = '';
   }     

   if(isset($_POST['submit'])){
      $email = $_POST['email'];
      $email = filter_var($email, FILTER_SANITIZE_STRING);
      $password = $_POST['password'];
      $password = filter_var($password, FILTER_SANITIZE_STRING);
      $confirm_password = $_POST['confirm_password'];
      $confirm_password = filter_var($confirm_password, FILTER_SANITIZE_STRING);

      if($password != $confirm_password){
         $message[] = 'Confirm password does not match.';
      }else{
         // Update the password
         $new_password = sha1($password);
         $update_password = $conn->prepare("UPDATE `users` SET password = ? WHERE email = ?");
         $update_password->execute([$new_password, $email]);

         // Delete the password reset record
         $delete_reset = $conn->prepare("DELETE FROM `password_reset` WHERE email = ?");
         $delete_reset->execute([$email]);

         // Display success message and redirect to the login page
         echo '
         <script>
            document.addEventListener("DOMContentLoaded", function() {
               swal({
                  title: "Password Reset",
                  text: "Your password has been successfully reset.",
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

   ?>

   <!DOCTYPE html>
   <html lang="en">
   <head>
      <meta charset="UTF-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Reset Password</title>

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
         margin-bottom: 1px;
      }

      .input-container input {
         padding-right: 40px;
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
            const passwordInput = document.getElementById("password-input");
            const confirmPasswordInput = document.getElementById("confirm-password-input");
            const togglePassword = document.getElementById("toggle-password");
            const toggleConfirmPassword = document.getElementById("toggle-confirm-password");

            togglePassword.addEventListener("click", function() {
               togglePasswordVisibility(passwordInput, togglePassword);
            });

            toggleConfirmPassword.addEventListener("click", function() {
               togglePasswordVisibility(confirmPasswordInput, toggleConfirmPassword);
            });

            function togglePasswordVisibility(inputField, toggleButton) {
               if (inputField.type === "password") {
                  inputField.type = "text";
                  toggleButton.classList.remove("fa-eye");
                  toggleButton.classList.add("fa-eye-slash");
               } else {
                  inputField.type = "password";
                  toggleButton.classList.remove("fa-eye-slash");
                  toggleButton.classList.add("fa-eye");
               }
            }
         });
      </script>
   </head>
   <body>
      
   <!-- header section starts  -->
   <?php include 'components/user_header.php'; ?>
   <!-- header section ends -->

   <br><br><br><br><br><br><br><br><br><br><br><br><br>

   <section class="form-container">
      <form action="" method="post">
         <h3>Reset Password</h3>
         <div class="input-container">
            <input type="email" name="email" required placeholder="Email Address" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
         </div>
         
         <div class="input-container">
            <input type="password" id="password-input" name="password" required placeholder="New Password" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
            <i id="toggle-password" class="fas fa-eye password-toggle large"></i>
         </div>
         
         <div class="input-container">
            <input type="password" id="confirm-password-input" name="confirm_password" required placeholder="Confirm New Password" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
            <i id="toggle-confirm-password" class="fas fa-eye password-toggle large"></i>
         </div>
         
         <input type="submit" value="Reset Password" name="submit" class="btn">
      </form>
   </section>

   <?php
   if(isset($message)){
      foreach($message as $message){
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
