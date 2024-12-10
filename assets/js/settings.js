// password strength assessment logic
$('#newPassword').on('input', function() {
    const password = $(this).val();
    let strength = 0;
    let strengthText = 'Weak';
    let strengthColor = 'danger';
    let validationErrors = [];

    // checking the password length
    if (password.length >= 8) {
        strength += 20;
    } else {
        validationErrors.push("At least 8 characters long");
    }

    // checking if it's in uppercase format
    if (/[A-Z]/.test(password)) {
        strength += 20;
    } else {
        validationErrors.push("At least one uppercase letter");
    }

    // checking if it's in lowercase
    if (/[a-z]/.test(password)) {
        strength += 20;
    } else {
        validationErrors.push("At least one lowercase letter");
    }

    // check if there are numbers in the new password inputted
    if (/[0-9]/.test(password)) {
        strength += 20;
    } else {
        validationErrors.push("At least one number");
    }

    // checking for special characters
    if (/[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password)) {
        strength += 20;
    } else {
        validationErrors.push("At least one special character");
    }

    // show text and color based on the password strength
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

    // updating the strength indicator based on the current password strength and show respective color
    $('#strengthBar')
        .css('width', `${strength}%`)
        .removeClass('bg-danger bg-warning bg-success')
        .addClass(`bg-${strengthColor}`);
    $('#strengthText')
        .text(strengthText)
        .removeClass('text-danger text-warning text-success')
        .addClass(`text-${strengthColor}`);

    // update form submission to check for special characters
    $('#changePasswordForm').data('validationErrors', validationErrors);
});

// submit form logic
$('#changePasswordForm').on('submit', function(e) {
    e.preventDefault();

    const currentPassword = $('#currentPassword').val();
    const newPassword = $('#newPassword').val();
    const confirmPassword = $('#confirmPassword').val();
    const validationErrors = $(this).data('validationErrors') || [];

    // validate passwords
    if (newPassword !== confirmPassword) {
        Swal.fire({
            icon: 'error',
            title: 'Password Mismatch',
            text: 'New password and confirm password do not match.'
        });
        return;
    }

    // checking for validation errors
    if (validationErrors.length > 0) {
        Swal.fire({
            icon: 'error',
            title: 'Password Requirements Not Met',
            html: `Your password must include:<br>${validationErrors.map(error => `• ${error}`).join('<br>')}`
        });
        return;
    }

    // disable submit button and show loading
    $('#changePasswordBtn')
        .prop('disabled', true)
        .html('<span class="spinner-border spinner-border-sm mr-2"></span>Changing Password...');

    // AJAX request to change password
    $.ajax({
        url: 'change_password.php',
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
            // re-enable submit button
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