$(document).ready(function() {
    let userTable = $('#studentTable').DataTable({
        "responsive": true,
        "lengthChange": true,
        "autoWidth": false,
        "pageLength": 10,
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
        "language": {
            "search": "Search users:",
            "lengthMenu": "Show _MENU_ entries",
            "zeroRecords": "No matching users found",
            "info": "Showing _START_ to _END_ of _TOTAL_ users",
            "infoEmpty": "No users available",
            "infoFiltered": "(filtered from _MAX_ total users)"
        },
        "columnDefs": [
            { 
                "orderable": false, 
                "targets": -1 
            }
        ]
    });

    let index = 1;

    function loadUsers() {
        $.ajax({
            url: 'fetch_users.php',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    // $('#studentTable tbody').empty();
                    userTable.clear();
    
                    response.users.forEach(user => {

                        userTable.row.add([
                            index++,
                            user.full_name,
                            user.username,
                            user.email,
                            user.contact_number || 'N/A',
                            user.user_role,
                            `
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="actionDropdown${user.id}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="actionDropdown${user.id}">
                                    <a class="dropdown-item edit-user" href="#" data-id="${user.id}">
                                        <i class="fas fa-edit text-warning mr-2"></i> Edit
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item delete-user text-danger" href="#" data-id="${user.id}">
                                        <i class="fas fa-trash text-danger mr-2"></i> Delete
                                    </a>
                                </div>
                            </div>
                            `
                        ]);
                    });

                    // Load table
                    userTable.draw();

                } else {
                    console.error('Failed to load users:', response.message);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Failed to load users'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Network Error',
                    text: 'Could not fetch users. Please try again.'
                });
            }
        });
    }
    loadUsers();
    
    // toggle password visibility
    $('.toggle-password').on('click', function() {
        const passwordField = $(this).closest('.input-group').find('input');
        const icon = $(this).find('i');
        
        if (passwordField.attr('type') === 'password') {
            passwordField.attr('type', 'text');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        } else {
            passwordField.attr('type', 'password');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        }
    });

    // creating user requests
    $('#user_role').on('change', function() {
        const role = $(this).val();
        const usernameField = $('#username');
        const usernameGroup = usernameField.closest('.form-group');

        usernameField.val('');

        switch(role) {
            case 'teacher':
                // AJAX request for getting teacher auto gnerated username
                $.ajax({
                    url: 'create_user.php', 
                    type: 'GET',
                    data: { action: 'generate_teacher_id' },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            usernameField.val(response.username);
                            usernameField.prop('disabled', true);
                            usernameGroup.show();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Could not generate teacher ID'
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Network Error',
                            text: 'Could not fetch teacher ID'
                        });
                    }
                });
                break;
            
            case 'student':
                // mannual input of LRN for student username
                usernameField.prop('disabled', false);
                usernameField.attr('placeholder', 'Enter Student LRN');
                usernameGroup.show();
                break;
            
            default:
                // manual username creation for admin role
                usernameField.prop('disabled', false);
                usernameField.attr('placeholder', 'Enter Username');
                usernameGroup.show();
        }
    });

    function validateLRN(lrn) {
        // LRN must be 12 digits long
        const lrnRegex = /^\d{12}$/;
        return lrnRegex.test(lrn);
    }

    // form submission trigger
    $('#createUserForm').on('submit', function(e) {
        e.preventDefault();
        
        const role = $('#user_role').val();
        const usernameField = $('#username');
        const username = usernameField.val().trim();
    
        // for (let pair of formData.entries()) {
        //     console.log(pair[0] + ': ' + pair[1]);
        // }

        if (role === 'student') {
            if (!validateLRN(username)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid LRN',
                    text: 'LRN must be a 12-digit number'
                });
                return;
            }
        }

        usernameField.prop('disabled', false);

        let formData = new FormData(this);
    
        // AJAX request to create user account
        $.ajax({
            url: 'create_user.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            complete: function() {
                usernameField.prop('disabled', true);
            },
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'User Created',
                    text: `User created with username: ${response.username}`,
                    showConfirmButton: true

                }).then(() => {
                    $('#createUserForm')[0].reset();
                    $('#addUserModal').modal('hide');

                    loadUsers();
                });
            },
            error: function(xhr, status, error) {
                let errorMessage = 'An unexpected error occurred';
                
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.statusText) {
                    errorMessage = xhr.statusText;
                }
                console.error('Full error:', xhr.responseText);
    
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errorMessage,
                    showConfirmButton: true
                });
            }
        });
    });

    // trigger role change to set up username field
    $('#user_role').trigger('change');

    // edit user - fetch user details
    $(document).on('click', '.edit-user', function(e) {
        e.preventDefault();
        const userId = $(this).data('id');

        // console.log('Attempting to edit user with ID:', userId); 
        $.ajax({
            url: 'update_user.php', 
            type: 'GET',
            data: { 
                id: userId 
            },
            dataType: 'json',
            success: function(response) {
                // console.log('Received response:', response); 

                if (response.status === 'success') {
                    const user = response.user;
                    
                    $('#edit_user_id').val(user.id);
                    $('#edit_full_name').val(user.full_name);
                    $('#edit_username').val(user.username);
                    $('#edit_email').val(user.email);
                    $('#edit_contact_number').val(user.contact_number || '');
                    $('#edit_user_role').val(user.user_role);

                    $('#edit_password').val('');

                    $('#editUserModal').modal('show');
                } else {
                    console.error('Failed to fetch user details:', response);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Failed to fetch user details'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', xhr.status, xhr.responseText);
                
                let errorMessage = 'Could not fetch user details. Please try again.';
                try {
                    const errorResponse = JSON.parse(xhr.responseText);
                    errorMessage = errorResponse.message || errorMessage;
                } catch(e) {
                    console.log('An error occured' + e);
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Network Error',
                    text: errorMessage
                });
            }
        });
    });

    // update user form submission
    $('#editUserForm').on('submit', function(e) {
        e.preventDefault();

        let formData = new FormData(this);

        // for (let pair of formData.entries()) {
        //     console.log(pair[0] + ': ' + pair[1]);
        // }

        $.ajax({
            url: 'update_user.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'User Updated',
                        text: 'User account updated successfully',
                        showConfirmButton: true
                    }).then(() => {
                        $('#editUserModal').modal('hide');
                        
                        loadUsers();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Update Error',
                        text: response.message || 'Failed to update user'
                    });
                }
            },
            error: function(xhr, status, error) {
                let errorMessage = 'An unexpected error occurred';
                
                try {
                    const errorResponse = JSON.parse(xhr.responseText);
                    errorMessage = errorResponse.message || errorMessage;
                } catch(e) {
                    errorMessage = xhr.statusText || errorMessage;
                }

                console.error('Full error:', xhr.responseText);

                Swal.fire({
                    icon: 'error',
                    title: 'Network Error',
                    text: errorMessage,
                    showConfirmButton: true
                });
            }
        });
    });

    // delete user functionality
    $(document).on('click', '.delete-user', function(e) {
        e.preventDefault();
        
        const userId = $(this).data('id');
        const clickedRow = $(this).closest('tr');

        Swal.fire({
            title: 'Are you sure?',
            text: 'Do you want to delete this user account?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'delete_user.php',
                    type: 'POST',
                    data: { 
                        user_id: userId 
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            userTable
                                .row(clickedRow)
                                .remove()
                                .draw();

                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: 'User account has been deleted.',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Deletion Failed',
                                text: response.message || 'Could not delete user'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        let errorMessage = 'An unexpected error occurred';
                        
                        try {
                            const errorResponse = JSON.parse(xhr.responseText);
                            errorMessage = errorResponse.message || errorMessage;
                        } catch(e) {
                            errorMessage = xhr.statusText || errorMessage;
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Deletion Error',
                            text: errorMessage
                        });
                    }
                });
            }
        });
    });
});