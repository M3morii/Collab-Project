<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
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
        document.getElementById('loginForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const submitBtn = document.getElementById('submitBtn');
            const errorMessages = document.getElementById('error-messages');
            const errorList = errorMessages.querySelector('ul');
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...';

            try {
                const formData = new FormData(this);
                
                const response = await fetch('/api/v1/login', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        email: formData.get('email'),
                        password: formData.get('password')
                    })
                });

                const data = await response.json();

                if (!response.ok) {
                    throw data;
                }

                localStorage.setItem('token', data.token);
                
                if (data.user.role === 'admin') {
                    alert("test")
                    window.location.href = '/admin/dashboard';
                } else if (data.user.role === 'teacher') {
                    window.location.href = '/teacher/dashboard';
                } else {
                    window.location.href = '/student/dashboard';
                }

            } catch (error) {
                errorList.innerHTML = '';
                errorMessages.classList.remove('d-none');

                if (error.errors) {
                    Object.values(error.errors).forEach(messages => {
                        messages.forEach(message => {
                            const li = document.createElement('li');
                            li.textContent = message;
                            errorList.appendChild(li);
                        });
                    });
                } else if (error.message) {
                    const li = document.createElement('li');
                    li.textContent = error.message;
                    errorList.appendChild(li);
                }
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Masuk';
            }
        });
    </script>
</body>
</html> 