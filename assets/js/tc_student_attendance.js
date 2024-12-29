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
                        record.lrn,
                        record.full_name,
                        record.sex || 'N/A',
                        record.grade || 'N/A',
                        record.section || 'N/A',
                        record.attendance 
                            ? `<span class="badge badge-${
                                record.attendance === 'present' ? 'success' : 
                                record.attendance === 'late' ? 'warning' : 'danger'
                            }">${record.attendance}</span>`
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

// Initialize datepicker with today's date and trigger load
$('#datepicker').datepicker({
    format: 'yyyy-mm-dd',
    autoclose: true,
    todayHighlight: true
}).datepicker('setDate', new Date());

function formatDate(dateString) {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', options);
}

// function to fetch attendance records
function filterLoadedAttendance(selectedDate = null, selectedTeacherId = null) {
    const dateToFetch = selectedDate || moment().format('YYYY-MM-DD');

    $.ajax({
        url: 'tc_fetch_attendance.php',
        type: 'GET',
        dataType: 'json',
        data: { 
            date: dateToFetch,
        }, 
        success: function (response) {
            console.log('Fetch Attendance Response:', response);
            attendanceTable.clear();

            // Check if we have a successful response with an array
            if (response.status === 'success') {
                if (response.attendance && response.attendance.length > 0) {
                    // Populate the table with records
                    response.attendance.forEach((record) => {
                        // Function to determine badge color based on attendance status
                        const getBadgeClass = (status) => {
                            switch(status.toLowerCase()) {
                                case 'present':
                                    return 'success';
                                case 'absent':
                                    return 'danger';
                                case 'late':
                                    return 'warning';
                                default:
                                    return 'secondary';
                            }
                        };

                        attendanceTable.row.add([
                            record.lrn,
                            record.full_name,
                            record.sex || 'N/A',
                            record.grade || 'N/A',
                            record.section || 'N/A',
                            record.attendance 
                                ? `<span class="badge badge-${getBadgeClass(record.attendance)}">${record.attendance.toUpperCase()}</span>`
                                : 'N/A',
                            record.attendance_date ? formatDate(record.attendance_date) : 'Not Recorded',
                            record.student_id,
                        ]);
                    });

                    attendanceTable.draw();
                } else {
                    attendanceTable.draw();
                    Swal.fire({
                        icon: 'info',
                        title: 'No Records',
                        text: 'No attendance records found for the selected date.'
                    });
                }
            } else {
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
            
            attendanceTable.clear().draw();
            
            Swal.fire({
                icon: 'error',
                title: 'Network Error',
                text: 'Could not fetch attendance records.',
            });
        }
    });
}

// Edit Attendance
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
    $('#student_id').val(studentId);

    // Set the date field
    $('#attendance_date').val(attendanceDate ? formatDate(attendanceDate) : '');

    // Reset all radio buttons 
    $('#edit_presentRadio, #edit_absentRadio, #edit_lateRadio').prop('checked', false);

    switch(attendance) {
        case 'present':
            $('#edit_presentRadio').prop('checked', true);
            break;
        case 'absent':
            $('#edit_absentRadio').prop('checked', true);
            break;
        case 'late':
            $('#edit_lateRadio').prop('checked', true);
            break;
        default:
            console.warn('Undefined or invalid attendance status:', attendance);
    }
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
                Swal.fire({
                    icon: 'success',
                    title: 'Attendance Updated',
                    text: response.message || 'The attendance record has been updated successfully.',
                });

                $('#editAttendanceModal').modal('hide'); 

                loadAttendance(); 
            } else {
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

loadAttendance();

// Date picker filter 
$('#datepicker').on('changeDate', function(e) {
    // Format date correctly using moment
    const selectedDate = moment(e.date).format('YYYY-MM-DD');
    const selectedTeacherId = $('#filterByTeacher').val();
    filterLoadedAttendance(selectedDate, selectedTeacherId);
});

// Teacher side printing functionality
function printAttendance() {
    const selectedDate = moment($('#datepicker').val(), 'YYYY-MM-DD');
    let printWindow = null;

    $.ajax({
        url: 'tc_print-attendance.php',
        type: 'GET',
        dataType: 'json',
        data: { 
            date: selectedDate.format('YYYY-MM-DD')
        },
        beforeSend: function() {
            Swal.fire({
                title: 'Generating Report',
                text: 'Please wait...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });
        },
        success: function(response) {
            Swal.close();
            
            if (response.status === 'success' && Array.isArray(response.attendance)) {
                try {
                    printWindow = window.open('', '_blank');
                    if (!printWindow) {
                        throw new Error('Popup blocked');
                    }

                    const sortedAttendance = [...response.attendance].sort((a, b) => {
                        if (a.sex !== b.sex) return a.sex === 'Male' ? -1 : 1;
                        return a.full_name.localeCompare(b.full_name);
                    });

                    const printContent = generatePrintTemplate(selectedDate, sortedAttendance, response);
                    printWindow.document.write(printContent);
                    printWindow.document.close();

                    printWindow.onload = () => {
                        setTimeout(() => {
                            printWindow.print();
                            printWindow.onafterprint = () => printWindow.close();
                        }, 500);
                    };
                } catch (e) {
                    console.error('Print window error:', e);
                    if (printWindow) printWindow.close();
                    Swal.fire({
                        icon: 'error',
                        title: 'Print Error',
                        text: 'Please allow popups for printing.'
                    });
                }
            } else {
                Swal.fire({
                    icon: 'info',
                    title: 'No Records',
                    text: 'No attendance records found for printing.'
                });
            }
        },
        error: function(xhr, status, error) {
            Swal.close();
            console.error('Print Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Print Error',
                text: 'Could not generate attendance report. Please try again.'
            });
        }
    });
}

function generatePrintTemplate(selectedDate, attendance, response) {
    const maleStudents = attendance.filter(s => s.sex === 'Male');
    const femaleStudents = attendance.filter(s => s.sex === 'Female');
    const firstRecord = attendance[0] || {};

    return `
        <!DOCTYPE html>
        <html>
        <head>
            <title>SF2 - Daily Attendance Report</title>
            <style>
                @media print {
                    @page {
                        size: landscape;
                        margin: 0.5cm;
                    }
                }
                
                body {
                    font-family: Arial, sans-serif;
                    line-height: 1.2;
                    margin: 0;
                    padding: 1cm;
                }

                .header {
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    margin-bottom: 1rem;
                    gap: 1rem;
                }

                .logo {
                    width: 80px;
                    height: 80px;
                    object-fit: contain;
                }

                .header-text {
                    text-align: center;
                }

                .header-text h1 {
                    margin: 0;
                    font-size: 16px;
                    font-weight: bold;
                }

                .header-text p {
                    margin: 5px 0;
                    font-size: 12px;
                }

                .form-info {
                    display: grid;
                    grid-template-columns: repeat(3, 1fr);
                    gap: 1rem;
                    margin-bottom: 1rem;
                    font-size: 12px;
                }

                .attendance-table {
                    width: 100%;
                    border-collapse: collapse;
                    font-size: 11px;
                }

                .attendance-table th,
                .attendance-table td {
                    border: 1px solid black;
                    padding: 4px;
                    text-align: center;
                }

                .name-column {
                    text-align: left !important;
                    width: 200px;
                }

                .date-cell {
                    width: 25px;
                }

                .summary-cell {
                    width: 40px;
                }

                .remarks-cell {
                    width: 200px;
                }

                .gender-header {
                    background-color: #f3f4f6;
                    font-weight: bold;
                }
            </style>
        </head>
        <body>
            <div class="header">
                <img src="../assets/img/deped.png" alt="School Logo" class="logo">
                <div class="header-text">
                    <h1>School Form 2 (SF2) Daily Attendance Report of Learners</h1>
                    <p><em>(This replaces Form 1, Form 2 & STS Form 4 - Absenteeism and Dropout Profile)</em></p>
                </div>
            </div>

            <div class="form-info">
                <div>
                    <strong>Name of School:</strong> ${response.school_name || 'Sewahon National High School'}
                </div>
                <div>
                    <strong>School Year:</strong> ${response.school_year || '2023-2024'}
                </div>
                <div>
                    <strong>Report for the Month of:</strong> ${selectedDate.format('MMMM YYYY')}
                    <br>
                    <strong>Grade Level:</strong> ${firstRecord.grade || 'N/A'} 
                    <strong>Section:</strong> ${firstRecord.section || 'N/A'}
                </div>
            </div>

            <table class="attendance-table">
                <thead>
                    ${generateTableHeaders(selectedDate)}
                </thead>
                <tbody>
                    <tr class="gender-header">
                        <td colspan="${getWeekdayCount(selectedDate) + 4}">MALE</td>
                    </tr>
                    ${generateStudentRows(maleStudents, selectedDate)}
                    <tr class="gender-header">
                        <td colspan="${getWeekdayCount(selectedDate) + 4}">FEMALE</td>
                    </tr>
                    ${generateStudentRows(femaleStudents, selectedDate)}
                </tbody>
            </table>
        </body>
        </html>
    `;
}

function generateTableHeaders(selectedDate) {
    return `
        <tr>
            <th rowspan="2" class="name-column">LEARNER'S NAME<br>(Last Name, First Name, Middle Name)</th>
            ${generateDateHeaders(selectedDate)}
            <th colspan="2">Total for the Month</th>
            <th rowspan="2" class="remarks-cell">REMARKS</th>
        </tr>
        <tr>
            ${generateDateSubHeaders(selectedDate)}
            <th>ABSENT</th>
            <th>TARDY</th>
        </tr>
    `;
}

function getWeekdayCount(date) {
    const daysInMonth = date.daysInMonth();
    let weekdayCount = 0;
    
    for (let i = 1; i <= daysInMonth; i++) {
        const currentDate = moment(date).date(i);
        const dayOfWeek = currentDate.day();
        if (dayOfWeek !== 0 && dayOfWeek !== 6) {
            weekdayCount++;
        }
    }
    return weekdayCount;
}

function generateDateHeaders(selectedDate) {
    const daysInMonth = selectedDate.daysInMonth();
    let headers = '';
    
    for (let i = 1; i <= daysInMonth; i++) {
        const currentDate = moment(selectedDate).date(i);
        const dayOfWeek = currentDate.day();
        
        if (dayOfWeek !== 0 && dayOfWeek !== 6) {
            headers += `<th colspan="1" class="date-cell">${i}</th>`;
        }
    }
    return headers;
}

function generateDateSubHeaders(selectedDate) {
    const daysInMonth = selectedDate.daysInMonth();
    let subHeaders = '';
    
    for (let i = 1; i <= daysInMonth; i++) {
        const currentDate = moment(selectedDate).date(i);
        const dayOfWeek = currentDate.day();
        
        if (dayOfWeek !== 0 && dayOfWeek !== 6) {
            const dayAbbrev = currentDate.format('ddd').toUpperCase();
            subHeaders += `<th class="date-cell">${dayAbbrev[0]}</th>`;
        }
    }
    return subHeaders;
}

function generateAttendanceMarks(student, selectedDate) {
    const daysInMonth = selectedDate.daysInMonth();
    let marks = '';
    
    for (let i = 1; i <= daysInMonth; i++) {
        const currentDate = moment(selectedDate).date(i);
        const dayOfWeek = currentDate.day();
        
        if (dayOfWeek !== 0 && dayOfWeek !== 6) {
            let mark = '';  // Default to empty
            const dateStatus = student.attendance_dates[i];
            
            if (dateStatus) {
                if (dateStatus === 'ABSENT') mark = 'A';
                else if (dateStatus === 'LATE') mark = 'L';
                else if (dateStatus === 'PRESENT') mark = 'P';
            }
            
            marks += `<td class="date-cell">${mark}</td>`;
        }
    }
    
    return marks;
}

function generateStudentRows(students, selectedDate) {
    let rows = '';
    
    students.forEach(student => {
        const absentCount = Object.values(student.attendance_dates)
            .filter(status => status === 'ABSENT').length;
        const tardyCount = Object.values(student.attendance_dates)
            .filter(status => status === 'LATE').length;
        
        rows += `
            <tr>
                <td class="name-column">${student.full_name}</td>
                ${generateAttendanceMarks(student, selectedDate)}
                <td class="summary-cell">${absentCount}</td>
                <td class="summary-cell">${tardyCount}</td>
                <td class="remarks-cell"></td>
            </tr>
        `;
    });
    
    return rows;
}

$(document).on('click', '#print', function() {
    printAttendance();
});