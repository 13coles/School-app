$(function() {
    // Initializing data table
    $('#gradesTable').DataTable({
        "responsive": true,
        "lengthChange": false,
        "autoWidth": false
    });

    /* Note: this is just a temporary function, liwata lng later for necessary implementation for static demo purposes lg ni ang logics ko dri. */ 

    // Edit grades button event listener
    $('.btn-edit-grades').on('click', function() {
        const $row = $(this).closest('tr'); // getting the tchosen row data to edit
        const $gradeInputs = $row.find('.editable-grade');
        const $editBtn = $(this);

        if ($editBtn.hasClass('btn-primary')) {
            // If the button is on edit, implement this logic
            // Store the original values before editing
            $gradeInputs.each(function() {
                $(this).attr('data-original-value', $(this).val());
            });

            // Switch the button to edit mode
            $gradeInputs.prop('readonly', false) // read only inputs if not being edited
                        .addClass('bg-light')
                        .first().focus();
            
            $editBtn.removeClass('btn-primary')
                    .addClass('btn-success')
                    .html('<i class="fas fa-save mr-1"></i>Save Grades'); // change the edit grade button into save button

            // Cancel button for cancellation of edits
            const $cancelBtn = $('<button>', {
                class: 'btn btn-sm btn-danger ml-2 btn-cancel-grades',
                html: '<i class="fas fa-times mr-1"></i>Cancel'
            });
            $(this).after($cancelBtn);
        } else {
            // Save the grades otherwise if it is not the edit button (save button is displayed)
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

    // Cancel button event listener
    $(document).on('click', '.btn-cancel-grades', function() {
        const $row = $(this).closest('tr'); // getting the chosen row data to edit
        const $gradeInputs = $row.find('.editable-grade');
        
        // Restore the previous original value if cancelled
        $gradeInputs.each(function() {
            $(this).val($(this).attr('data-original-value'));
        });

        // Return to view mode (readonly) after
        resetGradesView($row);
    });

    // Function for resetting view mode
    function resetGradesView($row) {
        const $gradeInputs = $row.find('.editable-grade');
        const $editBtn = $row.find('.btn-edit-grades');
        const $cancelBtn = $row.find('.btn-cancel-grades');

        $gradeInputs.prop('readonly', true)
                    .removeClass('bg-light');
        
        $editBtn.removeClass('btn-success')
                .addClass('btn-primary')
                .html('<i class="fas fa-edit mr-2"></i>Edit Grade');
        
        // Removing cancel button after returning to view mode
        if ($cancelBtn.length) {
            $cancelBtn.remove();
        }
    }

    // Regex code to ensure that only numeric numbers are entered in the input fields on every table cells
    $(document).on('input', '.editable-grade', function() {
        // Allow only numbers and limit it to 2 decimal places
        let value = $(this).val(); // get the current value inputted by the user

        /* 
            Regex "(/[^0-9.]/g)" means: it will remove all characters that are not numbers or a single decimal point.
            The "g" flag means: that this condition will be applied globally to all inputs that matches in the string. 
        */ 
        value = value.replace(/[^0-9.]/g, '');
        
        // Making sure that there is only one decimal point. Decimal points are allowed for inal grade computations.
        // The "\." is a regex that checks for decimal points with a matching "g" global flag to ensure it is checking globally.
        const decimalCount = (value.match(/\./g) || []).length;
        if (decimalCount > 1) {
            value = value.replace(/\.+$/, '');
        }

        // Giving a maximum of 99 value for the grades because grades cannot exceed 99.
        const numValue = parseFloat(value);
        if (numValue > 99) {
            value = '99';
        }

        $(this).val(value);
    });

    // Event listener to allow enter to be clicked and submit the form aside from just clicking the save button.
    $(document).on('keydown', '.editable-grade', function(e) {
        if (e.key === 'Enter') {
            $(this).closest('tr').find('.btn-edit-grades').click();
        }
    });
});