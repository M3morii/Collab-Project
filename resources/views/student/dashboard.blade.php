<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-light">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#">Dashboard Siswa</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#"><i class="bi bi-house"></i> Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="bi bi-journal-text"></i> Tugas</a>
                    </li>
                </ul>
                <div class="dropdown">
                    <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle"></i> <span id="dropdownStudentName">Loading...</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#" onclick="editProfile()"><i class="bi bi-pencil"></i> Edit Profil</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="#" onclick="logout()"><i class="bi bi-box-arrow-right"></i> Keluar</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container py-4">
        <div class="row">
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Kelas yang Diikuti</h5>
                        <div class="row" id="enrolledClasses">
                            <!-- Classes will be loaded here -->
                            <div class="col-12 text-center">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Load enrolled classes
        function getStudentClasses() {
            fetch('/api/v1/student/dashboard/overview', {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                credentials: 'include'
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data); // Debug: cek struktur response
                
                const classesContainer = document.getElementById('enrolledClasses');
                
                if (data && data.data && data.data.classes) {
                    const classes = data.data.classes;
                    
                    if (classes.length > 0) {
                        let html = '';
                        classes.forEach(classRoom => {
                            html += `
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="card h-100 shadow-sm">
                                        <div class="card-body">
                                            <h5 class="card-title">${classRoom.name}</h5>
                                            <div class="mb-3">
                                                <p class="card-text text-muted mb-1">
                                                    <i class="bi bi-person-circle"></i> ${classRoom.teacher.name}
                                                </p>
                                                <small class="text-muted">
                                                    <i class="bi bi-calendar3"></i> Dibuat: ${new Date(classRoom.created_at).toLocaleDateString('id-ID')}
                                                </small>
                                            </div>
                                            <div class="d-grid">
                                                <a href="/student/classes/${classRoom.id}" class="btn btn-primary">
                                                    <i class="bi bi-arrow-right-circle"></i> Masuk Kelas
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                        
                        classesContainer.innerHTML = html;
                    } else {
                        classesContainer.innerHTML = `
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle"></i> Anda belum terdaftar di kelas manapun.
                                </div>
                            </div>
                        `;
                    }
                } else {
                    throw new Error('Data tidak valid');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('enrolledClasses').innerHTML = `
                    <div class="col-12">
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle"></i> Gagal memuat daftar kelas: ${error.message}
                        </div>
                    </div>
                `;
            });
        }

        // Load profile
        function loadProfile() {
            fetch('/api/v1/profile', {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                credentials: 'include'
            })
            .then(response => response.json())
            .then(data => {
                if (data.user && data.user.name) {
                    document.getElementById('dropdownStudentName').textContent = data.user.name;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('dropdownStudentName').textContent = 'Siswa';
            });
        }

        // Load data when page loads
        document.addEventListener('DOMContentLoaded', function() {
            loadProfile();
            getStudentClasses();
        });

        // Logout function
        function logout() {
            if (confirm('Apakah Anda yakin ingin keluar?')) {
                fetch('/logout', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    credentials: 'include'
                })
                .then(() => {
                    window.location.href = '/login';
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Gagal keluar. Silakan coba lagi.');
                });
            }
        }
    </script>
</body>
</html> 