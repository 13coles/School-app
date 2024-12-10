$(function() {
    $('#gradesTable').DataTable({
        "responsive": true,
        "lengthChange": false,
        "autoWidth": false
    });

    /* Note: this is just a temporary function, liwata lng later for necessary implementation for static demo purposes lg ni ang logics ko dri. */ 
    // Temporary (static functionality):function to handle saving grades
    function saveGrades($row) {
        const grades = {};
        const $gradeInputs = $row.find('.editable-grade');
        
        $gradeInputs.each(function() {
            const subject = $(this).data('subject');
            const grade = $(this).val();
            grades[subject] = grade;
        });
    }

    $('.btn-edit-grades').each(function() {
        const $row = $(this).closest('tr');
        const $saveButton = $('<button>', {
            class: 'btn btn-sm btn-success btn-save-grades ml-2',
            html: '<i class="fas fa-save mr-1"></i>Save Grades',
            click: function() {
                saveGrades($row);
            }
        }).prop('disabled', true);

        $(this).replaceWith($saveButton);
    });

    // enable save button when any grade is changed
    $(document).on('input', '.editable-grade', function() {
        const $row = $(this).closest('tr');
        const $saveButton = $row.find('.btn-save-grades');
        
        // check if any grade has changed
        let hasChanges = false;
        $row.find('.editable-grade').each(function() {
            if ($(this).val() !== $(this).attr('data-original-value')) {
                hasChanges = true;
                return false;
            }
        });

        $saveButton.prop('disabled', !hasChanges);
    });

    // regex code to ensure that only numeric numbers are entered in the input fields
    $(document).on('input', '.editable-grade', function() {
        let value = $(this).val();

        // remove non-numeric characters except decimal point
        value = value.replace(/[^0-9.]/g, '');
        
        // ensure only one decimal point
        const decimalCount = (value.match(/\./g) || []).length;
        if (decimalCount > 1) {
            value = value.replace(/\.+$/, '');
        }

        // limit grade to 99
        const numValue = parseFloat(value);
        if (numValue > 99) {
            value = '99'; 
        }

        $(this).val(value);
    });

    // store original values when page loads
    $('.editable-grade').each(function() {
        $(this).attr('data-original-value', $(this).val());
    });
});