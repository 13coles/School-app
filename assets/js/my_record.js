$(document).ready(function() {
    // function to load all the records fetched from the response of the server
    function loadPersonalStudentRecord() {
        $.ajax({
            url: './personal_record.php',
            type: 'GET',
            dataType: 'json',
            xhrFields: {
                withCredentials: true
            },
            success: function(response) {
                if (response.status === 'success') {
                    $('#personalRecordTable tbody').empty();
                    
                    // display a message if there is no data
                    if (!response.students || response.students.length === 0) {
                        $('#personalRecordTable tbody').append(`
                            <tr>
                                <td colspan="10" class="text-center text-muted">
                                    No student record found
                                </td>
                            </tr>
                        `);
                        return;
                    }

                    // generate the tbody data based on the fetched response
                    response.students.forEach(student => {
                        let row = `
                            <tr>
                                <td>${student.lrn || 'N/A'}</td>
                                <td>${student.full_name || 'N/A'}</td>
                                <td class="text-center">${student.sex || 'N/A'}</td>
                                <td class="text-center">${student.grade || 'N/A'}</td>
                                <td class="text-center">${student.section || 'N/A'}</td>
                                <td class="text-center">${student.barangay || 'N/A'}</td>
                                <td>${student.municipality || 'N/A'}</td>
                                <td>${student.province || 'N/A'}</td>
                                <td>${student.contact_number || 'N/A'}</td>
                                <td class="text-center">
                                    <i class="fas fa-eye view-student-details" 
                                       data-student-id="${student.id}" 
                                       data-toggle="tooltip" 
                                       title="View Full Details" 
                                       style="cursor: pointer; color: #007bff;"></i>
                                </td>
                            </tr>
                        `;
                        $('#personalRecordTable tbody').append(row);
                    });

                    $('[data-toggle="tooltip"]').tooltip();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        html: `
                            <p>Failed to load student record</p>
                            <pre>${JSON.stringify(response, null, 2)}</pre>
                        `
                    });
                }
            },
            error: function(xhr, status, error) {
                let errorMessage = 'Unknown error occurred';
                try {
                    const errorResponse = JSON.parse(xhr.responseText);
                    errorMessage = errorResponse.message || JSON.stringify(errorResponse);
                } catch (parseError) {
                    errorMessage = xhr.responseText || error;
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Network Error',
                    html: `
                    Could not fetch student record. 
                    <br>Status: ${status}
                    <br>Error: ${errorMessage}
                    <br>Check console for more details.
                `
                });
            }
        });
    }

    // function to view student details
    function viewStudentFullDetails(studentId) {
        $.ajax({
            url: './get_details.php', 
            type: 'GET',
            data: { student_id: studentId },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    const student = response.student;
                    
                    // personal information fields
                    $('#view_lrn').text(student.lrn || 'N/A');
                    $('#view_full_name').text(student.full_name || 'N/A');
                    $('#view_birth_date').text(formatBirthDateWithAge(student.birth_date || 'N/A'));
                    $('#view_sex').text(student.sex || 'N/A');
                    $('#view_religion').text(student.religion || 'N/A');
                    
                    // address information fields
                    $('#view_complete_address').text([
                        student.street, 
                        student.barangay, 
                        student.municipality, 
                        student.province
                    ].filter(Boolean).join(', ') || 'N/A'); // join the separated streets, barngay, municipality and province to display as one

                    // address information fields
                    $('#view_street').text(student.street || 'N/A');
                    $('#view_barangay').text(student.barangay || 'N/A');
                    $('#view_municipality').text(student.municipality || 'N/A');
                    $('#view_province').text(student.province || 'N/A');
                    $('#view_contact_number').text(student.contact_number || 'N/A');

                    // parent/guardian information
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

                    $('#viewDetailsModal').modal('show');
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
                
                let errorMessage = 'Could not fetch student details';
                try {
                    const errorResponse = JSON.parse(xhr.responseText);
                    errorMessage = errorResponse.message || errorMessage;
                } catch (parseError) {
                    errorMessage = xhr.responseText || error;
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Network Error',
                    text: errorMessage
                });
            }
        });
    }

    // function to format birthday
    function formatBirthDateWithAge(birthDate) {
        if (!birthDate) return 'N/A';
    
        // parse the birth date
        const birth = new Date(birthDate);
        
        // calculate the age based on the response birthdate
        const today = new Date();
        let age = today.getFullYear() - birth.getFullYear();
        const monthDiff = today.getMonth() - birth.getMonth();
        
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
            age--;
        }
    
        // store the formatted date
        const formattedDate = birth.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    
        return `${formattedDate} (${age} years old)`;
    }

    // trigger to open the view details modal
    $(document).on('click', '.view-student-details', function(e) {
        e.preventDefault();
        const studentId = $(this).data('student-id');
        viewStudentFullDetails(studentId);
    });

    // search functionality
    $('#studentSearch').on('keyup', function() {
        var searchTerm = $(this).val().toLowerCase();
        
        $('#personalRecordTable tbody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(searchTerm) > -1)
        });
    });

    loadPersonalStudentRecord();
});