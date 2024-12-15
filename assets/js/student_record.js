$(document).ready(function() {
    loadStudentRecord();
    // function to combine names
    function combineNames() {
        let lastName = $('#last_name').val() ? $('#last_name').val().trim() : '';
        let firstName = $('#first_name').val() ? $('#first_name').val().trim() : '';
        let middleName = $('#middle_name').val() ? $('#middle_name').val().trim() : '';
    
        // combine names
        let fullName = '';
        if (lastName) fullName += lastName;
        if (firstName) fullName += (fullName ? ', ' : '') + firstName;
        if (middleName) fullName += ' ' + middleName;
    
        // add a hidden input element that will hold the value of the concatinated names and take the full_name attribute to pass in the database
        if ($('#full_name').length === 0) {
            $('<input>').attr({
                type: 'hidden',
                id: 'full_name',
                name: 'full_name'
            }).appendTo('#addStudentRecordModal');
        }
    
        // set the value of the hidden input for the full name
        $('#full_name').val(fullName);
    
        // console.log('Combined Full Name:', fullName);
    
        return fullName; // Return the full name for additional validation if needed
    }
    // combine names for every changes
    $('#last_name, #first_name, #middle_name').on('input', combineNames);

    // form validation function
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
            
            // checking if field exists before accessing its value
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
    
        // Ccmbine the names before validation
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

    // form submission handler for student record
    $('#addStudentRecordForm').on('submit', function(e) {
        e.preventDefault();

        // remove previous errors
        $('.is-invalid').removeClass('is-invalid');

        const fullName = combineNames();

        // Validate the form
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
        formData.append('full_name', fullName);

        // for (let pair of formData.entries()) {
        //     console.log(pair[0] + ': ' + pair[1]);
        // }
    
        // AJAX request to create student record
        $.ajax({
            url: 'create_records.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Student Record Created',
                    text: `Student record created with LRN: ${response.lrn}`,
                    showConfirmButton: true
                }).then(() => {
                    $('#addStudentRecordForm')[0].reset();
                    
                    $('#addStudentRecordModal').modal('hide');

                    loadStudentRecord();
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

    // real-time validation as user types
    $('#addStudentRecordModal').find('input, select').on('change input', function() {
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

    // function to fetch and display students from the database
    function loadStudentRecord() {
        const teacherSection = `<?php echo $_SESSION['teacher_section']; ?>`; 
        
        $.ajax({
            url: 'fetch_records.php',
            type: 'GET',
            data: { section: teacherSection }, 
            dataType: 'json',
            success: function(response) {
                console.log('Response from fetch_records.php:', response);
                if (response.status === 'success') {
                    $('#studentTable tbody').empty();
        
                    response.students.forEach(student => {
                        let row = `
                            <tr>
                                <td>${student.lrn}</td>
                                <td>${student.full_name}</td>
                                <td class="text-center">${student.sex}</td>
                                <td class="text-center">${student.grade}</td>
                                <td class="text-center">${student.section}</td>
                                <td class="text-center">${student.barangay}</td>
                                <td>${student.municipality}</td>
                                <td>${student.province}</td>
                                <td>${student.contact_number}</td>
                                <td class="text-center">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="actionDropdown${student.id}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="actionDropdown${student.id}">
                                            <a class="dropdown-item view-student" data-id="${student.id}" style="cursor:default;">
                                                <i class="fas fa-eye text-info mr-2"></i> View Details
                                            </a>
                                            <a class="dropdown-item view-grades" href="../teacher/student_grades.php" style="cursor:default;">
                                                <i class="fas fa-list-alt text-info mr-2"></i> View Grades
                                            </a>
                                            <a class="dropdown-item attendance" style="cursor:default;">
                                                <i class="fas fa-calendar-check text-warning mr-2"></i> Attendance
                                            </a>
                                            <a class="dropdown-item edit-student" data-id="${student.id}" data-toggle="modal" data-target="#editStudentRecordModal" style="cursor:default;">
                                                <i class="fas fa-edit text-success mr-2"></i>Edit
                                            </a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item delete-student text-danger" data-id="${student.id}" style="cursor:default;">
                                                <i class="fas fa-archive text-danger mr-2"></i> Archive
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        `;
                        $('#studentTable tbody').append(row);
                    });
                    
                    $('[data-toggle="tooltip"]').tooltip();
    
                } else {
                    console.error('Failed to load students:', response.message);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Failed to load students'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                console.log('Response Text:', xhr.responseText); // Log the response text
                Swal.fire({
                    icon: 'error',
                    title: 'Network Error',
                    text: 'Could not fetch students. Please try again.'
                });
            }
        });
    }

    // view student details trigger logic
    $(document).on('click', '.view-student', function(e) {
        e.preventDefault();
        const studentId = $(this).data('id');
        
        // AJAX request to fetch details to display in the view details modal
        $.ajax({
            url: 'get_student_details.php', 
            type: 'GET',
            data: { id: studentId },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    const student = response.student;

                    // populate personal information
                    $('#view_lrn').text(student.lrn || 'N/A');
                    $('#view_full_name').text(student.full_name || 'N/A');
                    $('#view_birth_date').text(student.birth_date || 'N/A');
                    $('#view_sex').text(student.sex || 'N/A');
                    $('#view_religion').text(student.religion || 'N/A');
                    
                   // populate sections with consistent approach
                    $('#view_complete_address').text([
                        student.street, 
                        student.barangay, 
                        student.municipality, 
                        student.province, 
                    ].filter(Boolean).join(', '));

                    // populate each section systematically
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

                    // academic Information
                    $('#view_grade').text(student.grade || 'N/A');
                    $('#view_section').text(student.section || 'N/A');
                    $('#view_learning_modality').text(student.learning_modality || 'N/A');
                    $('#view_remarks').text(student.remarks || 'No additional remarks');

                    // birthday formatting
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
    
                    $('#viewStudentRecordModal').modal('show');
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
        
        // console.log('Edit button clicked for student ID:', studentId);
    
        $.ajax({
            url: 'update_records.php',
            type: 'GET',
            data: { id: studentId },
            dataType: 'json',
            success: function(response) {
                // console.log('Full AJAX Response:', response);
    
                if (response.status === 'success') {
                    const student = response.student;
                    // console.log('Student Data:', student);
                    
                    // break the full name into 3 parts
                    const nameParts = student.full_name.split(',').map(part => part.trim());
                    const lastNamePart = nameParts[0] || '';
                    const firstAndMiddleNames = nameParts[1] ? nameParts[1].split(' ') : [];
                    
                    const firstName = firstAndMiddleNames[0] || '';
                    const middleName = firstAndMiddleNames.slice(1).join(' ') || '';
    
                    // populate form fields
                    $('#edit_student_id').val(student.id);
                    $('#edit_lrn').val(student.lrn);
                    
                    // personal information
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
    
                    $('#editStudentRecordModal').modal('show');
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

    // update student record request
    $('#editStudentRecord').on('submit', function(e) {
        e.preventDefault();
        
        // combine separated names into one (full name)
        const fullName = [
            $('#edit_last_name').val().trim(),
            $('#edit_first_name').val().trim() + 
            (this.middle_name.value ? ' ' + this.middle_name.value.trim() : '')
        ].filter(Boolean).join(', ');
        
        const formData = new FormData(this);
        formData.set('full_name', fullName);
        formData.set('student_id', $('#edit_student_id').val());
        
        $.ajax({
            url: 'update_records.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Student record updated successfully',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        $('#editStudentModal').modal('hide');
                        $('#editStudentRecordModal').modal('hide');

                        loadStudentRecord();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Could not update student record'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Update error:', xhr.responseText);
                Swal.fire({
                    icon: 'error',
                    title: 'Network Error',
                    text: xhr.responseJSON?.message || 'Could not update student record. Please try again.'
                });
            }
        });
    });

    // attendance modal trigger
    $(document).on('click', '.attendance', function() {
        // get student details from the table row
        const studentName = $(this).closest('tr').find('td:nth-child(2)').text();
        
        // set student name in modal based from the selected table row
        $('#attendanceModalLabel').html(`
            <i class="fas fa-calendar-check mr-2 text-success"></i>
            Attendance for ${studentName}
        `);

        // show attendance modal
        $('#attendanceModal').modal('show');
    });
});