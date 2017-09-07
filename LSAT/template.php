<?php

require 'core/init.php';

$user = new User();
$user->checkIsValidUser('admin');

?>

<!doctype html>
<html class="no-js" lang="en">
<head>
  <title>LSAT | Template</title>
  <?php include 'includes/templates/headTags.php' ?>
</head>

<body>

  <?php include 'includes/templates/header.php' ?>

  <section class="scroll-container" role="main">

    <div class="row">
     <?php include 'includes/templates/adminSidebar.php' ?>
      <div class="large-9 medium-8 columns">
        <h3>Title</h3>
        <h4 class="subheader"> Subtitle</h4>
        <hr>  

          Content

      </div>
    </div>
  </section>

     <?php include 'includes/templates/footer.php' ?>


     <script src="js/vendor/jquery.js"></script>
     <script src="js/foundation.min.js"></script>
     <script>
      $(document).foundation();

      function X(){
        var xx  = $("#xx").val();
        
        $.post( "controls/doAction.php", { action:"xx", xx: xx })
        .done(function( data ) {
          console.log(data);
          data = JSON.parse(data);
          if(data.message == 'success'){
            alert("Success");
            window.location.reload();
          }else{
            alert("There was an error: " + data.message);
          }

        });
      }
    </script>
  </body>
  </html>
