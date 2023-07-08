<?php

include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
   header('location:login.php');
};

if (isset($_POST['update'])) {

   $name = $_POST['name'];
   $name = filter_var($name, FILTER_UNSAFE_RAW);
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_UNSAFE_RAW);
   $first_name = $_POST['first_name'];
   $first_name = filter_var($first_name, FILTER_UNSAFE_RAW);
   $last_name = $_POST['last_name'];
   $last_name = filter_var($last_name, FILTER_UNSAFE_RAW);

   $update_profile = $conn->prepare("UPDATE `users` SET name = ?, first_name = ?, last_name = ?, email = ? WHERE id = ?");
   $update_profile->execute([$name, $first_name, $last_name, $email, $user_id]);

   $old_image = $_POST['old_image'];
   $image = $_FILES['image']['name'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_size = $_FILES['image']['size'];
   $image_folder = 'uploaded_img/' . $image;

   if (!empty($image)) {

      if ($image_size > 2000000) {
         $message[] = 'image size is too large';
      } else {
         $update_image = $conn->prepare("UPDATE `users` SET image = ? WHERE id = ?");
         $update_image->execute([$image, $user_id]);

         if ($update_image) {
            move_uploaded_file($image_tmp_name, $image_folder);
            unlink('uploaded_img/' . $old_image);
            $message[] = 'image has been updated!';
         }
      }
   }

   $old_prcimage = $_POST['old_prcimage'];
   $prcimage = $_FILES['prcimage']['name'];
   $prcimage_tmp_name = $_FILES['prcimage']['tmp_name'];
   $prcimage_size = $_FILES['prcimage']['size'];
   $prcimage_folder = 'uploaded_img/prc_img/' . $prcimage;

   if (!empty($prcimage)) {

      if ($prcimage_size > 2000000) {
         $message[] = 'image size is too large';
      } else {
         $update_prcimage = $conn->prepare("UPDATE `users` SET prcimage = ? WHERE id = ?");
         $update_prcimage->execute([$prcimage, $user_id]);

         if ($update_prcimage) {
            move_uploaded_file($prcimage_tmp_name, $prcimage_folder);
            unlink('uploaded_img/prc_img/' . $old_prcimage);
            $message[] = 'PRC image has been updated!';
         }
      }
   }

   $old_pass = $_POST['old_pass'];
   $previous_pass = md5($_POST['previous_pass']);
   $previous_pass = filter_var($previous_pass, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
   $new_pass = md5($_POST['new_pass']);
   $new_pass = filter_var($new_pass, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
   $confirm_pass = md5($_POST['confirm_pass']);
   $confirm_pass = filter_var($confirm_pass, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

   if (!empty($previous_pass) || !empty($new_pass) || !empty($confirm_pass)) {
      if ($previous_pass != $old_pass) {
         $message[] = 'old password not matched!';
      } elseif ($new_pass != $confirm_pass) {
         $message[] = 'confirm password not matched!';
      } elseif (empty($previous_pass) | empty($new_pass) | empty($confirm_pass)) {
         $message[] = 'password not touched.';
      } else {
         $update_password = $conn->prepare("UPDATE `users` SET password = ? WHERE id = ?");
         $update_password->execute([$confirm_pass, $user_id]);
         $message[] = 'password has been updated!';
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

   <title>user profile update</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>

<body>

   <?php
   if (isset($message)) {
      foreach ($message as $message) {
         echo '
         <div class="message">
            <span>' . $message . '</span>
            <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
         </div>
         ';
      }
   }
   ?>

   <h1 class="title"> update <span>user</span> profile </h1>

   <section class="update-profile-container">

      <?php
      $select_profile = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
      $select_profile->execute([$user_id]);
      $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
      ?>

      <form action="" method="post" enctype="multipart/form-data">
         <img src="uploaded_img/<?= $fetch_profile['image']; ?>" alt="">
         <div class="flex">
            <div class="inputBox">
               <span>username: </span>
               <input type="text" name="name" required class="box" placeholder="enter your name" value="<?= $fetch_profile['name']; ?>">
               <span>First Name: </span>
               <input type="text" name="first_name" required class="box" placeholder="enter your first name" value="<?= $fetch_profile['first_name']; ?>">
               <span>Last Name: </span>
               <input type="text" name="last_name" required class="box" placeholder="enter your last name" value="<?= $fetch_profile['last_name']; ?>">
               <span>email : </span>
               <input type="email" name="email" required class="box" placeholder="enter your email" value="<?= $fetch_profile['email']; ?>">
               <span>profile pic : </span>
               <input type="hidden" name="old_image" value="<?= $fetch_profile['image']; ?>">
               <input type="file" name="image" class="box" accept="image/jpg, image/jpeg, image/png">
               <span>PRC ID : </span>
               <img src="uploaded_img/prc_img/<?= $fetch_profile['prcimage']; ?>" alt="">
               <input type="hidden" name="old_prcimage" value="<?= $fetch_profile['prcimage']; ?>">
               <input type="file" name="prcimage" class="box" accept="image/jpg, image/jpeg, image/png">

            </div>
            <div class="inputBox">
               <input type="hidden" name="old_pass" value="<?= $fetch_profile['password']; ?>">
               <span>old password :</span>
               <input type="password" class="box" name="previous_pass">
               <span>new password :</span>
               <input type="password" class="box" name="new_pass">
               <span>confirm password :</span>
               <input type="password" class="box" name="confirm_pass">
            </div>
         </div>
         <div class="flex-btn">
            <input type="submit" value="update profile" name="update" class="btn">
            <a href="user_page.php" class="option-btn">go back</a>
         </div>
      </form>

   </section>

</body>

</html>