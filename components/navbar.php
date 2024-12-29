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
    <!-- User Account Dropdown Menu -->
    <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
            <i class="fas fa-user"></i>
            <i class="fas fa-caret-down ml-1" style="font-size: 0.7em;"></i>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
            <div class="dropdown-header">
                <div class="d-flex">
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
