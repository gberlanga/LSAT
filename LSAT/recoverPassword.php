<?php

require 'core/init.php';
?>

<!doctype html>
<html class="no-js" lang="en">
<head>
  <title>LSAT| Recover Password</title>
  <?php include 'includes/templates/headTags.php' ?>
</head>
<body>

  <div class="row centeredXY">
    <div class="small-9 small-centered columns ">
      <div class="panel text-centered">

         <a href="index.php"> <h1>LSAT</h1></a>
          <h4>Recuperar contraseña</h4>
          <h4>Una nueva contraseña será enviada a tu correo electrónico.</h4>
          <br/>
          <br/>
          <form id="recover" data-abide="ajax">
            <input name="mail" placeholder="e-mail" type="email">
            <small class="error">Se necesita una dirección valida de email.</small>
            <button type="submit">Obtener nueva contraseña </button>
          </form>

          <div id="wrap" style="display:none">
            <p>Listo, te mandamos la contraseña a tu email.</p>
          </div>

      </div>          
    </div>
  </div>


<?php include 'includes/templates/commonJs.php' ?>

<script>

$('input[name="mail"]').on('valid', function() {
  
});

$('form#recover').on('submit', function(e) {
            recoverPassword();
            e.preventDefault();
});

function recoverPassword(zone){
  var mymail = $('input[name="mail"]').val();
  
$.post( "controls/recoverPassword.php", { mail:mymail })
      .done(function( data ) {
    try{ 
      data = JSON.parse(data);}
        catch(e){  alert("Hubo un error."); return;}
        if(data.message == 'success'){
          $("#recover").hide();
          $("#wrap").show();
        }else{
          alert("Hubo un error: " + data.message);
        }
      
  }, "json");
}

</script>

</body>
</html>
