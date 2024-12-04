// Password strength assessment
$('#newPassword').on('input', function() {
    const password = $(this).val();
    let strength = 0;
    let strengthText = 'Weak';
    let strengthColor = 'danger';
    let validationErrors = [];

    if (password.length >= 8) {
        strength += 20;
    } else {
        validationErrors.push("At least 8 characters long");
    }

    if (/[A-Z]/.test(password)) {
        strength += 20;
    } else {
        validationErrors.push("At least one uppercase letter");
    }

    if (/[a-z]/.test(password)) {
        strength += 20;
    } else {
        validationErrors.push("At least one lowercase letter");
    }

    if (/[0-9]/.test(password)) {
        strength += 20;
    } else {
        validationErrors.push("At least one number");
    }

    if (/[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password)) {
        strength += 20;
    } else {
        validationErrors.push("At least one special character");
    }

    if (strength <= 40) {
        strengthText = 'Weak';
        strengthColor = 'danger';

    } else if (strength <= 60) {
        strengthText = 'Fair';
        strengthColor = 'warning';

    } else {
        strengthText = 'Strong';
        strengthColor = 'success';
    }

    $('#strengthBar')
        .css('width', `${strength}%`)
        .removeClass('bg-danger bg-warning bg-success')
        .addClass(`bg-${strengthColor}`);
    $('#strengthText')
        .text(strengthText)
        .removeClass('text-danger text-warning text-success')
        .addClass(`text-${strengthColor}`);

    $('#changePasswordForm').data('validationErrors', validationErrors);
});

// form submission
$('#changePasswordForm').on('submit', function(e) {
    e.preventDefault();

    const currentPassword = $('#currentPassword').val();
    const newPassword = $('#newPassword').val();
    const confirmPassword = $('#confirmPassword').val();
    const validationErrors = $(this).data('validationErrors') || [];

    if (newPassword !== confirmPassword) {
        Swal.fire({
            icon: 'error',
            title: 'Password Mismatch',
            text: 'New password and confirm password do not match.'
        });
        return;
    }

    if (validationErrors.length > 0) {
        Swal.fire({
            icon: 'error',
            title: 'Password Requirements Not Met',
            html: `Your password must include:<br>${validationErrors.map(error => `• ${error}`).join('<br>')}`
        });
        return;
    }

    $('#changePasswordBtn')
        .prop('disabled', true)
        .html('<span class="spinner-border spinner-border-sm mr-2"></span>Changing Password...');

    // AJAX request to change password for the teacher
    $.ajax({
        url: 'teacher_change_pass.php',
        method: 'POST',
        data: {
            currentPassword: currentPassword,
            newPassword: newPassword
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Password Changed',
                    text: 'Your password has been successfully updated.'
                }).then(() => {
                    $('#changePasswordForm')[0].reset();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message || 'Failed to change password.'
                });
            }
        },
        error: function(xhr) {
            console.error('Full error details:', xhr);
            
            try {
                const errorResponse = xhr.responseJSON;
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errorResponse.message || 'An unexpected error occurred',
                    confirmButtonText: 'OK'
                });
            } catch (parseError) {
                Swal.fire({
                    icon: 'error',
                    title: 'Network Error',
                    text: 'Unable to connect to the server. Please check your connection and try again.',
                    confirmButtonText: 'OK'
                });
            }
        },
        complete: function() {
            $('#changePasswordBtn')
                .prop('disabled', false)
                .html('<i class="fas fa-save mr-2"></i>Change Password');
        }
    });
});

// toggle password visibility
$('.toggle-password').click(function() {
    const targetId = $(this).data('target');
    const input = $(`#${targetId}`);
    const icon = $(this).find('i');
    
    if (input.attr('type') === 'password') {
        input.attr('type', 'text');
        icon.removeClass('fa-eye').addClass('fa-eye-slash');
    } else {
        input.attr('type', 'password');
        icon.removeClass('fa-eye-slash').addClass('fa-eye');
    }
});