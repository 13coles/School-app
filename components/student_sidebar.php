<aside class="main-sidebar sidebar-dark-primary elevation-4 modern-sidebar">
    <!-- Sidebar -->
    <div class="sidebar">
      <div class="user-panel-container">
          <div class="user-panel-wrapper">
              <div class="user-avatar-container">
              <img src="../assets/img/images.jfif" class="user-avatar" alt="User Image">
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
          
          <!-- Home Section -->
          <li class="nav-item menu-open">
            <a href="home.php" class="nav-link active modern-nav-link">
              <i class="nav-icon fas fa-tachometer-alt"></i>  
              <p>Home</p>
            </a>  
          </li>

          <!-- My Record Section -->
          <li class="nav-item">
            <a href="my_record.php" class="nav-link modern-nav-link">
              <i class="nav-icon fas fa-address-card"></i> 
              <p>My Record</p>
            </a>
          </li>

          <!-- Report Card Section -->
          <li class="nav-item">
            <a href="report_card.php" class="nav-link modern-nav-link">
              <i class="nav-icon fas fa-file-alt"></i> 
              <p>Report Card</p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="my_performance.php" class="nav-link modern-nav-link">
                  <i class="fas fa-trophy mr-2"></i>
                  <p>Performance</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="my_attendance.php" class="nav-link modern-nav-link">
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
