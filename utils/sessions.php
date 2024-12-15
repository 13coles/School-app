<?php


if (isset($_SESSION['error'])) {
    echo '<div class="alert alert-danger" id="error-message">' . $_SESSION['error'] . '</div>';
    unset($_SESSION['error']); 
}

if (isset($_SESSION['success'])) {
    echo '<div class="alert alert-success" id="success-message">' . $_SESSION['success'] . '</div>';
    unset($_SESSION['success']);
}

if (isset($_SESSION['info'])) {
    echo '<div class="alert alert-info" id="info-message">' . $_SESSION['info'] . '</div>';
    unset($_SESSION['info']); 
}
?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function() {
 
    $('.alert').fadeIn(500);

    setTimeout(function() {
        $('.alert').fadeOut(500);
    }, 5000); 
});
</script>
