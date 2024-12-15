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

function loadAttendance() {
    $.ajax({
        url: 'fetch_student_attendance.php',
        type: 'GET',
        dataType: 'json',
        success: function (response) {
            console.log('Fetch Attendance Response:', response);

            if (response.status === 'success' && Array.isArray(response.attendance)) {
                attendanceTable.clear();

                response.attendance.forEach((record) => {
                    attendanceTable.row.add([
                        record.student_id,
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
                    ]);
                });

                attendanceTable.draw();
                console.log(`Loaded ${response.attendance.length} attendance records`);
            } else {
                attendanceTable.clear().draw();
                Swal.fire({
                    icon: 'info',
                    title: 'No Attendance Records',
                    text: response.message || 'No attendance records found.',
                });
            }
        },
        error: function (xhr, status, error) {
            console.error('AJAX Error:', status, error);
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
        url: 'fetch_student_attendance.php', 
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
