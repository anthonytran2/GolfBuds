<?php
include_once 'header.php';
?>

<div id="carousel1" class="carousel" data-ride="carousel">
  <ol class="carousel-indicators">
    <li data-target="#carousel1" data-slide-to="0" class="active"></li>
    <li data-target="#carousel1" data-slide-to="1"></li>
    <li data-target="#carousel1" data-slide-to="2"></li>
  </ol>
  <div class="carousel-inner" role="listbox">
    <div class="item active"><img src="images/golflighter.jpg" alt="First slide image" class="center-block">
      <div class="carousel-caption">
        <h3>Join Today!</h3>
        <p>Start your day with a GolfBud!</p>
      </div>
    </div>
    <div class="item"><img src="images/IMG-Academies.jpg" alt="Second slide image" class="center-block">
      <div class="carousel-caption">
        <h3>Nearby Golf Courses</h3>
        <p>Find a Golf Course that fits you!</p>
      </div>
    </div>
    <div class="item"><img src="images/Slide_2.jpg" alt="Third slide image" class="center-block">
      <div class="carousel-caption">
        <h3>Satisfaction Guaranteed!</h3>
        <p>Once you become a GolfBud, you'll never want to stop!</p>
      </div>
    </div>
  </div>
  <a class="left carousel-control" href="#carousel1" role="button" data-slide="prev">
    <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
    <span class="sr-only">Previous</span></a><a class="right carousel-control" href="#carousel1" role="button" data-slide="next">
      <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span><span class="sr-only">Next</span></a>
</div>

    <div class="row">
      <div class="col-lg-6">
        <div class="accountContent">
          <?php
          if (isset($_SESSION['u_id'])) {
            echo '<form action="includes/logout.inc.php" method="POST">
            <button type="submit" name="submit">LOGOUT</button>
            </form>';
          } else {
            echo '
            <div class="container">
            <form class="form-signin" action="includes/login.inc.php" method="POST">
            <h3>Login</h3>

            <label>Username:</label>
            <input type="text" name="uid" placeholder="Username" class="form-control">


            <label>Password: <a href="forgotPassword.html" rel="forgot_password" class="forgot linkform">Forgot your password?</a></label>
            <input type="password" name="pwd" placeholder="Password">

            <div class="bottom">
            <div class="remember"><input type="checkbox" /><span>Keep me logged in</span></div>
            <button type="submit" class="btn btn-success" name="submit">Login</button>
            <a href="register.php" rel="register" class="linkform">You don\'t have an account yet? Register here</a>
            <div class="clear"></div>
            </div>
            </form>
            <form class="forgot_password">
            <h3>Forgot Password</h3>
            <div>
            <label>Username or Email:</label>
            <input type="text" />
            <span class="error">This is an error</span>
            </div>
            <div class="bottom">
            <input type="submit" value="Send reminder"></input>
            <a href="index.php" rel="login" class="linkform">Suddenly remebered? Log in here</a>
            <a href="register.php" rel="register" class="linkform">You don\'t have an account? Register here</a>
            <div class="clear"></div>
            </div>
            </form>
            </div>';
          }
          ?>
        </div>
      </div>
    </div>

    <div class="container-fluid">

      <div class="container-fluid">
        <!-- Brand and toggle get grouped for better mobile display -->


        <div id="leftContent" class="col-lg-6 col-md-6">
          <h3 ><i>"The object of golf is not just to win. It is to play like a gentleman and win." - Phil Mickelson</i></h3>
        </div>
        <div id="rightContent" class="col-lg-4"> <img src="images/Phil-Mickelson-Gregory-Silveira-money-laundering-case.jpg" height="200" class="img-circle" id="images" align ="right"> </div>
        <!-- /.navbar-collapse -->

        <div class="row"><a id="review" > </a>
          <div class="col-lg-6"><img src="images/steph-curry-golf-web-rd-1.jpg" alt="" width="354" height="200" class="img-circle"/></div>
          <div class="col-lg-6 col-md-7">
            <h3><i>"I'm happy with it. Obviously as a competitor, you feel like you can always play better. So hopefully I can do that tomorrow." - Stephen Curry</i></h3>
          </div>
        </div>

        <div class="row">
          <div class="col-lg-6 col-md-7">
            <h3><i>"I'm happy with it. Obviously as a competitor, you feel like you can always play better. So hopefully I can do that tomorrow." - Stephen Curry</i></h3>
          </div>
          <div class="col-lg-6" align="right"><img src="images/steph-curry-golf-web-rd-1.jpg" alt="" width="354" height="200" class="img-circle"/>
          </div>
        </div>
      </div>

      <!-- /.container-fluid -->

    </div>


    <?php
    include_once 'footer.php';
    ?>
