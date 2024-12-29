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

// function to fetch attendance records
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

function formatDate(dateString) {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', options);
}

// Attendance Printing
function printAttendance() {
    const selectedDate = $('#datepicker').val() ? moment($('#datepicker').val(), 'MM/DD/YYYY') : moment();
    const selectedTeacherId = $('#filterByTeacher').val();
    const printWindow = window.open('', '_blank');
    
    $.ajax({
        url: 'print-attendance.php',
        type: 'GET',
        dataType: 'json',
        data: { 
            date: selectedDate.format('YYYY-MM-DD'),
            teacher_id: selectedTeacherId,
            print: true 
        },
        success: function(response) {
            if (response.status === 'success' && response.attendance) {
                // Sort attendance records by gender and name
                const sortedAttendance = response.attendance.sort((a, b) => {
                    // First sort by gender (Male first)
                    if (a.sex !== b.sex) {
                        return a.sex === 'Male' ? -1 : 1;
                    }
                    // Then sort by name
                    return a.full_name.localeCompare(b.full_name);
                });

                // Split into male and female groups
                const maleStudents = sortedAttendance.filter(student => student.sex === 'Male');
                const femaleStudents = sortedAttendance.filter(student => student.sex === 'Female');

                let printContent = `
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
                                <strong>Grade Level:</strong> ${response.attendance[0]?.grade || ''} 
                                <strong>Section:</strong> ${response.attendance[0]?.section || ''}
                            </div>
                        </div>

                        <table class="attendance-table">
                            <thead>
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
                            </thead>
                            <tbody>
                                <!-- Male Students -->
                                <tr class="gender-header">
                                    <td colspan="${getWeekdayCount(selectedDate) + 4}">MALE</td>
                                </tr>
                                ${generateStudentRows(maleStudents, selectedDate)}

                                <!-- Female Students -->
                                <tr class="gender-header">
                                    <td colspan="${getWeekdayCount(selectedDate) + 4}">FEMALE</td>
                                </tr>
                                ${generateStudentRows(femaleStudents, selectedDate)}
                            </tbody>
                        </table>
                    </body>
                    </html>
                `;

                printWindow.document.write(printContent);
                printWindow.document.close();
                printWindow.onload = function() {
                    printWindow.print();
                    printWindow.onafterprint = function() {
                        printWindow.close();
                    };
                };
            } else {
                Swal.fire({
                    icon: 'info',
                    title: 'No Attendance Records',
                    text: 'No attendance records found for printing.'
                });
                printWindow.close();
            }
        },
        error: function(xhr, status, error) {
            console.error('Print Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Print Error',
                text: 'Could not generate attendance report.'
            });
            printWindow.close();
        }
    });
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

$(document).on('click', '.btn-primary', function() {
    printAttendance();
});