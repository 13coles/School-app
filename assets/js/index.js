function loadTopStudents() {
    
    fetch('http://localhost/School-app/admin/top-1p.php') 
        .then(response => response.json())
        .then(data => {
            // Check if the request was successful
            if (data.status === 'success') {
                let students = data.data;
                let tableBody = document.getElementById('top-students-table').getElementsByTagName('tbody')[0];

                // Clear existing table data
                tableBody.innerHTML = '';

                // Loop through students data and add rows to the table
                students.forEach((student, index) => {
                    let row = tableBody.insertRow();
                    row.innerHTML = `
                        <td>${index + 1}</td>
                        <td>${student.full_name}</td>
                        <td>${student.final_grade.toFixed(2)}</td>
                    `;
                });
            } else {
                // Handle error
                alert('Error loading data: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while loading the data.');
        });
}

// Call the function to load data when the page is ready
document.addEventListener('DOMContentLoaded', function() {
    loadTopStudents();
});