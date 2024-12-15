<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
  <!-- Left navbar links -->
  <ul class="navbar-nav">
    <li class="nav-item">
      <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
    </li>
  </ul>

  <!-- Right navbar links -->
  <ul class="navbar-nav ml-auto">
    <!-- Notifications Dropdown Menu -->
    <li class="nav-item dropdown">
      <a class="nav-link" data-toggle="dropdown" href="#">
        <i class="far fa-bell"></i>
        <span class="badge badge-warning navbar-badge">15</span>
      </a>
      <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
        <span class="dropdown-item dropdown-header">15 Notifications</span>
        <div class="dropdown-divider"></div>
        <a href="#" class="dropdown-item">
          <i class="fas fa-envelope mr-2"></i> 4 new messages
          <span class="float-right text-muted text-sm">3 mins</span>
        </a>
        <div class="dropdown-divider"></div>
        <a href="#" class="dropdown-item">
          <i class="fas fa-users mr-2"></i> 8 friend requests
          <span class="float-right text-muted text-sm">12 hours</span>
        </a>
        <div class="dropdown-divider"></div>
        <a href="#" class="dropdown-item">
          <i class="fas fa-file mr-2"></i> 3 new reports
          <span class="float-right text-muted text-sm">2 days</span>
        </a>
        <div class="dropdown-divider"></div>
        <a href="#" class="dropdown-item dropdown-footer">See All Notifications</a>
      </div>
    </li>

    <!-- User Account Dropdown Menu -->
    <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
            <i class="fas fa-user"></i>
            <i class="fas fa-caret-down ml-1" style="font-size: 0.7em;"></i>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
            <div class="dropdown-header">
                <div class="d-flex">
                    <div class="flex-shrink-0">
                        <!-- Dynamically load user's profile image -->
                        <?php 
                          $image_path = ($_SESSION['user_role'] === 'student') 
                              ? './assets/images/users/' 
                              : '../assets/images/users/';
                          $default_image = 'default-user.jpg'; // Fallback image if no user-specific image
                          $user_image = $_SESSION['profile_image'] ?? $default_image;
                        ?>
                        <img src="<?= htmlspecialchars($image_path . $user_image) ?>" 
                             class="img-circle elevation-2" 
                             alt="User Image" 
                             style="width: 40px; height: 40px;">
                    </div>
                    <div class="flex-grow-1 ml-3">
                        <!-- Current user's full name -->
                        <h6 class="mb-0"><?= htmlspecialchars($_SESSION['full_name']); ?></h6>
                        <small class="text-muted"><?= ucfirst($_SESSION['user_role']); ?></small>
                    </div>
                </div>
            </div>

            <?php 
            // Dropdown menu links
            $dropdown_menu = [
                'admin' => [],
                'teacher' => [
                    'My profile' => '../teacher/teacher_profile.php',
                    'Account settings' => '../teacher/teacher_settings.php'
                ],
                'student' => [
                    'My profile' => './profile.php',
                    'Account settings' => './settings.php'
                ]
            ];

            // Current user's menu items
            $user_dropdown_menu = $dropdown_menu[$_SESSION['user_role']] ?? [];

            // Render menu items
            foreach ($user_dropdown_menu as $label => $link) {
                echo '<div class="dropdown-divider"></div>';
                echo '<a href="' . htmlspecialchars($link) . '" class="dropdown-item">';
                echo '<i class="fas fa-' . ($label === 'My profile' ? 'user' : 'cog') . ' mr-2"></i> ';
                echo ucfirst($label);
                echo '</a>';
            }
            ?>

            <div class="dropdown-divider"></div>
            <!-- Logout link dynamically based on role -->
            <?php 
            $logout_path = ($_SESSION['user_role'] === 'student') 
                ? './authentication/logout.php' 
                : '../authentication/logout.php';
            ?>
            <a href="<?= htmlspecialchars($logout_path) ?>" class="dropdown-item dropdown-footer text-danger">
                <i class="fas fa-sign-out-alt mr-2"></i> Logout
            </a>
        </div>
    </li>
  </ul>
</nav>
