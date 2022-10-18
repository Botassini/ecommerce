<html>
  <style>
    #btn-sidebar:hover{
      background-color: #1A2226;
    }
  </style>
</html>


<header class="main-header" >

  <!-- Logo -->
  <a href="#" class="logo" style="font-weight: bold; font-size: 17px; background-color: #1A2226;">FGL ADMIN</a>
  <!-- Header Navbar: style can be found in header.less -->
  <nav class="navbar navbar-static-top" style="background-color: #222D32;">
    <!-- Sidebar toggle button-->
    <a href="#" id="btn-sidebar" class="sidebar-toggle" data-toggle="push-menu" role="button">
      <span class="sr-only">Toggle navigation</span>
    </a>

    <div class="navbar-custom-menu" >
      <ul class="nav navbar-nav" >
        <!-- User Account: style can be found in dropdown.less -->
        <li class="dropdown user user-menu" >
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" >
            <img src="<?php echo (!empty($admin['photo'])) ? '../images/'.$admin['photo'] : '../images/profile.jpg'; ?>" class="user-image" alt="User Image">
            <span class="hidden-xs"><?php echo $admin['firstname'].' '.$admin['lastname']; ?></span>
          </a>
          <ul class="dropdown-menu" style="background-color: #1A2226;">
            <!-- User image -->
            <li class="user-header" style="background-color: #1A2226;">
              <img src="<?php echo (!empty($admin['photo'])) ? '../images/'.$admin['photo'] : '../images/profile.jpg'; ?>" class="img-circle" alt="User Image">

              <p>
                <?php echo $admin['firstname'].' '.$admin['lastname']; ?>
                <small>Membro desde <?php echo date('M. Y', strtotime($admin['created_on'])); ?></small>
              </p>
            </li>
            <li class="user-footer">
              <div class="pull-left">
                <a href="#profile" data-toggle="modal" class="btn btn-default btn-flat" id="admin_profile">Atualizar</a>
              </div>
              <div class="pull-right">
                <a href="../logout.php" class="btn btn-default btn-flat">Deslogar</a>
              </div>
            </li>
          </ul>
        </li>
      </ul>
    </div>
  </nav>
</header>


<?php include 'includes/profile_modal.php'; ?>