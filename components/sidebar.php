<aside class="main-sidebar sidebar-dark-primary elevation-4 modern-sidebar">
    <!-- Sidebar -->
    <div class="sidebar">
       <div class="user-panel-container">
           <div class="user-panel-wrapper">
                <div class="user-avatar-container">
                   <img src="../vendor/almasaeed2010/adminlte/dist/img/user1-128x128.jpg" class="user-avatar" alt="User Image">
                </div>
                <div class="user-info">
                   <h4 class="user-name"><?php echo $_SESSION['full_name']; ?></h4>
                   <p class="user-role"><?php echo ucfirst($_SESSION['user_role']); ?></p>
                </div>
           </div>
        </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column nav-compact" data-widget="treeview" role="menu" data-accordion="false">
          <li class="nav-item menu-open">
            <a href="/admin/index.php" class="nav-link active modern-nav-link">
              <i class="nav-icon fas fa-home"></i>
              <p>Home</p>
            </a>  
          </li>

          <li class="nav-header">MANAGEMENT</li>

          <li class="nav-item">
            <a href="../admin/user_management.php" class="nav-link modern-nav-link">
              <i class="nav-icon fas fa-user"></i>
              <p>Users</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="../admin/students.php" class="nav-link modern-nav-link">
              <i class="nav-icon fas fa-user-graduate"></i>
              <p>Students</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link modern-nav-link">
              <i class="nav-icon fas fa-book-open"></i>
              <p>Teachers</p>
            </a>
          </li>

          <li class="nav-header">ADDITIONAL</li>

          <li class="nav-item">
            <a href="#" class="nav-link modern-nav-link">
              <i class="nav-icon fas fa-file-alt"></i>
              <p>
                Reports
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="../admin/view_grades.php" class="nav-link modern-nav-link">
                  <i class="fas fa-trophy mr-2"></i>
                  <p>Performance</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="#" class="nav-link modern-nav-link">
                  <i class="fas fa-calendar-check mr-2"></i>
                  <p>Attendance</p>
                </a>
              </li>
            </ul>
          </li>
        </ul>
      </nav>
    </div>
</aside>