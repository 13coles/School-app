$(document).ready(function() {
    $('#editProfileForm').on('submit', function(e) {
        e.preventDefault();
        
        // validate contact number
        const contactNumber = $('input[name="contact_number"]').val();
        const guardianContact = $('input[name="guardian_contact"]').val();
        
        // regex validation for contact numbers (10-11 digits)
        const phoneRegex = /^[0-9]{10,11}$/;
        
        // return a notification if the numbers does not match the required format (user contact)
        if (contactNumber && !phoneRegex.test(contactNumber)) {
            Swal.fire({
                icon: 'error',
                title: 'Invalid Contact Number',
                text: 'Please enter a valid 10-11 digit contact number',
                confirmButtonText: 'OK',
                confirmButtonColor: '#3085d6'
            });
            return;
        }
        
        // guardian contact
        if (guardianContact && !phoneRegex.test(guardianContact)) {
            Swal.fire({
                icon: 'error',
                title: 'Invalid Guardian Contact',
                text: 'Please enter a valid 10-11 digit contact number',
                confirmButtonText: 'OK',
                confirmButtonColor: '#3085d6'
            });
            return;
        }

        // confirm update of profile details first
        Swal.fire({
            icon: 'question',
            title: 'Update Profile',
            text: 'Do you want to complete your profile update?',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Update',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // collect form data
                var formData = $(this).serialize();

                Swal.fire({
                    title: 'Updating Profile...',
                    text: 'Please wait while we process your request',
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // AJAX request to update profile
                $.ajax({
                    url: 'profile_update.php',
                    method: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Profile Updated Successfully!',
                                html: `
                                    <div class="text-left">
                                        <p>${response.message}</p>
                                        <small>Your profile information has been updated in successfully.</small>
                                    </div>
                                `,
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#28a745'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'info',
                                title: 'No Changes Made',
                                text: response.message,
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#17a2b8'
                            });
                        }
                    },
                    error: function(xhr) {
                        var errorResponse = JSON.parse(xhr.responseText);
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Update Failed',
                            html: `
                                <div class="text-left">
                                    <p>${errorResponse.message}</p>
                                    <small>Please check your information and try again.</small>
                                </div>
                            `,
                            confirmButtonText: 'Try Again',
                            confirmButtonColor: '#dc3545'
                        });
                    }
                });
            }
        });
    });
});