
<nav data-topbar="" class="top-bar">

  <!-- Title -->
  <ul class="title-area">
    <li class="name"><h1>
      <?php
      $page = "#";
      if($user->data()->role == "admin"){
        $page = 'registerTeacher.php';
      }
      else if($user->data()->role == "teacher"){
        $page = 'groups.php';
      }
      else if($user->data()->role == "student"){
        $page = 'dashboard.php';
      }
      echo "<a href='$page'>LSAT</a>";
      ?>
    </h1></li>
  </ul>

<section class="top-bar-section">

  <ul class="right">
    <li class="divider"></li>

    <li class="has-dropdown not-click"><a><?php echo $user->data()->username; ?></a>
      <ul class="dropdown">
        <li class="title back js-generated"><h5><a href="javascript:void(0)">Atras</a></h5></li>
        <li><a href="settings.php">Mi información</a></li>
      </ul>
    </li>

    <li class="divider"></li>

    <li class="has-form show-for-small-up">
      <a class="button" href="logout.php">Log Out</a>
    </li>
  </ul>
</section>
</div>

</nav>


<div class="ajaxWaiting"> <img src='img/loader.gif'></div>
