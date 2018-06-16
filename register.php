
<?php
include_once 'header.php';
?>

<div id="form_wrapper" class="form_wrapper">
  <form class="register active" action="includes/signup.inc.php" method="POST">
    <h3>Register</h3>
    <div class="column">
      <div>
        <label>First Name:</label>
        <input type="text" name="first">
        <span class="error">This is an error</span>
      </div>
      <div>
        <label>Username:</label>
        <input type="text" name="user">
        <span class="error">This is an error</span>
      </div>
      <div>
        <label>Email:</label>
        <input type="text" name="email">
        <span class="error">This is an error</span>
      </div>
    </div>
    <div class="column">
      <div>
        <label>Last Name:</label>
        <input type="text" name="last">
        <span class="error">This is an error</span>
      </div>
      <div>
        <label>Password:</label>
        <input type="password" name="pwd">
        <span class="error">This is an error</span>
      </div>

    </div>
    <div class="bottom">
      <div class="remember">
        <input type="checkbox" >
        <span>Send me updates</span>
      </div>
      <button type="submit" name="submit" class="btn btn-success">Register</button>
      <a href="index.php" rel="login" class="linkform">You have an account already? Log in here</a>
      <div class="clear"></div>
    </div>
  </form>
</div>


<?php
include_once 'footer.php';
?>
