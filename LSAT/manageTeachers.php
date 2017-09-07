<?php

require 'core/init.php';

$user = new User();
$user->checkIsValidUser('admin');
$teachers = $user->getUsersByRole('teacher');
?>

<!doctype html>
<html class="no-js" lang="en">
<head>
  <title>LSAT | Maestros</title>
  <?php include 'includes/templates/headTags.php' ?>
</head>

<body>

  <?php include 'includes/templates/header.php' ?>

  <section class="scroll-container" role="main">

    <div class="row">
      <?php include 'includes/templates/adminSidebar.php' ?>
      <div class="large-9 medium-8 columns">
        <br/>
        <h3>Lista de todos los maestros registrados</h3>
        <hr>

        <table>
         <thead>
           <tr>
             <th width="300">Username</th>
             <th width="200">Mail</th>
             <th width="200">Nomina</th>
             <th width="200">Fecha de registro</th>
             <th width="300">Editar</th>
             <th width="300">Eliminar</th>
           </tr>
         </thead>

         <tbody>
           <?php
           foreach ($teachers as $teacher) {

             echo "<tr id='$teacher->id'>
             <td> $teacher->username </td>
             <td> $teacher->mail </td>
             <td> $teacher->idNumber </td>
             <td> $teacher->registeredDate </td>
             <td> <a href='editUser.php?uId=$teacher->id' class='tiny button secondary'>Editar</a> </td>
             <td> <a onclick='deleteUser($teacher->id)'   class='tiny button alert'>Eliminar</a> </td>
           </tr>";
         }

         ?>

       </tbody>
     </table>

   </div>
 </div>
</section>

<?php include 'includes/templates/footer.php' ?>


<script src="js/vendor/jquery.js"></script>
<script src="js/foundation.min.js"></script>
<script>
  $(document).foundation();

  function deleteUser(id){
    var r = confirm("Estas seguro que deseas eliminar este usuario?");
    if (r == true) {
      window.location.replace('./deleteTeacher.php?uId='+id);
    }
  }

</script>
</body>
</html>
