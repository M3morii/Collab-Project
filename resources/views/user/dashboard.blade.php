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
                        <a class="nav-link active" href="#">
                            <i class="bi bi-house-door"></i> Beranda
                        </a>
                    </li>
                </ul>
                <div class="d-flex">
                    <div class="dropdown">
                        <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i> <span id="userName"></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="bi bi-box-arrow-right"></i> Keluar
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container py-4">
        <!-- Daftar Kelas -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-3">
                            <i class="bi bi-collection"></i> Kelas Saya
                        </h5>
                        <div id="classesList" class="row">
                            <!-- Classes will be loaded here -->
                            <div class="text-center py-4">
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

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            loadClasses();
        });

        function loadClasses() {
            $.ajax({
                url: '/api/v1/student/dashboard/overview',
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('token')}`,
                    'Accept': 'application/json'
                },
                success: function(response) {
                    const classes = response.data.classes;
                    let html = '';

                    if (classes.length > 0) {
                        classes.forEach(classRoom => {
                            html += `
                                <div class="col-md-4 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h5 class="card-title">${classRoom.name}</h5>
                                            <p class="card-text">
                                                <small class="text-muted">
                                                    <i class="bi bi-person"></i> ${classRoom.teacher.name}
                                                </small>
                                            </p>
                                            <a href="/student/classes/${classRoom.id}" class="btn btn-primary btn-sm">
                                                Lihat Kelas
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                    } else {
                        html = `
                            <div class="col-12">
                                <div class="text-center py-4">
                                    <i class="bi bi-info-circle text-muted fs-1"></i>
                                    <p class="mt-2">Belum ada kelas yang diikuti</p>
                                </div>
                            </div>
                        `;
                    }

                    $('#classesList').html(html);
                },
                error: function(xhr) {
                    $('#classesList').html(`
                        <div class="col-12">
                            <div class="alert alert-danger">
                                Gagal memuat data kelas
                            </div>
                        </div>
                    `);
                }
            });
        }
    </script>
</body>
</html> 