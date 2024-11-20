<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body class="bg-light">
    <div class="min-vh-100 d-flex align-items-center justify-content-center">
        <div class="card shadow-sm" style="width: 400px">
            <div class="card-body p-4">
                <h2 class="text-center mb-4">Login</h2>

                <div id="error-messages" class="alert alert-danger d-none">
                    <ul class="mb-0"></ul>
                </div>

                <form id="loginForm">
                    @csrf
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" id="email" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <input type="password" name="password" id="password" class="form-control" required>
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                <i class="bi bi-eye-slash" id="toggleIcon"></i>
                            </button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary w-100" id="submitBtn">
                            Masuk
                        </button>
                    </div>
                </form>

                <p class="text-center mb-0">
                    Belum punya akun?
                    <a href="{{ route('register') }}" class="text-decoration-none">
                        Daftar disini
                    </a>
                </p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle Password Visibility
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');
        const toggleIcon = document.querySelector('#toggleIcon');

        togglePassword.addEventListener('click', function () {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            toggleIcon.classList.toggle('bi-eye');
            toggleIcon.classList.toggle('bi-eye-slash');
        });

        // Login Form Submit
        $('#loginForm').on('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = $('#submitBtn');
            const errorMessages = $('#error-messages');
            const errorList = errorMessages.find('ul');
            
            // Disable button dan tampilkan loading
            submitBtn.prop('disabled', true)
                    .html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...');

            // Reset error messages
            errorList.empty();
            errorMessages.addClass('d-none');

            $.ajax({
                url: '/api/v1/login',
                type: 'POST',
                dataType: 'json',
                data: {
                    email: $('#email').val(),
                    password: $('#password').val()
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Accept': 'application/json'
                },
                success: function(data) {
                    // Simpan token di localStorage dan cookies
                    localStorage.setItem('token', data.token);
                    document.cookie = `auth_token=${data.token}; path=/`;
                    
                    // Redirect berdasarkan role dengan delay sedikit
                    setTimeout(() => {
                        if (data.user.role === 'admin') {
                            window.location.replace('/admin/dashboard');
                        } else if (data.user.role === 'teacher') {
                            window.location.replace('/teacher/dashboard');
                        } else {
                            window.location.replace('/student/dashboard');
                        }
                    }, 100);
                },
                error: function(xhr) {
                    errorMessages.removeClass('d-none');
                    errorList.empty();

                    if (xhr.responseJSON.errors) {
                        Object.values(xhr.responseJSON.errors).forEach(messages => {
                            messages.forEach(message => {
                                errorList.append(`<li>${message}</li>`);
                            });
                        });
                    } else if (xhr.responseJSON.message) {
                        errorList.append(`<li>${xhr.responseJSON.message}</li>`);
                    }
                },
                complete: function() {
                    // Reset button state
                    submitBtn.prop('disabled', false)
                            .html('Masuk');
                }
            });
        });
    </script>
</body>
</html> 