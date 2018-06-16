<?php
  include_once 'header.php';
?>

  <div class="accountContent">
    <?php
      if (isset($_SESSION['u_id'])) {
        echo '<form action="includes/logout.inc.php" method="POST">
        <button type="submit" name="submit">LOGOUT</button>
        </form>';
      } else {
        header("Location: index.php");
        exit();
      }
    ?>
  </div>

<?php
  include_once 'footer.php';
?>
