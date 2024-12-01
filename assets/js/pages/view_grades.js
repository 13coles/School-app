$(function() {
    // Initialize DataTable
    $('#gradesTable').DataTable({
        "responsive": true,
        "lengthChange": false,
        "autoWidth": false
    });

    // Modify the Edit Grades Button Handler
    $('.btn-edit-grades').on('click', function() {
        const $row = $(this).closest('tr');
        const $gradeInputs = $row.find('.editable-grade');
        const $editBtn = $(this);

        if ($editBtn.hasClass('btn-primary')) {
            // Store original values before editing
            $gradeInputs.each(function() {
                $(this).attr('data-original-value', $(this).val());
            });

            // Switch to Edit Mode
            $gradeInputs.prop('readonly', false)
                        .addClass('bg-light')
                        .first().focus();
            
            $editBtn.removeClass('btn-primary')
                    .addClass('btn-success')
                    .html('<i class="fas fa-save mr-1"></i>Save Grades');

            // Add Cancel Button
            const $cancelBtn = $('<button>', {
                class: 'btn btn-sm btn-danger ml-2 btn-cancel-grades',
                html: '<i class="fas fa-times mr-1"></i>Cancel'
            });
            $(this).after($cancelBtn);
        } else {
            // Save Grades
            const grades = {};
            $gradeInputs.each(function() {
                const subject = $(this).data('subject');
                const grade = $(this).val();
                grades[subject] = grade;
            });

            // Switch back to View Mode
            resetGradesView($row);
        }
    });

    // Add Cancel Button Handler
    $(document).on('click', '.btn-cancel-grades', function() {
        const $row = $(this).closest('tr');
        const $gradeInputs = $row.find('.editable-grade');
        
        // Restore original values
        $gradeInputs.each(function() {
            $(this).val($(this).attr('data-original-value'));
        });

        // Reset to view mode
        resetGradesView($row);
    });

    // Function to reset view mode
    function resetGradesView($row) {
        const $gradeInputs = $row.find('.editable-grade');
        const $editBtn = $row.find('.btn-edit-grades');
        const $cancelBtn = $row.find('.btn-cancel-grades');

        $gradeInputs.prop('readonly', true)
                    .removeClass('bg-light');
        
        $editBtn.removeClass('btn-success')
                .addClass('btn-primary')
                .html('<i class="fas fa-edit mr-2"></i>Edit Grade');
        
        // Remove cancel button
        if ($cancelBtn.length) {
            $cancelBtn.remove();
        }
    }

    // Validate numeric input for grades
    $(document).on('input', '.editable-grade', function() {
        // Allow only numbers and limit to 2 decimal places
        let value = $(this).val();
        value = value.replace(/[^0-9.]/g, '');
        
        // Ensure only one decimal point
        const decimalCount = (value.match(/\./g) || []).length;
        if (decimalCount > 1) {
            value = value.replace(/\.+$/, '');
        }

        // Limit to 100
        const numValue = parseFloat(value);
        if (numValue > 99) {
            value = '99';
        }

        $(this).val(value);
    });

    // Optional: Add keyboard support for editing
    $(document).on('keydown', '.editable-grade', function(e) {
        if (e.key === 'Enter') {
            $(this).closest('tr').find('.btn-edit-grades').click();
        }
    });
});