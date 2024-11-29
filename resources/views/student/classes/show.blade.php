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
            <a class="navbar-brand" href="/student/dashboard">
                <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#"><i class="bi bi-book"></i> Materi</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="bi bi-journal-text"></i> Tugas</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container py-4">
        <!-- Header Kelas -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="card-title mb-1">{{ $class->name }}</h4>
                                <p class="text-muted mb-0">
                                    <i class="bi bi-person-circle"></i> {{ $class->teacher->name }}
                                </p>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-success" id="classStatus">Active</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Daftar Tugas -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Daftar Tugas</h5>
                        <div id="tasksList">
                            <div class="text-center">
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

    <!-- Tambahkan modal untuk detail tugas -->
    <div class="modal fade" id="taskDetailModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Tugas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="fw-bold">Judul:</label>
                        <p id="modalTaskTitle">Loading...</p>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Deskripsi:</label>
                        <p id="modalTaskDescription">Loading...</p>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="fw-bold">Deadline:</label>
                                <p id="modalTaskDeadline">Loading...</p>
                            </div>
                            <div class="mb-3">
                                <label class="fw-bold">Tipe Tugas:</label>
                                <p id="modalTaskType">Loading...</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="fw-bold">Status:</label>
                                <p id="modalTaskStatus">Loading...</p>
                            </div>
                            <div class="mb-3">
                                <label class="fw-bold">Nilai Maksimal:</label>
                                <p id="modalTaskMaxScore">Loading...</p>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Lampiran:</label>
                        <div id="modalTaskAttachments">Loading...</div>
                    </div>
                    
                    <div class="mt-4">
                        <h6 class="mb-3">Pengumpulan Tugas</h6>
                        <div id="submissionStatus">
                            <!-- Status akan diisi melalui JavaScript -->
                        </div>
                        
                        <form id="submitTaskForm" enctype="multipart/form-data" class="mt-3">
                            <input type="hidden" id="taskIdInput" name="task_id">
                            <div class="mb-3">
                                <label class="form-label">File Tugas</label>
                                <input type="file" class="form-control" name="submission_files[]" multiple required>
                                <small class="text-muted">Anda dapat memilih lebih dari satu file</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Catatan (opsional)</label>
                                <textarea class="form-control" name="notes" rows="3"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Kumpulkan</button>
                        </form>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Tambahkan modal baru untuk nilai -->
    <div class="modal fade" id="gradeModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Nilai Tugas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="gradeModalContent">
                        <div class="text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Fungsi untuk melihat detail tugas
        function viewTaskDetail(taskId) {
            currentTaskId = taskId; // Simpan taskId ke variabel global
            console.log('Viewing task ID:', taskId); // Debug
            
            fetch(`/api/v1/student/tasks/${taskId}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                console.log('Task detail:', data);
                const taskData = data.data;
                
                // Update semua informasi tugas
                document.getElementById('modalTaskTitle').textContent = taskData.title;
                document.getElementById('modalTaskDescription').textContent = taskData.description;
                document.getElementById('modalTaskDeadline').textContent = new Date(taskData.deadline).toLocaleDateString('id-ID');
                document.getElementById('modalTaskType').textContent = taskData.task_type === 'individual' ? 'Individu' : 'Kelompok';
                document.getElementById('modalTaskMaxScore').textContent = taskData.max_score || '-';
                
                // Update status pengumpulan
                const statusElement = document.getElementById('modalTaskStatus');
                if (taskData.submissions && taskData.submissions.length > 0) {
                    statusElement.innerHTML = '<span class="badge bg-success">Sudah Dikumpulkan</span>';
                } else {
                    statusElement.innerHTML = '<span class="badge bg-warning">Belum Dikumpulkan</span>';
                }
                
                // Update lampiran
                const attachmentsElement = document.getElementById('modalTaskAttachments');
                if (taskData.attachments && taskData.attachments.length > 0) {
                    let attachmentsHtml = '<div class="list-group">';
                    taskData.attachments.forEach(attachment => {
                        attachmentsHtml += `
                            <a href="/api/v1/student/tasks/${taskId}/attachments/${attachment.id}/download" 
                               class="list-group-item list-group-item-action">
                                <i class="bi bi-file-earmark"></i> ${attachment.file_name}
                            </a>
                        `;
                    });
                    attachmentsHtml += '</div>';
                    attachmentsElement.innerHTML = attachmentsHtml;
                } else {
                    attachmentsElement.innerHTML = '<p class="text-muted">Tidak ada lampiran</p>';
                }
                
                // Set task ID ke form
                document.getElementById('taskIdInput').value = taskId;
                
                // Show modal
                const modal = new bootstrap.Modal(document.getElementById('taskDetailModal'));
                modal.show();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Gagal memuat detail tugas: ' + error.message);
            });
        }

        // Fungsi untuk memuat detail kelas
        function getClassDetail() {
            const classId = {{ $class->id }};
            
            fetch(`/api/v1/student/classes/${classId}/tasks`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                credentials: 'include'
            })
            .then(response => response.json())
            .then(data => {
                // Update informasi kelas
                document.getElementById('className').textContent = data.data.class;
                document.getElementById('teacherName').innerHTML = `
                    <i class="bi bi-person-circle"></i> ${data.data.teacher}
                `;
                
                // Update daftar tugas
                getClassTasks();
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('className').textContent = 'Error loading class';
                document.getElementById('teacherName').innerHTML = `
                    <i class="bi bi-exclamation-triangle"></i> Failed to load teacher info
                `;
            });
        }

        // Fungsi untuk memuat daftar tugas
        function getClassTasks() {
            const classId = {{ $class->id }};
            
            fetch(`/api/v1/student/classes/${classId}/tasks`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
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
                console.log('Tasks data:', data);
                const tasksContainer = document.getElementById('tasksList');
                
                if (data.data.tasks && data.data.tasks.length > 0) {
                    let html = '<div class="list-group">';
                    data.data.tasks.forEach(task => {
                        html += `
                            <div class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">${task.title}</h6>
                                    <small class="text-muted">Deadline: ${new Date(task.deadline).toLocaleDateString('id-ID')}</small>
                                </div>
                                <p class="mb-1">${task.description}</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        Tipe: ${task.task_type === 'individual' ? 'Individu' : 'Kelompok'}
                                    </small>
                                    <div class="btn-group">
                                        <button class="btn btn-sm btn-primary" onclick="viewTaskDetail(${task.id})">
                                            Lihat Detail
                                        </button>
                                        <button class="btn btn-sm btn-info text-white" onclick="viewGrade(${task.id})">
                                            <i class="bi bi-award"></i> Lihat Nilai
                                        </button>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    html += '</div>';
                    tasksContainer.innerHTML = html;
                } else {
                    tasksContainer.innerHTML = `
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> Belum ada tugas untuk kelas ini.
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('tasksList').innerHTML = `
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle"></i> Gagal memuat daftar tugas: ${error.message}
                    </div>
                `;
            });
        }

        // Event listener untuk form submit
        document.getElementById('submitTaskForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const taskId = currentTaskId;
            console.log('Submitting task ID:', taskId); // Debug
            
            if (!taskId) {
                alert('Task ID tidak valid');
                return;
            }
            
            const formData = new FormData(this);
            
            // Rename file input dari submission_files menjadi attachments
            const files = formData.getAll('submission_files[]');
            formData.delete('submission_files[]');
            files.forEach(file => {
                formData.append('attachments[]', file);
            });
            
            // Rename notes menjadi description
            const notes = formData.get('notes');
            formData.delete('notes');
            formData.append('description', notes);
            
            // Debug: cek isi FormData
            for (let pair of formData.entries()) {
                console.log(pair[0] + ': ' + pair[1]);
            }
            
            // Tampilkan loading state
            const submitButton = this.querySelector('button[type="submit"]');
            const originalText = submitButton.innerHTML;
            submitButton.disabled = true;
            submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Mengirim...';
            
            fetch(`/api/v1/student/tasks/${taskId}/submissions`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => {
                        throw new Error(err.message || 'Gagal mengumpulkan tugas');
                    });
                }
                return response.json();
            })
            .then(data => {
                console.log('Submit response:', data); // Debug
                alert('Tugas berhasil dikumpulkan!');
                bootstrap.Modal.getInstance(document.getElementById('submitTaskModal')).hide();
                viewTaskDetail(taskId); // Refresh detail tugas
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Gagal mengumpulkan tugas: ' + error.message);
            })
            .finally(() => {
                submitButton.disabled = false;
                submitButton.innerHTML = originalText;
            });
        });

        // Panggil fungsi saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            getClassTasks();
        });

        // Fungsi untuk melihat nilai tugas
        function viewGrade(taskId) {
            const gradeModal = new bootstrap.Modal(document.getElementById('gradeModal'));
            gradeModal.show();
            
            fetch(`/api/v1/student/tasks/${taskId}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                const taskData = data.data;
                const gradeContent = document.getElementById('gradeModalContent');
                
                if (taskData.submissions && taskData.submissions.length > 0) {
                    const submission = taskData.submissions[0];
                    if (submission.grade !== null) {
                        gradeContent.innerHTML = `
                            <div class="text-center mb-4">
                                <div class="display-4 text-primary mb-2">${submission.grade}/${taskData.max_score}</div>
                                <p class="text-muted">Dinilai pada: ${new Date(submission.graded_at).toLocaleDateString('id-ID')}</p>
                            </div>
                            ${submission.feedback ? `
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title">Feedback Guru:</h6>
                                        <p class="card-text">${submission.feedback}</p>
                                    </div>
                                </div>
                            ` : ''}
                        `;
                    } else {
                        gradeContent.innerHTML = `
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i> Tugas belum dinilai oleh guru
                            </div>
                        `;
                    }
                } else {
                    gradeContent.innerHTML = `
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i> Anda belum mengumpulkan tugas ini
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('gradeModalContent').innerHTML = `
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle"></i> Gagal memuat nilai: ${error.message}
                    </div>
                `;
            });
        }
    </script>
</body>
</html> 