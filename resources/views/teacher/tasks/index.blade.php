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
                            <i class="bi bi-person-circle"></i> <span id="dropdownTeacherName">Loading...</span>
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
                <h4 class="card-title mb-0">
                    {{ request('type') == 'group' ? 'Manajemen Kelompok' : 'Manajemen Tugas' }} - {{ $class->name }}
                </h4>
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
                                @if(!request('type') || (request('type') == 'group' && $task->task_type == 'group'))
                                <tr>
                                    <td>{{ $task->title }}</td>
                                    <td>
                                        <span class="badge bg-{{ $task->task_type == 'group' ? 'warning' : 'info' }}">
                                            {{ $task->task_type == 'group' ? 'Kelompok' : 'Individu' }}
                                        </span>
                                    </td>
                                    <td>{{ $task->deadline }}</td>
                                    <td>
                                        <span class="badge bg-{{ $task->status === 'published' ? 'success' : 'secondary' }}">
                                            {{ $task->status === 'published' ? 'Dipublikasi' : 'Draft' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if(request('type') == 'group')
                                        <div class="d-grid gap-1">
                                            <button type="button" class="btn btn-sm btn-success w-100 text-start" onclick="createGroup({{ $task->id }})">
                                                <i class="bi bi-plus-lg"></i> Buat Kelompok
                                            </button>
                                            <button type="button" class="btn btn-sm btn-info w-100 text-start" onclick="listGroups({{ $task->id }})">
                                                <i class="bi bi-list"></i> Daftar Kelompok
                                            </button>
                                        </div>
                                        @else
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-info" onclick="viewTask({{ $task->id }})">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-primary" onclick="editTask({{ $task->id }})">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-success" onclick="viewSubmissions({{ $task->id }})">
                                                <i class="bi bi-file-earmark-check"></i> Submission
                                            </button>
                                        </div>
                                        @endif
                                    </td>
                                </tr>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <i class="bi bi-info-circle fs-3 text-muted"></i>
                                        <p class="mt-2">
                                            {{ request('type') == 'group' ? 'Belum ada tugas kelompok' : 'Belum ada tugas' }}
                                        </p>
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

    <!-- Modal Buat Kelompok -->
    <div class="modal fade" id="createGroupModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Buat Kelompok Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="createGroupForm">
                        <input type="hidden" id="taskIdInput" name="task_id">
                        <div class="mb-3">
                            <label class="form-label">Nama Kelompok</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea class="form-control" name="description" rows="2"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Maksimal Anggota</label>
                            <input type="number" class="form-control" name="max_members" min="2" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Pilih Anggota Kelompok</label>
                            <div id="availableStudentsList" class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                                <!-- Akan diisi melalui AJAX -->
                                <div class="text-center">
                                    <div class="spinner-border spinner-border-sm" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <span class="ms-2">Memuat daftar siswa...</span>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" onclick="submitCreateGroup()">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Daftar Kelompok -->
    <div class="modal fade" id="listGroupsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Daftar Kelompok</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="groupsList">
                        <!-- Akan diisi melalui AJAX -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Assign Siswa -->
    <div class="modal fade" id="assignMembersModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Assign Siswa ke Kelompok</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Pilih Kelompok</h6>
                            <select class="form-select mb-3" id="groupSelect">
                                <option value="">Pilih Kelompok...</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <h6>Pilih Siswa</h6>
                            <div class="border rounded p-3" style="max-height: 300px; overflow-y: auto;">
                                <div id="studentsList">
                                    <!-- Akan diisi melalui AJAX -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary" onclick="submitAssignMembers()">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Submission -->
    <div class="modal fade" id="submissionModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Daftar Submission</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Siswa</th>
                                    <th>Waktu Pengumpulan</th>
                                    <th>Status</th>
                                    <th>Nilai</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="submissionList">
                                <tr>
                                    <td colspan="5" class="text-center">Memuat data...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Nilai Submission -->
    <div class="modal fade" id="gradeSubmissionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Nilai Submission</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="gradeSubmissionForm">
                        <input type="hidden" id="submissionId" name="submission_id">
                        <div class="mb-3">
                            <label class="form-label">Siswa</label>
                            <input type="text" class="form-control" id="studentName" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">File Submission</label>
                            <div id="submissionFiles"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nilai</label>
                            <input type="number" class="form-control" name="score" min="0" max="100" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Komentar</label>
                            <textarea class="form-control" name="feedback" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary" onclick="submitGrade()">Simpan Nilai</button>
                </div>
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
                                    @if(request('type') == 'group')
                                    <div class="d-grid gap-1">
                                        <button type="button" class="btn btn-sm btn-success w-100 text-start" onclick="createGroup(${task.id})">
                                            <i class="bi bi-plus-lg"></i> Buat Kelompok
                                        </button>
                                        <button type="button" class="btn btn-sm btn-info w-100 text-start" onclick="listGroups(${task.id})">
                                            <i class="bi bi-list"></i> Daftar Kelompok
                                        </button>
                                    </div>
                                    @else
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-info" onclick="viewTask(${task.id})">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-primary" onclick="editTask(${task.id})">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-success" onclick="viewSubmissions(${task.id})">
                                            <i class="bi bi-file-earmark-check"></i> Submission
                                        </button>
                                    </div>
                                    @endif
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

    // Fungsi untuk mengedit tugas
    function editTask(taskId) {
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
                const task = response.data || response; // Menyesuaikan dengan format response API
                
                // Isi form edit dengan data yang ada
                $('#editTaskForm input[name="task_id"]').val(task.id);
                $('#editTaskForm input[name="title"]').val(task.title);
                $('#editTaskForm textarea[name="description"]').val(task.description);
                
                // Pastikan data tanggal ada sebelum menggunakan slice
                if (task.start_date) {
                    $('#editTaskForm input[name="start_date"]').val(task.start_date.slice(0, 16));
                }
                if (task.deadline) {
                    $('#editTaskForm input[name="deadline"]').val(task.deadline.slice(0, 16));
                }
                
                $('#editTaskForm select[name="task_type"]').val(task.task_type);
                $('#editTaskForm input[name="max_score"]').val(task.max_score);
                $('#editTaskForm input[name="weight_percentage"]').val(task.weight_percentage);
                $('#editTaskForm select[name="status"]').val(task.status);

                Swal.close();
                $('#editTaskModal').modal('show');
            },
            error: function(xhr) {
                console.error('Error:', xhr);
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
            method: 'PUT', // Ubah ke PUT untuk update
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#editTaskModal').modal('hide');
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'Tugas telah diperbarui',
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
                    text: 'Gagal memperbarui tugas: ' + (xhr.responseJSON?.message || 'Terjadi kesalahan'),
                    icon: 'error'
                });
            }
        });
    });

    function createGroup(taskId) {
        document.getElementById('taskIdInput').value = taskId;
        
        // Load daftar siswa yang belum masuk kelompok
        $.ajax({
            url: `/api/v1/teacher/classes/${classId}/students`,
            method: 'GET',
            data: { task_id: taskId }, // Tambahkan task_id ke request
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                let html = '';
                if (response.data && response.data.length > 0) {
                    response.data.forEach(student => {
                        html += `
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" 
                                       name="member_ids[]" value="${student.id}" 
                                       id="student${student.id}">
                                <label class="form-check-label" for="student${student.id}">
                                    ${student.name}
                                </label>
                            </div>
                        `;
                    });
                } else {
                    html = '<div class="alert alert-info mb-0">Tidak ada siswa yang tersedia untuk ditambahkan ke kelompok.</div>';
                }
                $('#availableStudentsList').html(html);
            },
            error: function(xhr) {
                console.error('Error:', xhr);
                $('#availableStudentsList').html(`
                    <div class="alert alert-danger mb-0">
                        Gagal memuat daftar siswa: ${xhr.responseJSON?.message || 'Terjadi kesalahan'}
                    </div>
                `);
            }
        });
        
        const modal = new bootstrap.Modal(document.getElementById('createGroupModal'));
        modal.show();
    }

    function listGroups(taskId) {
        const modal = new bootstrap.Modal(document.getElementById('listGroupsModal'));
        document.getElementById('groupsList').innerHTML = `
            <div class="text-center py-3">
                <div class="spinner-border spinner-border-sm" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <span class="ms-2">Memuat daftar kelompok...</span>
            </div>
        `;
        
        $.ajax({
            url: `/api/v1/teacher/classes/${classId}/tasks/${taskId}/groups`,
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                let html = '<div class="list-group">';
                if (response.data && response.data.length > 0) {
                    response.data.forEach(group => {
                        html += `
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="mb-0">${group.name}</h6>
                                    <span class="badge bg-primary">${group.members ? group.members.length : 0} Anggota</span>
                                </div>
                                <div class="mb-2">
                                    <strong class="small">Deskripsi:</strong>
                                    <p class="mb-1 text-muted small">${group.description || '-'}</p>
                                </div>
                                <div>
                                    <strong class="small">Anggota:</strong>
                                    <ul class="list-unstyled mb-0 mt-1">
                                        ${group.members ? group.members.map(member => `
                                            <li class="small">• ${member.name}</li>
                                        `).join('') : ''}
                                    </ul>
                                </div>
                            </div>
                        `;
                    });
                } else {
                    html = '<div class="alert alert-info">Belum ada kelompok yang dibuat</div>';
                }
                html += '</div>';
                document.getElementById('groupsList').innerHTML = html;
            },
            error: function(xhr) {
                console.error('Error:', xhr);
                document.getElementById('groupsList').innerHTML = `
                    <div class="alert alert-danger">
                        Gagal memuat data kelompok: ${xhr.responseJSON?.message || 'Terjadi kesalahan'}
                    </div>
                `;
            }
        });
        
        modal.show();
    }

    function assignMembers(taskId) {
        const modal = new bootstrap.Modal(document.getElementById('assignMembersModal'));
        
        // Load daftar kelompok untuk dropdown
        fetch(`/api/v1/tasks/${taskId}/groups`)
            .then(response => response.json())
            .then(data => {
                let options = '<option value="">Pilih Kelompok...</option>';
                if (data.groups) {
                    data.groups.forEach(group => {
                        options += `<option value="${group.id}">${group.name}</option>`;
                    });
                }
                document.getElementById('groupSelect').innerHTML = options;
            });
        
        // Load daftar siswa yang belum masuk kelompok
        fetch(`/api/v1/tasks/${taskId}/available-students`)
            .then(response => response.json())
            .then(data => {
                let html = '';
                if (data.students) {
                    data.students.forEach(student => {
                        html += `
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" 
                                       value="${student.id}" id="student${student.id}">
                                <label class="form-check-label" for="student${student.id}">
                                    ${student.name}
                                </label>
                            </div>
                        `;
                    });
                }
                document.getElementById('studentsList').innerHTML = html || 
                    '<p class="text-muted">Semua siswa sudah masuk kelompok</p>';
            });
        
        modal.show();
    }

    function submitCreateGroup() {
        const form = document.getElementById('createGroupForm');
        const taskId = document.getElementById('taskIdInput').value;
        
        // Kumpulkan data form termasuk member_ids dari checkbox yang dipilih
        const formData = new FormData(form);
        const selectedMembers = [];
        form.querySelectorAll('input[name="member_ids[]"]:checked').forEach(checkbox => {
            selectedMembers.push(checkbox.value);
        });
        
        // Validasi
        if (selectedMembers.length === 0) {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: 'Pilih minimal satu anggota kelompok'
            });
            return;
        }
        
        if (selectedMembers.length > formData.get('max_members')) {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: 'Jumlah anggota melebihi batas maksimal'
            });
            return;
        }

        // Loading state
        Swal.fire({
            title: 'Menyimpan...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Kirim data
        $.ajax({
            url: `/api/v1/teacher/classes/${classId}/tasks/${taskId}/groups`,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                name: formData.get('name'),
                description: formData.get('description'),
                max_members: formData.get('max_members'),
                member_ids: selectedMembers
            },
            success: function(response) {
                $('#createGroupModal').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Kelompok baru berhasil dibuat',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    window.location.reload();
                });
            },
            error: function(xhr) {
                console.error('Error:', xhr);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: xhr.responseJSON?.message || 'Terjadi kesalahan saat membuat kelompok'
                });
            }
        });
    }

    function submitAssignMembers() {
        // Implementasi assign siswa ke kelompok
    }

    document.addEventListener('DOMContentLoaded', function() {
        const token = localStorage.getItem('token');
        
        fetch('/api/v1/profile', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Authorization': `Bearer ${token}`
            },
            credentials: 'include'
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data && data.user && data.user.name) {
                // Update nama di dropdown
                document.getElementById('dropdownTeacherName').textContent = data.user.name;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('dropdownTeacherName').textContent = 'Guru';
        });
    });

    function viewSubmissions(taskId) {
        const modal = new bootstrap.Modal(document.getElementById('submissionModal'));
        
        $.ajax({
            url: `/api/v1/teacher/tasks/${taskId}/submissions`,
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                let html = '';
                if (response.data && response.data.length > 0) {
                    response.data.forEach(submission => {
                        const submissionDate = new Date(submission.submitted_at).toLocaleString('id-ID');
                        const statusBadge = getStatusBadge(submission.status);
                        
                        html += `
                            <tr>
                                <td>${submission.user.name}</td>
                                <td>${submissionDate}</td>
                                <td>${statusBadge}</td>
                                <td>${submission.score || '-'}</td>
                                <td>
                                    <div class="btn-group">
                                        <button class="btn btn-sm btn-info" onclick="viewSubmissionDetail(${submission.id})">
                                            <i class="bi bi-eye"></i> Detail
                                        </button>
                                        <button class="btn btn-sm btn-primary" onclick="gradeSubmission(${submission.id}, '${submission.user.name}')">
                                            <i class="bi bi-pencil-square"></i> Nilai
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        `;
                    });
                } else {
                    html = '<tr><td colspan="5" class="text-center">Belum ada submission</td></tr>';
                }
                $('#submissionList').html(html);
            },
            error: function(xhr) {
                $('#submissionList').html(`
                    <tr>
                        <td colspan="5" class="text-center text-danger">
                            Gagal memuat data: ${xhr.responseJSON?.message || 'Terjadi kesalahan'}
                        </td>
                    </tr>
                `);
            }
        });
        
        modal.show();
    }

    function getStatusBadge(status) {
        const badges = {
            'submitted': '<span class="badge bg-warning">Belum Dinilai</span>',
            'graded': '<span class="badge bg-success">Sudah Dinilai</span>',
            'revision_needed': '<span class="badge bg-danger">Perlu Revisi</span>'
        };
        return badges[status] || '<span class="badge bg-secondary">Unknown</span>';
    }

    function viewSubmissionDetail(submissionId) {
        $.ajax({
            url: `/api/v1/teacher/submissions/${submissionId}`,
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                const submission = response.data;
                Swal.fire({
                    title: 'Detail Submission',
                    html: `
                        <div class="text-start">
                            <p><strong>Siswa:</strong> ${submission.user.name}</p>
                            <p><strong>Waktu Pengumpulan:</strong> ${new Date(submission.submitted_at).toLocaleString('id-ID')}</p>
                            <p><strong>Status:</strong> ${getStatusBadge(submission.status)}</p>
                            <p><strong>Nilai:</strong> ${submission.score || '-'}</p>
                            <p><strong>Feedback:</strong> ${submission.feedback || '-'}</p>
                            ${submission.content ? `<p><strong>Konten:</strong><br>${submission.content}</p>` : ''}
                            ${submission.attachments && submission.attachments.length > 0 ? `
                                <p><strong>Lampiran:</strong></p>
                                <ul class="list-unstyled">
                                    ${submission.attachments.map(file => `
                                        <li><a href="/storage/${file.path}" target="_blank" class="btn btn-sm btn-outline-primary mb-1">
                                            <i class="bi bi-file-earmark"></i> ${file.original_name}
                                        </a></li>
                                    `).join('')}
                                </ul>
                            ` : ''}
                        </div>
                    `,
                    width: '600px'
                });
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: 'Gagal memuat detail submission: ' + (xhr.responseJSON?.message || 'Terjadi kesalahan')
                });
            }
        });
    }

    function gradeSubmission(submissionId, studentName) {
        const modal = new bootstrap.Modal(document.getElementById('gradeSubmissionModal'));
        
        $('#gradeSubmissionForm')[0].reset();
        $('#submissionId').val(submissionId);
        $('#studentName').val(studentName);
        
        $.ajax({
            url: `/api/v1/teacher/submissions/${submissionId}`,
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                const submission = response.data;
                
                // Tampilkan file submission jika ada
                let filesHtml = '';
                if (submission.attachments && submission.attachments.length > 0) {
                    filesHtml = submission.attachments.map(file => `
                        <div class="mb-2">
                            <a href="/storage/${file.path}" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-file-earmark"></i> ${file.original_name}
                            </a>
                        </div>
                    `).join('');
                } else {
                    filesHtml = '<p class="text-muted mb-0">Tidak ada file</p>';
                }
                $('#submissionFiles').html(filesHtml);
                
                // Isi nilai dan feedback jika sudah ada
                if (submission.score) {
                    $('input[name="score"]').val(submission.score);
                }
                if (submission.feedback) {
                    $('textarea[name="feedback"]').val(submission.feedback);
                }
            }
        });
        
        modal.show();
    }

    function submitGrade() {
        const submissionId = $('#submissionId').val();
        const formData = new FormData($('#gradeSubmissionForm')[0]);
        formData.append('status', 'graded'); // Sesuai dengan validasi di controller
        
        $.ajax({
            url: `/api/v1/teacher/submissions/${submissionId}/grade`,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#gradeSubmissionModal').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: response.message || 'Nilai berhasil disimpan',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    // Refresh daftar submission
                    viewSubmissions(response.data.task_id);
                });
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: xhr.responseJSON?.message || 'Gagal menyimpan nilai'
                });
            }
        });
    }
    </script>
</body>
</html> 