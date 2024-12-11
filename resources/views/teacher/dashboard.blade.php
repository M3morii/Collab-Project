<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard Guru</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-bootstrap-4/bootstrap-4.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-light">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#">Dashboard Guru</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('teacher.dashboard') }}">Dashboard</a>
                    </li>
                </ul>
                <div class="d-flex">
                    <div class="dropdown">
                        <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i> 
                            <span id="userName">Loading...</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item">Keluar</button>
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
        <!-- Welcome Card -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Selamat Datang, <span id="welcomeUserName">Loading...</span>!</h5>
                <p class="card-text">Berikut adalah ringkasan kelas anda.</p>
            </div>
        </div>

        <!-- Class Summary -->
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h6 class="card-title">Total Kelas</h6>
                        <h2>{{ $classes->count() }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h6 class="card-title">Kelas Aktif</h6>
                        <h2>{{ $classes->where('status', 'active')->count() }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h6 class="card-title">Total Siswa</h6>
                        <h2>{{ $classes->sum(function($class) { return $class->students->count(); }) }}</h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Classes -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Kelas Terbaru</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nama Kelas</th>
                                <th>Jumlah Siswa</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($classes->take(5) as $class)
                            <tr>
                                <td>{{ $class->name }}</td>
                                <td>{{ $class->students->count() }}</td>
                                <td>
                                    <span class="badge bg-{{ $class->status === 'active' ? 'success' : 'secondary' }}">
                                        {{ $class->status === 'active' ? 'Aktif' : 'Tidak Aktif' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="card-footer bg-white border-top-0">
                                        <div class="d-grid gap-2">
                                            <a href="{{ route('teacher.tasks.index', ['classId' => $class->id]) }}" 
                                               class="btn btn-outline-primary">
                                                <i class="bi bi-list-task"></i> Manajemen Tugas
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center">Belum ada kelas yang ditugaskan</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Event listener untuk form logout
        document.querySelector('form[action="{{ route("logout") }}"]').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Konfirmasi logout menggunakan SweetAlert2
            const result = await Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Anda telah keluar",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Keluar!',
                cancelButtonText: 'Batal'
            });

            if (result.isConfirmed) {
                try {
                    // Lakukan proses logout dengan AJAX
                    const response = await fetch('/logout', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    });

                    // Hapus token dari localStorage jika ada
                    localStorage.removeItem('token');
                    
                    // Redirect ke halaman login
                    window.location.href = '/login';
                    
                } catch (error) {
                    console.error('Error:', error);
                    // Jika terjadi error, tetap redirect ke login
                    window.location.href = '/login';
                }
            }
        });

        // Tambahkan script untuk mengambil data profile
        document.addEventListener('DOMContentLoaded', async function() {
            try {
                const response = await fetch('/api/v1/profile', {
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('token')}`,
                        'Accept': 'application/json'
                    }
                });
                
                if (response.ok) {
                    const data = await response.json();
                    document.getElementById('userName').textContent = data.user.name;
                    document.getElementById('welcomeUserName').textContent = data.user.name;
                } else {
                    const errorText = 'Error loading name';
                    document.getElementById('userName').textContent = errorText;
                    document.getElementById('welcomeUserName').textContent = errorText;
                }
            } catch (error) {
                console.error('Error:', error);
                const errorText = 'Error loading name';
                document.getElementById('userName').textContent = errorText;
                document.getElementById('welcomeUserName').textContent = errorText;
            }
        });
    </script>
</body>
</html> 