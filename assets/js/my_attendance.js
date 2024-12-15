// my_attendance.js
$(function() {
    // Ensure DataTables is available before initializing
    if ($.fn.DataTable) {
        initializeDataTable();
    } else {
        console.error('DataTables is not loaded');
    }
});

function initializeDataTable() {
    let attendanceTable = $('#personalRecordTable').DataTable({
        "responsive": true,
        "lengthChange": true,
        "autoWidth": false,
        "pageLength": 10,
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
        "language": {
            "search": "Search your specific record:",
            "lengthMenu": "Show _MENU_ entries",
            "zeroRecords": "No matching records found",
            "info": "Showing _START_ to _END_ of _TOTAL_ records",
            "infoEmpty": "No records available",
            "infoFiltered": "(filtered from _MAX_ total records)"
        }
    });

    // Load initial attendance data
    loadAttendance();

    // Search functionality
    $('#studentSearch').on('keyup', function() {
        attendanceTable.search($(this).val()).draw();
    });
}

function loadAttendance(selectedDate = null) {
    // Convert the date to the correct format for the server
    const dateToFetch = selectedDate 
        ? moment(selectedDate).format('YYYY-MM-DD') 
        : moment().format('YYYY-MM-DD');

    $.ajax({
        url: 'fetch_my_attendance.php',
        type: 'GET',
        dataType: 'json',
        data: { 
            date: dateToFetch
        }, 
        success: function (response) {
            console.log('Fetch Attendance Response:', response);

            // Clear the table first
            let attendanceTable = $('#personalRecordTable').DataTable();
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
                            record.attendance_date ? formatDate(record.attendance_date) : 'Not Recorded'
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
                        text: response.message || 'No attendance records found for the selected date.',
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
            console.error('AJAX Error Details:', {
                status: status,
                error: error,
                responseText: xhr.responseText,
                readyState: xhr.readyState,
                responseStatus: xhr.status
            });
            
            // Clear the table on error
            let attendanceTable = $('#personalRecordTable').DataTable();
            attendanceTable.clear().draw();
            
            // More detailed error handling
            let errorMessage = 'Could not fetch attendance records.';
            
            if (xhr.status === 404) {
                errorMessage = 'Endpoint not found. Please check the server configuration.';
            } else if (xhr.status === 500) {
                errorMessage = 'Internal server error. Please contact support.';
            } else if (status === 'parsererror') {
                errorMessage = 'Invalid response from server. Response is not valid JSON.';
                
                // Try to parse and log the response
                try {
                    console.error('Attempted to parse response:', JSON.parse(xhr.responseText));
                } catch (parseError) {
                    console.error('Could not parse response:', parseError);
                }
            }
            
            Swal.fire({
                icon: 'error',
                title: 'Network Error',
                text: errorMessage,
                footer: `Status: ${status}, Response: ${xhr.responseText}`
            });
        }
    });
}

function formatDate(dateString) {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', options);
}

// Ensure moment.js is loaded before using it
if (typeof moment === 'function') {
    // Date picker initialization
    $('#datepicker').datepicker({
        format: 'mm/dd/yyyy',
        autoclose: true,
        startDate: null, 
        endDate: '0d', 
        todayHighlight: true
    }).on('changeDate', function(e) {
        const selectedDate = moment(e.date).format('MM/DD/YYYY');
        loadAttendance(selectedDate);
    });
} else {
    console.error('moment.js is not loaded');
}