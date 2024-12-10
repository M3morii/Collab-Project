<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Detail Kelas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-light">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#" id="className">Detail Kelas</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('student.dashboard') }}">
                            <i class="bi bi-arrow-left"></i> Kembali
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
        <!-- Info Kelas -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <h4 class="card-title mb-1" id="classTitle">Memuat...</h4>
                        <p class="text-muted mb-0" id="teacherName">
                            <i class="bi bi-person"></i> Memuat...
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Daftar Tugas -->
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-3">
                    <i class="bi bi-list-task"></i> Daftar Tugas
                </h5>
                <div id="tasksList">
                    <!-- Tasks will be loaded here -->
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const classId = {{ $classId }};

        $(document).ready(function() {
            loadClassDetails();
            loadClassTasks();
        });

        function loadClassDetails() {
            $.ajax({
                url: `/api/v1/student/dashboard/overview`,
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('token')}`,
                    'Accept': 'application/json'
                },
                success: function(response) {
                    // Cari kelas yang sesuai dengan classId
                    const classData = response.data.classes.find(c => c.id == classId);
                    if (classData) {
                        $('#className, #classTitle').text(classData.name);
                        $('#teacherName').html(`<i class="bi bi-person"></i> ${classData.teacher.name}`);
                    } else {
                        $('#className, #classTitle').text('Kelas tidak ditemukan');
                        $('#teacherName').html(`<i class="bi bi-person"></i> -`);
                    }
                },
                error: function(xhr) {
                    console.error('Error:', xhr);
                    $('#className, #classTitle').text('Error memuat data');
                    $('#teacherName').html(`<i class="bi bi-person"></i> Error memuat data`);
                }
            });
        }

        function loadClassTasks() {
            $.ajax({
                url: `/api/v1/student/classes/${classId}/tasks`,
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('token')}`,
                    'Accept': 'application/json'
                },
                success: function(response) {
                    const tasks = response.data.tasks || [];
                    let html = '';

                    if (tasks.length > 0) {
                        tasks.forEach(task => {
                            const deadline = new Date(task.deadline).toLocaleDateString('id-ID', {
                                day: 'numeric',
                                month: 'long',
                                year: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit'
                            });

                            // Cek status tugas
                            const isSubmitted = task.status === 'submitted' || 
                                              (new URLSearchParams(window.location.search).get('taskId') == task.id && 
                                               new URLSearchParams(window.location.search).get('status') === 'submitted');
                            
                            html += `
                                <div class="card mb-2">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1">${task.title}</h6>
                                                <p class="mb-1 text-muted small">
                                                    ${task.description || 'Tidak ada deskripsi'}
                                                </p>
                                                <small class="text-muted">
                                                    <i class="bi bi-calendar"></i> Deadline: ${deadline}
                                                </small>
                                            </div>
                                            <div>
                                                ${isSubmitted ? 
                                                    `<span class="badge bg-success">
                                                        <i class="bi bi-check-circle"></i> Sudah Dikumpulkan
                                                     </span>` :
                                                    `<a href="/student/tasks/${task.id}?classId=${classId}" 
                                                        class="btn btn-primary btn-sm">
                                                        <i class="bi bi-pencil-square"></i> Kerjakan Tugas
                                                    </a>`
                                                }
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                    } else {
                        html = `
                            <div class="text-center py-4">
                                <i class="bi bi-clipboard-x fs-1 text-muted"></i>
                                <p class="mt-2">Belum ada tugas di kelas ini</p>
                            </div>
                        `;
                    }

                    $('#tasksList').html(html);

                    // Tampilkan notifikasi jika baru selesai mengumpulkan
                    const status = new URLSearchParams(window.location.search).get('status');
                    if (status === 'submitted') {
                        Swal.fire({
                            title: 'Tugas Berhasil Dikumpulkan!',
                            text: 'Terima kasih telah mengerjakan tugas',
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        });
                        // Bersihkan parameter URL
                        window.history.replaceState({}, document.title, `/student/classes/${classId}`);
                    }
                },
                error: function(xhr) {
                    $('#tasksList').html(`
                        <div class="alert alert-danger">
                            Gagal memuat daftar tugas
                        </div>
                    `);
                }
            });
        }
    </script>
</body>
</html> 