<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Student Performance Metrics Management System</title>
        <!-- Tailwind script -->
        <script src="https://cdn.tailwindcss.com"></script>
        <!-- Google font -->
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    </head>
    <body class="bg-gray-50 font-inter min-h-screen flex flex-col">
        <header class="bg-white shadow-md">
            <div class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8 flex items-center justify-between">
                <div class="flex items-center">
                    <img src="assets/img/images.jfif" alt="Sewahon logo" class="h-12 w-12 mr-4 rounded-full">
                    <h1 class="text-2xl font-bold text-gray-900">Student Performance Metrics Management System</h1>
                </div>
            </div>
        </header>

        <main class="flex-grow flex flex-col items-center justify-center px-4 py-8">
            <img src="assets/img/images.jfif" alt="Sewahon logo" class="h-48 w-48 mb-8 rounded-full shadow-lg">
            
            <div class="w-full max-w-md">
                <div class="bg-white shadow-xl rounded-2xl overflow-hidden">
                    <div class="p-8 text-center">
                        <h2 class="text-2xl font-semibold text-gray-800 mb-6 tracking-wider uppercase" id="form-title">Sewahon National High School</h2>
                        <!-- Role Based Button Selection -->
                        <div id="role-selector" class="space-y-4">
                            <button onclick="showLoginForm('teacher')" class="w-full py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-300 ease-in-out transform hover:scale-105 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                                Teacher
                            </button>
                            <button onclick="showLoginForm('student')" class="w-full py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-300 ease-in-out transform hover:scale-105 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Student
                            </button>
                            <button onclick="showLoginForm('admin')" class="w-full py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-300 ease-in-out transform hover:scale-105 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                Admin
                            </button>
                        </div>

                        <!-- Dynamic Role Based Form -->
                        <div id="login-form" class="hidden">
                            <form id="dynamic-form" action="login.php" method="post" class="space-y-4">
                                <input type="hidden" name="role" id="role-input">
                                
                                <div class="mb-4">
                                    <label for="username" id="username-label" class="block text-left text-gray-700 mb-2">Username</label>
                                    <input type="text" name="username" id="username" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                
                                <div class="mb-4">
                                    <label for="password" class="block text-left text-gray-700 mb-2">Password</label>
                                    <input type="password" name="password" id="password" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                
                                <div id="additional-fields"></div>
                                
                                <button type="submit" class="w-full py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-300">
                                    Login
                                </button>
                                <button type="button" onclick="back()" class="w-full py-3 mt-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition duration-300">
                                    Back
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <footer class="bg-white shadow-md">
            <div class="max-w-7xl mx-auto px-4 py-4 sm:px-6 lg:px-8 text-center">
                <p class="text-gray-500 text-sm">&copy; <?php echo date('Y'); ?> Sewahon Performance Metrics. All rights reserved.</p>
            </div>
        </footer>

        <script type="text/javascript">
            function showLoginForm(role) {
                document.getElementById('role-selector').classList.add('hidden'); // Hide the role selector form (admin, teacher, student buttons)
                document.getElementById('login-form').classList.remove('hidden'); // Show the corresponding login forms based on role selected
                
                const formTitle = document.getElementById('form-title');
                formTitle.textContent = `${role.charAt(0).toUpperCase() + role.slice(1)} Login`;
                
                document.getElementById('role-input').value = role; 
                
                // Update username label and input based on role
                const usernameLabel = document.getElementById('username-label');
                const usernameInput = document.getElementById('username');
                const passwordInput = document.getElementById('password');
                
                switch(role) {
                    case 'teacher':
                        usernameLabel.textContent = 'Teacher ID';
                        usernameInput.placeholder = 'Enter Teacher ID';
                        usernameInput.name = 'teacher_id'; // update "name" attribute basd on role selection respectivly
                        passwordInput.name = 'teacher_password';
                        break;
                    case 'student':
                        usernameLabel.textContent = 'Learner Reference Number (LRN)';
                        usernameInput.placeholder = 'Enter LRN';
                        usernameInput.name = 'student_lrn'; // student role username input "name" attribute changed to student_lrn
                        passwordInput.name = 'student_password';
                        break;
                    case 'admin':
                        usernameLabel.textContent = 'Admin ID';
                        usernameInput.placeholder = 'Enter Admin ID';
                        usernameInput.name = 'admin_id'; // admin "name" attribute for admin
                        passwordInput.name = 'admin_password';
                        break;
                }
                
                document.getElementById('additional-fields').innerHTML = '';
            }

            function back() {
                document.getElementById('login-form').classList.add('hidden');
                document.getElementById('role-selector').classList.remove('hidden');
                document.getElementById('form-title').textContent = 'Sewahon National High School';
            }
        </script>
    </body>
</html>
                