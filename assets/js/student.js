$(document).ready(function() {
    let studentTable = $('#studentTable').DataTable({
        "responsive": true,
        "lengthChange": true,
        "autoWidth": false,
        "pageLength": 10,
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
        "language": {
            "search": "Search students:",
            "lengthMenu": "Show _MENU_ entries",
            "zeroRecords": "No matching students found",
            "info": "Showing _START_ to _END_ of _TOTAL_ students",
            "infoEmpty": "No students available",
            "infoFiltered": "(filtered from _MAX_ total students)"
        },
        "columnDefs": [
            { 
                "orderable": false, 
                "targets": -1 
            }
        ]
    });

    // same2 lng sa iban
    function combineNames() {
        let lastName = $('#last_name').val() ? $('#last_name').val().trim() : '';
        let firstName = $('#first_name').val() ? $('#first_name').val().trim() : '';
        let middleName = $('#middle_name').val() ? $('#middle_name').val().trim() : '';
    
        let fullName = '';
        if (lastName) fullName += lastName;
        if (firstName) fullName += (fullName ? ', ' : '') + firstName;
        if (middleName) fullName += ' ' + middleName;
    
        // add a hidden input that will take the value of the combined name as full name then send it to the backend
        if ($('#full_name').length === 0) {
            $('<input>').attr({
                type: 'hidden',
                id: 'full_name',
                name: 'full_name'
            }).appendTo('#addStudentRecord');
        }
    
        $('#full_name').val(fullName);
    }

    $('#last_name, #first_name, #middle_name').on('input', combineNames);

    function validateForm() {
        let isValid = true;
        let errorFields = [];
    
        const requiredFields = [
            'lrn', 'last_name', 'first_name', 
            'birth_date', 'sex', 'barangay', 
            'municipality', 'province', 'grade', 
            'section', 'learning_modality'
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

    $('#addStudentRecord').on('submit', function(e) {
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
            url: 'create_student_record.php',
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
                        title: 'Student Record Created',
                        text: `Student record created successfully with LRN: ${response.lrn}`, 
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        $('#addStudentRecord')[0].reset();
                        $('#addUserModal').modal('hide');
    
                        loadStudents();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Failed to create student record',
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

    // real-time validation as user types
    $('#addStudentRecord').find('input, select').on('change input', function() {
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

    function loadStudents() {
        $.ajax({
            url: 'fetch_students.php',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                console.log('Fetch Students Response:', response); 
    
                if (response.status === 'success' && response.students && response.students.length > 0) {
                    studentTable.clear();
        
                    response.students.forEach(student => {

                        studentTable.row.add([
                            student.lrn,
                            student.full_name,
                            student.sex,
                            student.grade,
                            student.section,
                            student.barangay,
                            student.municipality,
                            student.province,
                            student.contact_number,
                            `
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="actionDropdown${student.id}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="actionDropdown${student.id}">
                                    <a class="dropdown-item view-student" data-id="${student.id}" style="cursor:default;">
                                        <i class="fas fa-eye text-primary mr-2"></i> View Details
                                    </a>
                                     <a class="dropdown-item view-grades" href="../admin/view_grades.php?student_id=${student.id}" style="cursor:default;">
                                        <i class="fas fa-book-open text-warning mr-2"></i> View Card
                                    </a>
                                    <a class="dropdown-item view-grades" href="../admin/add_grade.php?student_id=${student.id}" style="cursor:default;">
                                        <i class="fas fa-pencil-alt text-info mr-2"></i> Add Grade
                                    </a>
                                    <a class="dropdown-item edit-student" data-id="${student.id}" data-toggle="modal" data-target="#editStudentModal" style="cursor:default;">
                                        <i class="fas fa-edit text-success mr-2"></i>Edit
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item delete-student text-danger" data-id="${student.id}" style="cursor:default;">
                                        <i class="fas fa-archive text-danger mr-2"></i> Archive
                                    </a>
                                </div>
                            </div>
                            `,
                            student.id
                        ]);

                    });
    
                    studentTable.draw();
    
                    console.log(`Loaded ${response.students.length} students`); 
                } else {
                    console.warn('No students found or invalid response:', response);
                    
                    studentTable.clear().draw();
                    
                    Swal.fire({
                        icon: 'info',
                        title: 'No Students',
                        text: response.message || 'No student records found'
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
                        Could not fetch students. 
                        <br>Status: ${status}
                        <br>Error: ${error}
                        <br>Check console for more details.
                    `,
                    showConfirmButton: true
                });
            }
        });
    }

    loadStudents();

    $(document).on('click', '.view-student', function(e) {
        e.preventDefault();
        const studentId = $(this).data('id');
        
        $.ajax({
            url: 'fetch_student_details.php', 
            type: 'GET',
            data: { id: studentId },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    const student = response.student;

                    // personal information
                    $('#view_lrn').text(student.lrn || 'N/A');
                    $('#view_full_name').text(student.full_name || 'N/A');
                    $('#view_birth_date').text(student.birth_date || 'N/A');
                    $('#view_sex').text(student.sex || 'N/A');
                    $('#view_religion').text(student.religion || 'N/A');
                    
                   // sections with consistent approach
                    $('#view_complete_address').text([
                        student.street, 
                        student.barangay, 
                        student.municipality, 
                        student.province, 
                    ].filter(Boolean).join(', '));

                    $('#view_street').text(student.street || 'N/A');
                    $('#view_barangay').text(student.barangay || 'N/A');
                    $('#view_municipality').text(student.municipality || 'N/A');
                    $('#view_province').text(student.province || 'N/A');
                    $('#view_contact_number').text(student.contact_number || 'N/A');

                    // parent/guardian details
                    $('#view_father_name').text(student.father_name || 'N/A');
                    $('#view_mother_name').text(student.mother_name || 'N/A');
                    $('#view_guardian_name').text(student.guardian_name || 'N/A');
                    $('#view_relationship').text(student.relationship || 'N/A');
                    $('#view_guardian_contact').text(student.guardian_contact || 'N/A');

                    // academic information
                    $('#view_grade').text(student.grade || 'N/A');
                    $('#view_section').text(student.section || 'N/A');
                    $('#view_learning_modality').text(student.learning_modality || 'N/A');
                    $('#view_remarks').text(student.remarks || 'No additional remarks');

                    if (student.birth_date) {
                        const birthDate = new Date(student.birth_date);
                        const formattedDate = birthDate.toLocaleDateString('en-US', {
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric'
                        });
                        $('#view_birth_date').text(formattedDate);
                    }

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

                    if (student.birth_date) {
                        const age = calculateAge(student.birth_date);
                        $('#view_birth_date').append(` <small class="text-muted">(${age} years old)</small>`);
                    }
    
                    $('#viewStudentModal').modal('show');
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Could not retrieve student details'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching student details:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Network Error',
                    text: 'Could not fetch student details. Please try again.'
                });
            }
        });
    });

    // Edit Student Details
    $(document).on('click', '.edit-student', function(e) {
        e.preventDefault();
        const studentId = $(this).data('id');
        
        console.log('Edit button clicked for student ID:', studentId);
    
        $.ajax({
            url: 'update_student_details.php',
            type: 'GET',
            data: { id: studentId },
            dataType: 'json',
            success: function(response) {
                // console.log('Full AJAX Response:', response);
    
                if (response.status === 'success') {
                    const student = response.student;
                    // console.log('Student Data:', student);
    
                    const nameParts = student.full_name.split(',').map(part => part.trim());
                    const lastNamePart = nameParts[0] || '';
                    const firstAndMiddleNames = nameParts[1] ? nameParts[1].split(' ') : [];
                    
                    const firstName = firstAndMiddleNames[0] || '';
                    const middleName = firstAndMiddleNames.slice(1).join(' ') || '';
    
                    // form fields with parsed name
                    $('#edit_student_id').val(student.id);
                    $('#edit_lrn').val(student.lrn);
                    
                    // personal details field
                    $('#edit_last_name').val(lastNamePart);
                    $('#edit_first_name').val(firstName);
                    $('#edit_middle_name').val(middleName);
                    $('#edit_birth_date').val(student.birth_date);
                    $('#edit_sex').val(student.sex);
                    $('#edit_religion').val(student.religion);
                    
                    // address information
                    $('#edit_street').val(student.street || '');
                    $('#edit_barangay').val(student.barangay || '');
                    $('#edit_municipality').val(student.municipality || '');
                    $('#edit_province').val(student.province || '');
                    $('#edit_contact_number').val(student.contact_number || '');
                    
                    // parent/guardian information
                    $('#edit_father_name').val(student.father_name || '');
                    $('#edit_mother_name').val(student.mother_name || '');
                    $('#edit_guardian_name').val(student.guardian_name || '');
                    $('#edit_guardian_relationship').val(student.relationship || '');
                    $('#edit_guardian_contact').val(student.guardian_contact || '');
                    
                    // academic information
                    $('#edit_grade').val(student.grade || '');
                    $('#edit_section').val(student.section || '');
                    $('#edit_learning_modality').val(student.learning_modality || '');
                    $('#edit_remarks').val(student.remarks || '');
    
                    // console.log('Parsed Name:', {
                    //     lastName: lastNamePart,
                    //     firstName: firstName,
                    //     middleName: middleName
                    // });
    
                    $('#editStudentModal').modal('show');
                } else {
                    console.error('Response error:', response.message);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Could not retrieve student details'
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
                        Could not fetch student details. 
                        <br>Status: ${status}
                        <br>Error: ${error}
                        <br>Check console for more details.
                    `
                });
            }
        });
    });

    // Update student record request
    $('#editStudentRecord').on('submit', function(e) {
        e.preventDefault();
        
        const fullName = [
            $('#edit_last_name').val().trim(),
            $('#edit_first_name').val().trim() + 
            (this.middle_name.value ? ' ' + this.middle_name.value.trim() : '')
        ].filter(Boolean).join(', ');
        
        const formData = new FormData(this);
        formData.set('full_name', fullName);
        formData.set('student_id', $('#edit_student_id').val());
        
        // for (let pair of formData.entries()) {
        //     console.log(pair[0] + ': ' + pair[1]);
        // }
        
        $.ajax({
            url: 'update_student_details.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            beforeSend: function() {
                $('#editStudentRecord button[type="submit"]')
                    .prop('disabled', true)
                    .html('<span class="spinner-border spinner-border-sm mr-1"></span>Updating...');
            },
            success: function(response) {
                console.log('Update Response:', response);

                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message || 'Student record updated successfully',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        $('#editStudentModal').modal('hide');
                        
                        setTimeout(loadStudents, 300);
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Could not update student record',
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

                let errorMessage = 'Could not update student record';
                
                try {
                    const errorResponse = JSON.parse(xhr.responseText);
                    errorMessage = errorResponse.message || errorMessage;
                } catch (e) {
                    errorMessage = xhr.statusText || errorMessage;
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Update Error',
                    text: errorMessage,
                    showConfirmButton: true
                });
            },
            complete: function() {
                $('#editStudentRecord button[type="submit"]')
                    .prop('disabled', false)
                    .html('Update Student');
            }
        });
    });

    // Attendance modal trigger
    $(document).on('click', '.view-attendance', function() {
        const row = $(this).closest('tr'); 
        const studentId = studentTable.row(row).data()[10]; 
        const studentName = row.find('td:nth-child(2)').text(); 
        
        $('#student_id').val(studentId);
    
        $('#attendanceModalLabel').html(`
            <i class="fas fa-calendar-check mr-2 text-success"></i>
            Attendance for ${studentName}
        `);
    
        $('#attendanceModal').modal('show'); // Show the modal
    }); 

    // Save attendance (AJAX submission)
    $('#attendanceForm').on('submit', function(e) {
        e.preventDefault(); // Prevent the form from submitting the traditional way
        
        const studentId = $('#student_id').val(); // Get the student ID from the hidden input
        const attendanceStatus = $('input[name="attendance"]:checked').val(); // Get the selected attendance status
        
        if (!attendanceStatus) {
            Swal.fire({
                icon: 'warning',
                title: 'No Attendance Status Selected',
                text: 'Please select either Present or Absent.',
            });
            return;
        }

        const attendanceDate = new Date().toISOString().split('T')[0]; // Get the current date in YYYY-MM-DD format

        console.log({
            student_id: studentId,
            attendance_date: attendanceDate,
            attendance: attendanceStatus
        }); // Log the data being sent

        // AJAX request to save attendance
        $.ajax({
            url: 'student_attendance.php', 
            type: 'POST',
            data: {
                student_id: studentId,
                attendance_date: attendanceDate,
                attendance: attendanceStatus
            },
            dataType: 'json',
            success: function(response) {
                console.log('Response:', response);
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Attendance Saved',
                        text: 'Attendance has been recorded successfully.',
                    }).then(() => {
                        resetAttendanceModal();
                        $('#attendanceModal').modal('hide'); 
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Failed to save attendance.',
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Error saving attendance:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Network Error',
                    text: 'Could not save attendance. Please try again.',
                });
            }
        });
    });

    function resetAttendanceModal() {
        // Clear the radio button selection
        $('input[name="attendance"]').prop('checked', false);
        
        // Clear the hidden input for student_id
        $('#student_id').val('');
        
        // Reset the modal label to default
        $('#attendanceModalLabel').html(`
            <i class="fas fa-calendar-check mr-2 text-success"></i>
            Attendance
        `);
    }
});