<?php
    $user_role = $_SESSION['user_role'] ?? 'guest'; 

    # Determine the folder path based on the user's role
    $base_path = ($user_role === 'student') ? './' : '../';
?>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Student Performance Metrics Management System</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="<?= $base_path ?>vendor/almasaeed2010/adminlte/plugins/fontawesome-free/css/all.min.css">
    <!-- IonIcons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?= $base_path ?>vendor/almasaeed2010/adminlte/dist/css/adminlte.min.css">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="<?= $base_path ?>vendor/almasaeed2010/adminlte/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
    <!-- Tailwind script -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= $base_path ?>assets/css/header_style.css">
    <link rel="stylesheet" href="<?= $base_path ?>assets/css/chart_styles.css">
    <link rel="stylesheet" href="<?= $base_path ?>assets/css/sidebar_styles.css">
</head>
