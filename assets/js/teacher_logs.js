let teacherLogTable;

$(document).ready(function () {
    teacherLogTable = $('#teacherLogTable').DataTable({
        "ajax": {
            "url": "fetch_teacher_logs.php",
            "type": "GET"
        },
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
        "columns": [
            { "data": 0 }, 
            { "data": 1 },
            { "data": 2 }, 
            { "data": 3 }, 
            { "data": 4 } 
        ],
        "columnDefs": [
            {
                "orderable": false,
                "targets": -1 
            },
            {
                "className": "text-center",
                "targets": [2, 3, 4] 
            }
        ],
        "order": [[2, "desc"]] 
    });

    // fetch teachers from the server and populate the dropdown
    $('#teacher_select').select2({
        ajax: {
            url: 'get_teachers.php',
            dataType: 'json',
            delay: 250,
            processResults: function(data) {
                return {
                    results: data.data.map(teacher => ({
                        id: teacher.id,
                        text: `${teacher.teacher_id_num} - ${teacher.full_name}`
                    }))
                };
            },
            cache: true
        },
        minimumInputLength: 1,
        dropdownParent: $('#timeinModal'),
        height: '38px',
    });

    // teacher time-in
    $('#addTeacherForm').on('submit', function(e) {
        e.preventDefault();

        $.ajax({
            url: 'timein.php',
            method: 'POST',
            data: {
                teacher_id: $('#teacher_select').val(),
                time_in: $('#time_in').val()
            },
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: `Time-in recorded for ${response.data.teacher_name}`,
                        showConfirmButton: false,
                        timer: 1500
                    });
                    $('#timeinModal').modal('hide');
                    $('#addTeacherForm')[0].reset();
                    $('#teacherLogTable').DataTable().ajax.reload();
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON || {};
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: response.message || 'Failed to record time-in',
                    confirmButtonText: 'OK'
                });
            }
        });
    });

    // timeout teacher
    $(document).on('click', '.time-out', function(e) {
        e.preventDefault();
        const logId = $(this).data('log-id');
        
        Swal.fire({
            title: 'Confirm Time-out',
            text: 'Are you sure you want to record time-out?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, record time-out'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'timeout.php',
                    method: 'POST',
                    data: { log_id: logId },
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: 'Time-out recorded successfully',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                teacherLogTable.ajax.reload(null, false);
                            });
                        }
                    },
                    error: function(xhr) {
                        const response = xhr.responseJSON || {};
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: response.message || 'Failed to record time-out',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            }
        });
    });
});
