<?php

if (isset($_POST['submit'])){
  include_once 'dbh.inc.php';

  $first = mysqli_real_escape_string($conn, $_POST['first']);
  $last = mysqli_real_escape_string($conn, $_POST['last']);
  $uid = mysqli_real_escape_string($conn, $_POST['user']);
  $pwd = mysqli_real_escape_string($conn, $_POST['pwd']);
  $email = mysqli_real_escape_string($conn, $_POST['email']);

  //Error handlers
  //Check for empty fields
  if (empty($first) || empty($last) || empty($uid) || empty($pwd) || empty($email)) {
    header("Location: ../register.php?signup=empty");
    exit();
  } else {
    //Check if input char are valid
    if (!preg_match("/^[a-zA-Z]*$/", $first) || !preg_match("/^[a-zA-Z]*$/", $last)){
      header("Location: ../register.php?signup=invalid");
      exit();
    } else {
      //Check if E-mail valid
      if (!filter_var($email, FILTER_VALIDATE_EMAIL)){
        header("Location: ../register.php?signup=invalid-email");
        exit();
      } else {
        $sql = "SELECT * FROM users WHERE user_uid='$uid'";
        $result = mysqli_query($conn, $sql);
        $resultCheck = mysqli_num_rows($result);

        if($resultCheck > 0){
          header("Location: ../register.php?signup=usertaken");
          exit();
        } else {
          //Hashing the password
          $hashedPwd = password_hash($pwd, PASSWORD_DEFAULT);
          //Insert user into the database
          $sql = "INSERT INTO users (user_first, user_last, user_email, user_uid, user_pwd) VALUES ('$first', '$last', '$email', '$uid', '$hashedPwd');";

          mysqli_query($conn, $sql);

          header("Location: ../register.php?signup=success");
          exit();
        }
      }
    }
  }
} else {
  header("Location: ../register.php");
  exit();
}

?>
