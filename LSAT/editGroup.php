<?php
require 'core/init.php';

$user = new User();
$user->checkIsValidUser('teacher');
$teacherId = $user->data()->id;

//Id del grupo
$groupId = Input::get("g");
$g = new Groups();
$group = $g->getGroupById($groupId);
if ($group == false){
  Redirect::to("./groups.php");
}

$students = $g->getAllStudentsFromGroup($groupId);
?>

<!doctype html>
<html class="no-js" lang="en">
<head>
  <title>LSAT | Editar grupo</title>
  <?php include 'includes/templates/headTags.php' ?>
</head>

<body>

  <?php include 'includes/templates/header.php' ?>

  <section class="scroll-container" role="main">

    <div class="row">
      <?php include 'includes/templates/teacherSidebar.php' ?>
      <div class="large-9 medium-8 columns">
        <br/>
        <h3>Editar grupo</h3>
        <hr>
        <h5>Nombre del grupo</h5>
        <input id="name" type="text" value="<?php echo $group->name; ?>">
        <a onclick="updateGroup()" class="button tiny right">Guardar cambios</a>
        <br/>
        <hr>
        <h5>Alumnos inscritos</h5>
        <table>
          <thead>
            <tr>
              <th width='330'>Matrícula</th>
              <th width='330'>Nombre</th>
              <th width='330'>Eliminar (Dar de baja del grupo)</th>
            </tr>
          </thead>
          <tbody>
            <?php
            foreach($students as $student) {
              echo "
              <tr>
                <td>$student->idNumber</td>
                <td>$student->username</td>
                <td><a onclick='deleteUser($student->id)'   class='tiny button alert'>Eliminar</a></td>
              </tr>
              ";
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </section>

  <script src="js/vendor/jquery.js"></script>
  <script src="js/foundation.min.js"></script>
  <script>
    $(document).foundation();

    function updateGroup(){
      var name  = $("#name").val();

      $.post( "controls/doAction.php", { action:"updateGroup", g:<?php echo $groupId; ?>, name: name })
      .done(function( data ) {

        data = JSON.parse(data);
        if(data.message == 'success'){
          window.location.replace('./groups.php');
        }else{
          alert("There was an error: " + data.message);
        }

      });

    }

    function deleteUser(id){
      var gId = <?php
      if (isset($groupId)) {
        echo "$groupId";
      }else{
        echo "-1";
      }
      ?>;

      var r = confirm("Estas seguro que deseas eliminar este usuario?");
      if (r == true) {
        window.location.replace('./deleteStudent.php?sId='+id+'&gId='+gId);
      }
    }

  </script>
</body>
</html>
