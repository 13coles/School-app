$(function () {
    $("#studentTable").DataTable({
        "responsive": true, 
        "lengthChange": false, 
        "autoWidth": false,
        "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"],
        "language": {
            "emptyTable": "No student records found",
            "zeroRecords": "No matching students found"
        }
    }).buttons().container().appendTo('#studentTable_wrapper .col-md-6:eq(0)');

    // Enable tooltips
    $('[data-toggle="tooltip"]').tooltip();

    // Attendance Modal Trigger
    $(document).on('click', '.btn-success', function() {
        // Get student details from the table row
        const studentName = $(this).closest('tr').find('td:nth-child(2)').text();
        
        // Set student name in modal based from the selected table row
        $('#attendanceModalLabel').html(`
            <i class="fas fa-calendar-check mr-2 text-success"></i>
            Attendance for ${studentName}
        `);

        // Show attendance modal
        $('#attendanceModal').modal('show');
    });
});