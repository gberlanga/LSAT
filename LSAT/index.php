<?php
require 'core/init.php';

$user = new User();
if ($user->isLoggedIn()){
  $user->redirectToDefault();
}

?>

<!doctype html>
<html class="no-js" lang="en">
<head>
  <title>LSAT | Welcome</title>
  <?php include 'includes/templates/headTags.php' ?>
</head>


<body>
  
  <div class="backgroundImg blured"></div>
  <div class="row centeredXY">
      <div class="text-centered">
        <h1 class="h1-intro">LSAT</h1>
        <h2 class="h2-intro">Aprende a tu ritmo y desde cualquier lugar</h2>
        <br/><br/>

        <form id="login" data-abide="ajax" class="lsatLogin">
          <div class="mail-field">
            <input name="mail" placeholder="E-mail" type="email">
            <small class="error">An email address is required.</small>
          </div>
          <div class="password-field">
            <input name="password" placeholder="Password" type="password" pattern="mypassword">
            <!-- <small class="error">A password is required.</small> -->
          </div>
          <br/>
          <button type="submit" class="small">Login</button>
        </form>
        <a href="recoverPassword.php">Recuperar contraseña </a>
      </div>          
  </div>

  <?php include 'includes/templates/commonJs.php' ?>

  <script type="text/javascript">

    $('form#login').on('submit', function(e) {
      logIn();
      e.preventDefault();
    });

    function logIn(){

      var user = $('input[name="mail"]').val();
      var pass = $('input[name="password"]').val();

      $.post( "controls/verifyLogin.php", { username: user, password: pass})
      .done(function( data ) {


        try{ data = JSON.parse(data);}
        catch(e){ alert("There was an error, please try again."); return;}

        if(data.message == 'success'){
          window.location.replace(data.page);
          
        }else{
          alert("There password or email are incorrect, please try again.");
          showError();
        }
      });
    }

    function showError(){
      $("#loginError").fadeIn('fast');
    }

    function hideError(){
      $("#loginError").hide();
    }


  </script>
</body>
</html>
