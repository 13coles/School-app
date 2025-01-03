$(document).ready(function() {
    let teacherTable = $('#teacherTable').DataTable({
        "responsive": true,
        "lengthChange": true,
        "autoWidth": false,
        "pageLength": 10,
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
        "language": {
            "search": "Search teachers:",
            "lengthMenu": "Show _MENU_ entries",
            "zeroRecords": "No matching teachers found",
            "info": "Showing _START_ to _END_ of _TOTAL_ teachers",
            "infoEmpty": "No teachers available",
            "infoFiltered": "(filtered from _MAX_ total teachers)"
        },
        "columnDefs": [
            { 
                "orderable": false, 
                "targets": -1 
            }
        ]
    });

    // Combine names function
    function combineNames() {
        let lastName = $('#last_name').val() ? $('#last_name').val().trim() : '';
        let firstName = $('#first_name').val() ? $('#first_name').val().trim() : '';
        let middleName = $('#middle_name').val() ? $('#middle_name').val().trim() : '';
    
        let fullName = '';
        if (lastName) fullName += lastName;
        if (firstName) fullName += (fullName ? ', ' : '') + firstName;
        if (middleName) fullName += ' ' + middleName;
    
        // Add hidden input for full name
        if ($('#full_name').length === 0) {
            $('<input>').attr({
                type: 'hidden',
                id: 'full_name',
                name: 'full_name'
            }).appendTo('#addTeacherRecord');
        }
    
        $('#full_name').val(fullName);
    }

    // Trigger name combination on input
    $('#last_name, #first_name, #middle_name').on('input', combineNames);

    // Form validation function
    function validateForm() {
        let isValid = true;
        let errorFields = [];
    
        const requiredFields = [
            'teacher_id_num', 'last_name', 'first_name', 
            'birth_date', 'sex', 'barangay', 
            'municipality', 'province', 'contact_number'
        ];
    
        requiredFields.forEach(field => {
            const $field = $(`[name="${field}"]`);
            
            if ($field.length === 0) {
                console.warn(`Field ${field} not found in the form`);
                return;
            }
    
            const fieldValue = $field.val();
            const isFieldEmpty = !fieldValue || 
                (typeof fieldValue === 'string' && fieldValue.trim() === '') ||
                (Array.isArray(fieldValue) && fieldValue.length === 0);
    
            if (isFieldEmpty) {
                $field.addClass('is-invalid');
                errorFields.push(field);
                isValid = false;
            } else {
                $field.removeClass('is-invalid');
            }
        });
    
        combineNames();
        const fullName = $('#full_name').val();
        
        if (!fullName || fullName.trim() === '') {
            $('#last_name, #first_name').addClass('is-invalid');
            errorFields.push('full name');
            isValid = false;
        }
    
        return {
            isValid: isValid,
            errorFields: errorFields
        };
    }

    // Add Teacher Record
    $('#addTeacherRecord').on('submit', function(e) {
        e.preventDefault();

        $('.is-invalid').removeClass('is-invalid');

        const validation = validateForm();

        if (!validation.isValid) {
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                html: `Please fill out the following required fields:<br>
                    <strong>${validation.errorFields.join(', ')}</strong>`,
                showConfirmButton: true
            });
            return false;
        }

        let formData = new FormData(this);

        $.ajax({
            url: 'create_teacher_record.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                console.log('Full Server Response:', response);

                if (response.status === 'success') {

                    Swal.fire({
                        icon: 'success',
                        title: 'Teacher Record Created',
                        text: `Teacher record created successfully with ID: ${response.teacher_id}`, 
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        $('#addTeacherRecord')[0].reset();
                        $('#addTeacherModal').modal('hide');

                        loadTeachers();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Failed to create teacher record',
                        showConfirmButton: true
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Full AJAX Error:', {
                    status: status,
                    error: error,
                    responseText: xhr.responseText
                });

                let errorMessage = 'An unexpected error occurred';
                
                try {
                    const errorResponse = JSON.parse(xhr.responseText);
                    errorMessage = errorResponse.message || errorMessage;
                } catch (e) {
                    errorMessage = xhr.statusText || errorMessage;
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Submission Error',
                    text: errorMessage,
                    showConfirmButton: true
                });
            }
        });
    });

    function fetchStudents(options = {}) {
        const {
            gradeSelector = '#grade',
            sectionSelector = '#section',
            studentsSelector = '#assigned_students',
            preselectedStudents = []
        } = options;
    
        const grade = $(gradeSelector).val();
        const section = $(sectionSelector).val();
        const $studentsSelect = $(studentsSelector);
    
        if (grade && section) {
            $.ajax({
                url: 'assigned_students.php',
                type: 'GET',
                data: { 
                    grade: grade, 
                    section: section 
                },
                dataType: 'json',
                success: function(response) {
                    $studentsSelect.empty();
    
                    if (response && response.status === 'success') {
                        if (response.students && response.students.length > 0) {
                            // Always enable multiple selection for both add and edit
                            $studentsSelect.attr('multiple', 'multiple');
    
                            // Populate students
                            response.students.forEach(student => {
                                const option = new Option(
                                    `${student.full_name} (LRN: ${student.lrn})`,
                                    student.id,
                                    false,
                                    preselectedStudents.includes(student.id)
                                );
                                $studentsSelect.append(option);
                            });
    
                            // Initialize Select2 with consistent styling
                            if ($.fn.select2) {
                                $studentsSelect.select2({
                                    placeholder: "Select students",
                                    allowClear: true,
                                    width: '100%',
                                    multiple: true,
                                    theme: 'bootstrap4',
                                    closeOnSelect: false,
                                    selectionCssClass: 'select2--small',
                                    dropdownCssClass: 'select2--small',
                                    templateResult: formatStudent,
                                    templateSelection: formatStudentSelection
                                });
                            }
    
                            // Show student count as toast
                            Swal.fire({
                                icon: 'info',
                                title: 'Students Found',
                                text: `${response.students.length} students available`,
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 2000
                            });
                        } else {
                            // No students case
                            $studentsSelect.append('<option value="">No students found</option>');
                        }
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Students fetch error:', error);
                    $studentsSelect.empty().append('<option value="">Error loading students</option>');
                }
            });
        } else {
            $studentsSelect.empty();
            if ($.fn.select2) {
                $studentsSelect.select2('destroy');
            }
        }
    }
    
    // Format functions for Select2
    function formatStudent(student) {
        if (!student.id) return student.text;
        return $(`<span><i class="fas fa-user mr-2"></i>${student.text}</span>`);
    }
    
    function formatStudentSelection(student) {
        if (!student.id) return student.text;
        return $(`<span><i class="fas fa-check-circle mr-2"></i>${student.text}</span>`);
    }
    
    $('#grade, #section').on('change', function() {
        fetchStudents({
            gradeSelector: '#grade',
            sectionSelector: '#section',
            studentsSelector: '#assigned_students',
            isEditModal: false
        });
    });
    
    $('#edit_grade, #edit_section').on('change', function() {
        fetchStudents({
            gradeSelector: '#edit_grade',
            sectionSelector: '#edit_section',
            studentsSelector: '#edit_assigned_students', 
            isEditModal: true
        });
    });

    // Load Teachers Function
    function loadTeachers() {
        console.log('Loading teachers...');
        $.ajax({
            url: 'fetch_teachers.php',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                console.log('Response from server:', response);
                if (response.status === 'success' && response.teachers && response.teachers.length > 0) {
                    teacherTable.clear();
            
                    response.teachers.forEach(teacher => {
                        teacherTable.row.add([
                            teacher.teacher_id_num, 
                            teacher.full_name,      
                            teacher.sex,           
                            teacher.birth_date,    
                            teacher.grade,        
                            teacher.section,    
                            teacher.contact_number, 
                            `
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="actionDropdown${teacher.id}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="actionDropdown${teacher.id}">
                                    <a class="dropdown-item view-teacher" data-id="${teacher.id}" style="cursor:default;">
                                        <i class="fas fa-eye text-info mr-2"></i> View Details
                                    </a>
                                    <a class="dropdown-item edit-teacher" data-id="${teacher.id}" data-toggle="modal" data-target="#editTeacherModal" style="cursor:default;">
                                        <i class="fas fa-edit text-success mr-2"></i>Edit
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item delete-teacher text-danger" data-id="${teacher.id}" style="cursor:default;">
                                        <i class="fas fa-archive text-danger mr-2"></i> Archive
                                    </a>
                                </div>
                            </div>
                            `
                        ]);
                    });
            
                    teacherTable.draw();
                } else {
                    console.warn('No teachers found or invalid response:', response);
                    teacherTable.clear().draw();
                    Swal.fire({
                        icon: 'info',
                        title: 'No Teachers',
                        text: response.message || 'No teacher records found'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error Details:');
                console.error('Status:', status);
                console.error('Error:', error);
                console.error('Response Text:', xhr.responseText);
                Swal.fire({
                    icon: 'error',
                    title: 'Network Error',
                    html: `
                        Could not fetch teachers. 
                        <br>Status: ${status}
                        <br>Error: ${error}
                        <br>Check console for more details.
                    `,
                    showConfirmButton: true
                });
            }
        });
    }
    
    loadTeachers();

    // View Teacher Details
    $(document).on('click', '.view-teacher', function(e) {
        e.preventDefault();
        const teacherId = $(this).data('id'); 
        console.log('Teacher ID:', teacherId);
    
        $.ajax({
            url: 'fetch_teacher_details.php', 
            type: 'GET',
            data: { id: teacherId }, 
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    const teacher = response.teacher;
    
                    // Populate the modal with teacher details
                    $('#view_teacher_id_num').text(teacher.teacher_id_num || 'N/A');
                    $('#view_full_name').text(teacher.full_name || 'N/A');
                    $('#view_birth_date').text(teacher.birth_date || 'N/A');
                    $('#view_sex').text(teacher.sex || 'N/A');
                    $('#view_religion').text(teacher.religion || 'N/A');

                    // Address Information
                    $('#view_complete_address').text([
                        teacher.street, 
                        teacher.barangay, 
                        teacher.municipality, 
                        teacher.province
                    ].filter(Boolean).join(', '));

                    $('#view_street').text(teacher.street || 'N/A');
                    $('#view_barangay').text(teacher.barangay || 'N/A');
                    $('#view_municipality').text(teacher.municipality || 'N/A');
                    $('#view_province').text(teacher.province || 'N/A');
                    $('#view_contact_number').text(teacher.contact_number || 'N/A');

                    // Assignment Information
                    $('#view_grade').text(teacher.grade || 'N/A');
                    $('#view_section').text(teacher.section || 'N/A');

                    // Format birth date with age
                    if (teacher.birth_date) {
                        const birthDate = new Date(teacher.birth_date);
                        const formattedDate = birthDate.toLocaleDateString('en-US', {
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric'
                        });

                        function calculateAge(birthDate) {
                            const today = new Date();
                            const birth = new Date(birthDate);
                            let age = today.getFullYear() - birth.getFullYear();
                            const monthDiff = today.getMonth() - birth.getMonth();

                            if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
                                age--;
                            }

                            return age;
                        }

                        const age = calculateAge(teacher.birth_date);
                        $('#view_birth_date').html(formattedDate + ` <small class="text-muted">(${age} years old)</small>`);
                    }

                    // Show the modal
                    $('#viewTeacherModal').modal('show');
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Could not retrieve teacher details'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching teacher details:', xhr.responseText); 
                Swal.fire({
                    icon: 'error',
                    title: 'Network Error',
                    text: 'Could not fetch teacher details. Please try again.'
                });
            }
        });
    });

    // Edit Teacher Details
    $(document).on('click', '.edit-teacher', function(e) {
        e.preventDefault();
        const teacherId = $(this).data('id');
        
        $.ajax({
            url: 'update_teachers.php', 
            type: 'GET',
            data: { id: teacherId },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    const teacher = response.teacher;
                    const assignedStudents = response.assigned_students || [];

                    // Parse full name
                    const nameParts = teacher.full_name.split(',').map(part => part.trim());
                    const lastNamePart = nameParts[0] || '';
                    const firstAndMiddleNames = nameParts[1] ? nameParts[1].split(' ') : [];
                    
                    const firstName = firstAndMiddleNames[0] || '';
                    const middleName = firstAndMiddleNames.slice(1).join(' ') || '';

                    // Populate edit form
                    $('#edit_teacher_id').val(teacher.id);
                    $('#edit_teacher_id_num').val(teacher.teacher_id_num);
                    
                    // Personal Details
                    $('#edit_last_name').val(lastNamePart);
                    $('#edit_first_name').val(firstName);
                    $('#edit_middle_name').val(middleName);
                    $('#edit_birth_date').val(teacher.birth_date);
                    $('#edit_sex').val(teacher.sex);
                    $('#edit_religion').val(teacher.religion || '');
                    
                    // Contact Information
                    $('#edit_contact_number').val(teacher.contact_number || '');
                    $('#edit_email').val(teacher.email || '');
                    
                    // Address Information
                    $('#edit_street').val(teacher.street || '');
                    $('#edit_barangay').val(teacher.barangay || '');
                    $('#edit_municipality').val(teacher.municipality || '');
                    $('#edit_province').val(teacher.province || '');
                    
                    // Professional Information
                    $('#edit_grade').val(teacher.grade || '');
                    $('#edit_section').val(teacher.section || '');

                    // Fetch and populate students for the selected grade and section
                    if (teacher.grade && teacher.section) {
                        fetchStudents({
                            gradeSelector: '#edit_grade',
                            sectionSelector: '#edit_section',
                            studentsSelector: '#assigned_students',
                            isEditModal: true,
                            preselectedStudents: assignedStudents.map(student => student.id)
                        });
                    }

                    $('#editTeacherModal').modal('show');
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Could not retrieve teacher details'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error Details:', {
                    status: status,
                    error: error,
                    responseText: xhr.responseText
                });

                Swal.fire({
                    icon: 'error',
                    title: 'Network Error',
                    html: `
                        Could not fetch teacher details. 
                        <br>Status: ${status}
                        <br>Error: ${error}
                        <br>Check console for more details.
                    `,
                    showConfirmButton: true
                });
            }
        });
    });

    // Update Teacher Record
    $('#editTeacherRecord').on('submit', function(e) {
        e.preventDefault();
        
        // Combine full name
        const fullName = [
            $('#edit_last_name').val().trim(),
            $('#edit_first_name').val().trim() + 
            (this.middle_name.value ? ' ' + this.middle_name.value.trim() : '')
        ].filter(Boolean).join(', ');
        
        const formData = new FormData(this);
        formData.set('full_name', fullName);
        formData.set('teacher_id', $('#edit_teacher_id').val());
        
        $.ajax({
            url: 'update_teachers.php', 
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            beforeSend: function() {
                $('#editTeacherRecord button[type="submit"]')
                    .prop('disabled', true)
                    .html('<span class="spinner-border spinner-border-sm mr-1"></span>Updating...');
            },
            success: function(response) {
                console.log('Update Response:', response);

                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        html: `
                            Teacher record updated successfully
                            <br>
                            <small>Assigned Students: ${response.assigned_students_count || 0}</small>
                        `,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        $('#editTeacherModal').modal('hide');
                        
                        setTimeout(loadTeachers, 300); 
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Could not update teacher record',
                        showConfirmButton: true
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Full Update Error:', {
                    status: status,
                    error: error,
                    responseText: xhr.responseText
                });

                let errorMessage = 'Could not update teacher record';
                let errorDetails = '';
                
                try {
                    const errorResponse = JSON.parse(xhr.responseText);
                    errorMessage = errorResponse.message || errorMessage;
                    errorDetails = errorResponse.error_details || '';
                } catch (e) {
                    errorMessage = xhr.statusText || errorMessage;
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Update Error',
                    html: `
                        ${errorMessage}
                        ${errorDetails ? `<br><small>Details: ${errorDetails}</small>` : ''}
                    `,
                    showConfirmButton: true
                });
            },
            complete: function() {
                $('#editTeacherRecord button[type="submit"]')
                    .prop('disabled', false)
                    .html('Update Teacher');
            }
        });
    });

    // Delete Teacher Record
    $(document).on('click', '.delete-teacher', function(e) {
        e.preventDefault();
        const teacherId = $(this).data('id');
        
        Swal.fire({
            title: 'Are you sure?',
            text: 'Do you want to mo this record to the archive?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Archive!'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading indicator
                Swal.fire({
                    title: 'Archiving...',
                    text: 'Please wait while we archive the record.',
                    allowOutsideClick: false,
                    onBeforeOpen: () => {
                        Swal.showLoading();
                    }
                });
    
                $.ajax({
                    url: 'archive_teacher.php',
                    type: 'POST',
                    data: { teacher_id: teacherId },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Archived',
                                text: response.message || 'Teacher record archived successfully',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                loadTeachers();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'Could not archive teacher record',
                                showConfirmButton: true
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Archive Error:', {
                            status: status,
                            error: error,
                            responseText: xhr.responseText
                        });
    
                        let errorMessage = 'Could not archive teacher record';
                        
                        try {
                            const errorResponse = JSON.parse(xhr.responseText);
                            errorMessage = errorResponse.message || errorMessage;
                        } catch (e) {
                            errorMessage = xhr.statusText || errorMessage;
                        }
    
                        Swal.fire({
                            icon: 'error',
                            title: 'Archive Error',
                            text: errorMessage,
                            showConfirmButton: true
                        });
                    }
                });
            }
        });
    });

    // Real-time form validation
    $('#addTeacherRecord, #editTeacherRecord').find('input, select').on('change input', function() {
        const $field = $(this);
        const isFieldEmpty = !$field.val() || 
            (typeof $field.val() === 'string' && $field.val().trim() === '') ||
            (Array.isArray($field.val()) && $field.val().length === 0);

        if ($field.prop('required') && isFieldEmpty) {
            $field.addClass('is-invalid');
        } else {
            $field.removeClass('is-invalid');
        }
    });
});