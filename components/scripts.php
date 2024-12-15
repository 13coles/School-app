<!-- REQUIRED SCRIPTS -->
<?php
    $base_path = ($user_role === 'student') ? './' : '../';
?>

<!-- jQuery -->
<script src="<?= $base_path ?>vendor/almasaeed2010/adminlte/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap -->
<script src="<?= $base_path ?>vendor/almasaeed2010/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE -->
<script src="<?= $base_path ?>vendor/almasaeed2010/adminlte/dist/js/adminlte.js"></script>
<!-- SweetAlert2 JS -->
<script src="<?= $base_path ?>vendor/almasaeed2010/adminlte/plugins/sweetalert2/sweetalert2.min.js"></script>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
