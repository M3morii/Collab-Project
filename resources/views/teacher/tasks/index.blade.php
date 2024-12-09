<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Manajemen Tugas - {{ $class->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.5/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.5/dist/sweetalert2.all.min.js"></script>
</head>
<body class="bg-light">
    <script>
        // Definisikan classId di awal
        const classId = {{ $class->id }};
    </script>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#">{{ $class->name }}</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('teacher.dashboard') }}">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                    </li>
                </ul>
                <div class="d-flex">
                    <div class="dropdown">
                        <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i> {{ auth()->user()->name }}
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
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-1">Manajemen Tugas</h4>
                <p class="text-muted mb-0">{{ $class->name }}</p>
            </div>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createTaskModal">
                <i class="bi bi-plus-circle"></i> Tambah Tugas
            </button>
        </div>

        <!-- Daftar Tugas -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Judul</th>
                                <th>Tipe</th>
                                <th>Tenggat</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tasks as $task)
                            <tr data-task-id="{{ $task->id }}">
                                <td>{{ $task->title }}</td>
                                <td>
                                    <span class="badge bg-{{ $task->task_type === 'individual' ? 'info' : 'warning' }}">
                                        {{ $task->task_type === 'individual' ? 'Individu' : 'Kelompok' }}
                                    </span>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($task->deadline)->format('d M Y H:i') }}</td>
                                <td>
                                    <span class="badge bg-{{ $task->status === 'published' ? 'success' : 'secondary' }}">
                                        {{ $task->status === 'published' ? 'Dipublikasi' : 'Draft' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-info" 
                                                onclick="viewTask({{ $task->id }})" title="Lihat Detail">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-warning" 
                                                onclick="editTask({{ $task->id }})" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger" 
                                                onclick="deleteTask({{ $task->id }})" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <i class="bi bi-clipboard-x fs-1 text-muted"></i>
                                    <p class="mt-3">Belum ada tugas di kelas ini</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Tugas -->
    <div class="modal fade" id="createTaskModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Tugas Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="createTaskForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Judul Tugas</label>
                            <input type="text" class="form-control" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea class="form-control" name="description" rows="3" required></textarea>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Mulai</label>
                                <input type="datetime-local" class="form-control" name="start_date" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tenggat Waktu</label>
                                <input type="datetime-local" class="form-control" name="deadline" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Tipe Tugas</label>
                                <select class="form-select" name="task_type" required>
                                    <option value="individual">Individu</option>
                                    <option value="group">Kelompok</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Nilai Maksimal</label>
                                <input type="number" class="form-control" name="max_score" min="0" max="100" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Bobot (%)</label>
                                <input type="number" class="form-control" name="weight_percentage" min="0" max="100" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status" required>
                                <option value="draft">Draft</option>
                                <option value="published">Publikasikan</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit Tugas -->
    <div class="modal fade" id="editTaskModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Tugas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editTaskForm">
                    <input type="hidden" name="task_id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Judul Tugas</label>
                            <input type="text" class="form-control" name="title">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea class="form-control" name="description" rows="3"></textarea>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Mulai</label>
                                <input type="datetime-local" class="form-control" name="start_date">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tenggat Waktu</label>
                                <input type="datetime-local" class="form-control" name="deadline">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Tipe Tugas</label>
                                <select class="form-select" name="task_type">
                                    <option value="individual">Individu</option>
                                    <option value="group">Kelompok</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Nilai Maksimal</label>
                                <input type="number" class="form-control" name="max_score" min="0" max="100">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Bobot (%)</label>
                                <input type="number" class="form-control" name="weight_percentage" min="0" max="100">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status">
                                <option value="draft">Draft</option>
                                <option value="published">Publikasikan</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    function deleteTask(taskId) {
        Swal.fire({
            title: 'Hapus Tugas?',
            text: "Tugas yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/api/v1/teacher/classes/${classId}/tasks/${taskId}`,
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: 'Tugas telah dihapus',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            title: 'Gagal!',
                            text: 'Gagal menghapus tugas: ' + (xhr.responseJSON?.message || 'Terjadi kesalahan'),
                            icon: 'error'
                        });
                    }
                });
            }
        });
    }

    // Load tasks saat halaman dimuat
    function loadTasks() {
        $.ajax({
            url: `/api/v1/teacher/classes/${classId}/tasks`,
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                // Debug response
                console.log('Response:', response);
                
                // Ambil tasks dari response yang sesuai dengan TaskController
                const tasks = response; // karena TaskController langsung return tasks
                
                let tasksHtml = '';
                
                if (!tasks || tasks.length === 0) {
                    tasksHtml = `
                        <div class="text-center py-5">
                            <i class="bi bi-clipboard-x fs-1 text-muted"></i>
                            <p class="mt-3">Belum ada tugas di kelas ini</p>
                        </div>
                    `;
                } else {
                    tasksHtml = `
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Judul</th>
                                        <th>Tipe</th>
                                        <th>Tenggat</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                    `;
                    
                    tasks.forEach(task => {
                        tasksHtml += `
                            <tr>
                                <td>${task.title}</td>
                                <td>
                                    <span class="badge bg-${task.task_type === 'individual' ? 'info' : 'warning'}">
                                        ${task.task_type === 'individual' ? 'Individu' : 'Kelompok'}
                                    </span>
                                </td>
                                <td>${new Date(task.deadline).toLocaleDateString('id-ID', { 
                                    day: '2-digit',
                                    month: 'short',
                                    year: 'numeric',
                                    hour: '2-digit',
                                    minute: '2-digit'
                                })}</td>
                                <td>
                                    <span class="badge bg-${task.status === 'published' ? 'success' : 'secondary'}">
                                        ${task.status === 'published' ? 'Dipublikasi' : 'Draft'}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-info" 
                                                onclick="viewTask(${task.id})" title="Lihat Detail">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-warning" 
                                                onclick="editTask(${task.id})" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger" 
                                                onclick="deleteTask(${task.id})" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        `;
                    });
                    
                    tasksHtml += `
                                </tbody>
                            </table>
                        </div>
                    `;
                }
                
                $('.card-body').html(tasksHtml);
            },
            error: function(xhr) {
                console.error('Error:', xhr);
                alert('Gagal memuat daftar tugas');
            }
        });
    }

    // Load tasks saat halaman dimuat
    loadTasks();

    // Handle form submit untuk membuat tugas baru
    $('#createTaskForm').on('submit', function(e) {
        e.preventDefault();
        
        let formData = new FormData(this);
        
        Swal.fire({
            title: 'Memproses...',
            text: 'Mohon tunggu sebentar',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        $.ajax({
            url: `/api/v1/teacher/classes/${classId}/tasks`,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#createTaskModal').modal('hide');
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'Tugas baru telah dibuat',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            },
            error: function(xhr) {
                Swal.fire({
                    title: 'Gagal!',
                    text: 'Gagal membuat tugas: ' + (xhr.responseJSON?.message || 'Terjadi kesalahan'),
                    icon: 'error'
                });
            }
        });
    });

    // Fungsi untuk melihat detail tugas
    function viewTask(taskId) {
        Swal.fire({
            title: 'Memuat...',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: `/api/v1/teacher/classes/${classId}/tasks/${taskId}`,
            method: 'GET',
            success: function(response) {
                // Debug response
                console.log('Response:', response);
                
                // Pastikan mengakses data dari response yang benar
                const task = response.data || response;
                
                // Format tanggal menggunakan fungsi helper
                const formatDate = (dateString) => {
                    if (!dateString) return '-';
                    return new Date(dateString).toLocaleString('id-ID', {
                        day: 'numeric',
                        month: 'long',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                };

                Swal.fire({
                    title: task.title || 'Detail Tugas',
                    html: `
                        <div class="text-start">
                            <p><strong>Deskripsi:</strong><br>${task.description || '-'}</p>
                            <p><strong>Tipe:</strong> ${task.task_type === 'individual' ? 'Individu' : 'Kelompok'}</p>
                            <p><strong>Tanggal Mulai:</strong> ${formatDate(task.start_date)}</p>
                            <p><strong>Tenggat:</strong> ${formatDate(task.deadline)}</p>
                            <p><strong>Nilai Maksimal:</strong> ${task.max_score || '0'}</p>
                            <p><strong>Bobot:</strong> ${task.weight_percentage || '0'}%</p>
                            <p><strong>Status:</strong> ${task.status === 'published' ? 'Dipublikasi' : 'Draft'}</p>
                        </div>
                    `,
                    width: '600px',
                    confirmButtonText: 'Tutup'
                });
            },
            error: function(xhr) {
                console.error('Error:', xhr);
                Swal.fire({
                    title: 'Gagal!',
                    text: 'Gagal memuat detail tugas: ' + (xhr.responseJSON?.message || 'Terjadi kesalahan'),
                    icon: 'error'
                });
            }
        });
    }

    // Ganti fungsi editTask dengan yang baru
    function editTask(taskId) {
        $.ajax({
            url: `/api/v1/teacher/classes/${classId}/tasks/${taskId}`,
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                // Isi form edit dengan data yang ada
                $('#editTaskForm input[name="task_id"]').val(taskId);
                $('#editTaskForm input[name="title"]').val(response.title);
                $('#editTaskForm textarea[name="description"]').val(response.description);
                
                // Format tanggal untuk input datetime-local
                if (response.start_date) {
                    $('#editTaskForm input[name="start_date"]').val(response.start_date.slice(0, 16));
                }
                if (response.deadline) {
                    $('#editTaskForm input[name="deadline"]').val(response.deadline.slice(0, 16));
                }
                
                $('#editTaskForm select[name="task_type"]').val(response.task_type);
                $('#editTaskForm input[name="max_score"]').val(response.max_score);
                $('#editTaskForm input[name="weight_percentage"]').val(response.weight_percentage);
                $('#editTaskForm select[name="status"]').val(response.status);

                // Tampilkan modal
                $('#editTaskModal').modal('show');
            },
            error: function(xhr) {
                Swal.fire({
                    title: 'Gagal!',
                    text: 'Gagal memuat data tugas: ' + (xhr.responseJSON?.message || 'Terjadi kesalahan'),
                    icon: 'error'
                });
            }
        });
    }

    // Handle submit form edit
    $('#editTaskForm').on('submit', function(e) {
        e.preventDefault();
        
        let formData = new FormData(this);
        let taskId = formData.get('task_id');
        
        Swal.fire({
            title: 'Memproses...',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        $.ajax({
            url: `/api/v1/teacher/classes/${classId}/tasks/${taskId}`,
            method: 'PUT',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#editTaskModal').modal('hide');
                
                // Update tampilan task yang diedit
                const taskRow = $(`tr[data-task-id="${taskId}"]`);
                const updatedHtml = `
                    <td>${response.data.title}</td>
                    <td>
                        <span class="badge bg-${response.data.task_type === 'individual' ? 'info' : 'warning'}">
                            ${response.data.task_type === 'individual' ? 'Individu' : 'Kelompok'}
                        </span>
                    </td>
                    <td>${new Date(response.data.deadline).toLocaleDateString('id-ID', { 
                        day: '2-digit',
                        month: 'short',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    })}</td>
                    <td>
                        <span class="badge bg-${response.data.status === 'published' ? 'success' : 'secondary'}">
                            ${response.data.status === 'published' ? 'Dipublikasi' : 'Draft'}
                        </span>
                    </td>
                    <td>
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-info" 
                                    onclick="viewTask(${response.data.id})" title="Lihat Detail">
                                <i class="bi bi-eye"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-warning" 
                                    onclick="editTask(${response.data.id})" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-danger" 
                                    onclick="deleteTask(${response.data.id})" title="Hapus">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </td>
                `;
                taskRow.html(updatedHtml);

                Swal.fire({
                    title: 'Berhasil!',
                    text: 'Tugas telah diperbarui',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                });
            },
            error: function(xhr) {
                Swal.fire({
                    title: 'Gagal!',
                    text: 'Gagal memperbarui tugas: ' + (xhr.responseJSON?.message || 'Terjadi kesalahan'),
                    icon: 'error'
                });
            }
        });
    });
    </script>
</body>
</html> 