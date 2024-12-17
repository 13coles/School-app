let attendanceTable;

$(document).ready(function () {
    attendanceTable = $('#attendanceTable').DataTable({
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

    loadAttendance();
});

$('#datepicker').datepicker({
    format: 'mm/dd/yyyy',
    autoclose: true,
    startDate: null, 
    endDate: '0d', 
    todayHighlight: true
});

loadAttendance();
loadTeachers();

// Teacher filter change event
$('#filterByTeacher').on('change', function() {
    const selectedTeacherId = $(this).val();
    const currentDate = moment().format('MM/DD/YYYY');
    
    // Reset date picker to current date
    $('#datepicker').datepicker('update', new Date());
    
    loadAttendance(currentDate, selectedTeacherId);
});

// Date picker change event
$('#datepicker').on('changeDate', function(e) {
    const selectedDate = moment(e.date).format('MM/DD/YYYY');
    const selectedTeacherId = $('#filterByTeacher').val();
    
    loadAttendance(selectedDate, selectedTeacherId);
});

function loadTeachers() {
    $.ajax({
        url: 'fetch_teachers.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success' && response.teachers) {
                const teacherSelect = $('#filterByTeacher');
                teacherSelect.empty();
                teacherSelect.append('<option value="">All Teachers</option>');
                
                response.teachers.forEach(teacher => {
                    teacherSelect.append(`
                        <option value="${teacher.id}">
                            ${teacher.full_name} (${teacher.grade} - ${teacher.section})
                        </option>
                    `);
                });
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading teachers:', error);
        }
    });
}

function loadAttendance(selectedDate = null, selectedTeacherId = null) {
    // Convert the date to the correct format for the server
    const dateToFetch = selectedDate 
        ? moment(selectedDate, 'MM/DD/YYYY').format('YYYY-MM-DD') 
        : moment().format('YYYY-MM-DD');

    $.ajax({
        url: 'fetch_attendance.php',
        type: 'GET',
        dataType: 'json',
        data: { 
            date: dateToFetch,
            teacher_id: selectedTeacherId
        }, 
        success: function (response) {
            console.log('Fetch Attendance Response:', response);
            console.log('Date Fetched:', dateToFetch);

            // Clear the table first
            attendanceTable.clear();

            // Check if we have a successful response with an array
            if (response.status === 'success') {
                if (response.attendance && response.attendance.length > 0) {
                    // Populate the table with records
                    response.attendance.forEach((record) => {
                        attendanceTable.row.add([
                            record.lrn,
                            record.full_name,
                            record.sex || 'N/A',
                            record.grade || 'N/A',
                            record.section || 'N/A',
                            record.attendance 
                                ? `<span class="badge badge-${record.attendance === 'present' ? 'success' : 'danger'}">${record.attendance}</span>`
                                : 'N/A',
                            record.attendance_date ? formatDate(record.attendance_date) : 'Not Recorded',
                            `
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a class="dropdown-item edit-attendance" style="cursor: default;"
                                        data-student-id="${record.student_id}" 
                                        data-lrn="${record.lrn}" 
                                        data-name="${record.full_name}" 
                                        data-attendance="${record.attendance}" 
                                        data-attendance-date="${record.attendance_date}">
                                        <i class="fas fa-edit text-success mr-2"></i> Edit
                                    </a>
                                </div>
                            </div>
                            `,
                            record.student_id,
                        ]);
                    });

                    attendanceTable.draw();
                    console.log(`Loaded ${response.attendance.length} attendance records`);
                } else {
                    // No records found
                    attendanceTable.draw();
                    Swal.fire({
                        icon: 'info',
                        title: 'No Attendance Records',
                        text: 'No attendance records found for the selected date.',
                    });
                }
            } else {
                // Error in response
                attendanceTable.draw();
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message || 'Could not fetch attendance records',
                });
            }
        },
        error: function (xhr, status, error) {
            console.error('AJAX Error:', status, error);
            
            // Clear the table on error
            attendanceTable.clear().draw();
            
            Swal.fire({
                icon: 'error',
                title: 'Network Error',
                text: 'Could not fetch attendance records.',
            });
        }
    });
}


function formatDate(dateString) {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', options);
}

// Trigger for opening the Edit Attendance Modal
$(document).on('click', '.edit-attendance', function () {
    const studentId = $(this).data('student-id');
    const name = $(this).data('name');
    const attendance = $(this).data('attendance');
    const attendanceDate = $(this).data('attendance-date');

    console.log('Editing attendance for:', { studentId, name, attendance, attendanceDate });

    // Highlight the row being edited
    const rowElement = $(this).closest('tr');
    attendanceTable.$('tr').removeClass('highlight');
    rowElement.addClass('highlight');

    // Populate modal fields
    $('#editAttendanceModalLabel').text(`Attendance for ${name}`);
    $('#student_id').val(studentId); // Use the correct student ID

    // Set the date field, ensuring correct format
    $('#attendance_date').val(attendanceDate ? formatDate(attendanceDate) : '');

    // Set radio buttons based on attendance status
    if (attendance === 'present') {
        $('#edit_presentRadio').prop('checked', true);
    } else if (attendance === 'absent') {
        $('#edit_absentRadio').prop('checked', true);
    } else {
        console.warn('Undefined or invalid attendance status:', attendance);
        $('#edit_presentRadio').prop('checked', false);
        $('#edit_absentRadio').prop('checked', false);
    }

    // Show modal
    $('#editAttendanceModal').modal('show');
});

$('#editAttendanceForm').on('submit', function (e) {
    e.preventDefault();

    const formData = {
        student_id: $('#student_id').val(),
        attendance: $('input[name="attendance"]:checked').val(),
        attendance_date: $('#attendance_date').val() || new Date().toISOString().split('T')[0] // Default to today's date
    };

    console.log('Updating attendance for:', formData);

    $.ajax({
        url: 'fetch_attendance.php', // Ensure this is the correct URL to handle the request
        type: 'POST',
        data: formData,
        success: function (response) {
            console.log('Update Response:', response);

            if (response.status === 'success') {
                // Show success message
                Swal.fire({
                    icon: 'success',
                    title: 'Attendance Updated',
                    text: response.message || 'The attendance record has been updated successfully.',
                });

                // Close the modal
                $('#editAttendanceModal').modal('hide'); 

                // Reload the attendance records
                loadAttendance(); 
            } else {
                // Show error message if the response status is not success
                Swal.fire({
                    icon: 'error',
                    title: 'Update Failed',
                    text: response.message || 'Could not update attendance record.',
                });
            }
        },
        error: function (xhr, status, error) {
            console.error('Error updating attendance:', error);
            Swal.fire({
                icon: 'error',
                title: 'Network Error',
                text: 'Could not update attendance record. Check the console for details.',
            });
        }
    });
});