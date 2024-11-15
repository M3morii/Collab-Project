<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Daftar Kelas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/animate.css@4.1.1/animate.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        
        .page-header {
            background: linear-gradient(135deg, #0d6efd, #0dcaf0);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 0 15px rgba(0,0,0,0.05);
            transition: transform 0.2s;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .table {
            vertical-align: middle;
        }

        .table thead th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
            color: #6c757d;
            font-weight: 600;
        }

        .btn {
            border-radius: 8px;
            padding: 8px 16px;
            transition: all 0.2s;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .btn-sm {
            padding: 5px 10px;
        }

        .badge {
            padding: 6px 12px;
            border-radius: 6px;
            font-weight: 500;
        }

        .modal-content {
            border-radius: 15px;
            border: none;
        }

        .modal-header {
            background: linear-gradient(135deg, #0d6efd, #0dcaf0);
            color: white;
            border-radius: 15px 15px 0 0;
        }

        .modal-header .btn-close {
            color: white;
        }

        .form-control, .form-select {
            border-radius: 8px;
            padding: 10px 15px;
            border: 1px solid #dee2e6;
        }

        .form-control:focus, .form-select:focus {
            box-shadow: 0 0 0 0.25rem rgba(13,110,253,0.15);
        }

        .table-responsive {
            border-radius: 15px;
        }

        .loading-spinner {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1000;
        }

        /* Animasi untuk konten */
        .animate__animated {
            animation-duration: 0.8s;
        }

        /* Animasi untuk cards statistik */
        .row .card {
            opacity: 0;
            animation: slideIn 0.5s ease forwards;
        }

        .row .card:nth-child(1) { animation-delay: 0.2s; }
        .row .card:nth-child(2) { animation-delay: 0.4s; }
        .row .card:nth-child(3) { animation-delay: 0.6s; }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Animasi untuk tabel */
        .card .table-responsive {
            opacity: 0;
            animation: fadeIn 0.5s ease forwards;
            animation-delay: 0.8s;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        /* Animasi untuk tombol */
        .btn {
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        /* Animasi loading page */
        .page-transition {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255,255,255,0.9);
            z-index: 9999;
            display: flex;
            justify-content: center;
            align-items: center;
            animation: fadeOut 0.5s ease forwards;
            animation-delay: 0.5s;
        }

        @keyframes fadeOut {
            from {
                opacity: 1;
            }
            to {
                opacity: 0;
                visibility: hidden;
            }
        }
    </style>
</head>
<body>
    <!-- Loading Spinner -->
    <div class="loading-spinner">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <!-- Header Section dengan animasi -->
    <div class="page-header animate__animated animate__fadeIn">
        <div class="container">
            <h2 class="text-center mb-0">Daftar Kelas</h2>
        </div>
    </div>

    <!-- Main Content dengan animasi -->
    <div class="container py-4 animate__animated animate__fadeInUp">
        <div class="d-flex gap-2 mb-4">
            <a href="/admin/dashboard" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
            </a>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#classModal">
                <i class="bi bi-plus-lg"></i> Tambah Kelas
            </button>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Kelas</h5>
                        <h3 class="mb-0" id="totalClasses">0</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5 class="card-title">Kelas Aktif</h5>
                        <h3 class="mb-0" id="activeClasses">0</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <h5 class="card-title">Kelas Nonaktif</h5>
                        <h3 class="mb-0" id="inactiveClasses">0</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Card -->
        <div class="card">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0">Data Kelas</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Kelas</th>
                                <th>Deskripsi</th>
                                <th>KKM</th>
                                <th>Tahun Akademik</th>
                                <th>Semester</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="classTableBody">
                            <!-- Data akan diisi melalui JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah/Edit Kelas -->
    <div class="modal fade" id="classModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="classModalTitle">Tambah Kelas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="classForm">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Nama Kelas</label>
                            <input type="text" class="form-control" id="className" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="classDescription" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nilai KKM</label>
                            <input type="number" class="form-control" id="classKkm" min="0" max="100" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tahun Akademik</label>
                                <input type="text" class="form-control" id="classYear" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Semester</label>
                                <select class="form-select" id="classSemester" required>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" id="classStatus" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Tambahkan modal untuk assign guru dan siswa -->
    <div class="modal fade" id="assignModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="assignModalTitle">Assign Guru & Siswa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="assignForm">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Guru</label>
                            <select class="form-select" id="teacherSelect">
                                <option value="">Pilih Guru</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Siswa</label>
                            <select class="form-select" id="studentSelect" multiple>
                                <!-- Options akan diisi melalui JavaScript -->
                            </select>
                            <small class="text-muted">Tahan Ctrl/Cmd untuk memilih beberapa siswa</small>
                        </div>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Fungsi untuk mendapatkan token
        function getToken() {
            const token = localStorage.getItem('token');
            if (!token) {
                window.location.href = '/login';
                return null;
            }
            return token;
        }

        // Fungsi untuk mengambil data kelas
        async function fetchClasses() {
            const spinner = document.querySelector('.loading-spinner');
            spinner.style.display = 'block';
            
            const token = getToken();
            if (!token) return;

            try {
                const response = await fetch('/api/v1/admin/classes', {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                if (response.ok) {
                    const result = await response.json();
                    displayClasses(result.data);
                    updateStatistics(result.data);
                } else if (response.status === 401) {
                    // Token tidak valid
                    localStorage.removeItem('token');
                    window.location.href = '/login';
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Gagal mengambil data kelas'
                    });
                }
            } catch (error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Terjadi kesalahan saat mengambil data'
                });
            } finally {
                spinner.style.display = 'none';
            }
        }

        // Update fungsi submit form untuk menggunakan token yang sama
        document.getElementById('classForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const token = getToken();
            if (!token) return;

            const classId = this.dataset.classId;
            const data = {
                name: document.getElementById('className').value,
                description: document.getElementById('classDescription').value,
                kkm_score: parseInt(document.getElementById('classKkm').value),
                academic_year: document.getElementById('classYear').value,
                semester: document.getElementById('classSemester').value,
                status: document.getElementById('classStatus').value
            };

            try {
                const url = classId 
                    ? `/api/v1/admin/classes/${classId}`
                    : '/api/v1/admin/classes';
                
                const response = await fetch(url, {
                    method: classId ? 'PUT' : 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(data)
                });

                if (response.ok) {
                    const result = await response.json();
                    const modal = bootstrap.Modal.getInstance(document.getElementById('classModal'));
                    modal.hide();
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: classId ? 'Kelas berhasil diupdate' : 'Kelas berhasil ditambahkan',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    
                    fetchClasses();
                } else if (response.status === 401) {
                    localStorage.removeItem('token');
                    window.location.href = '/login';
                } else {
                    const errorData = await response.json();
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: errorData.message || 'Terjadi kesalahan'
                    });
                }
            } catch (error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Terjadi kesalahan saat menyimpan kelas'
                });
            }
        });

        // Fungsi untuk menampilkan data ke tabel
        function displayClasses(classes) {
            const tableBody = document.getElementById('classTableBody');
            tableBody.innerHTML = '';

            classes.forEach((kelas, index) => {
                const kkmScore = kelas.kkm_score ?? '-';
                const academicYear = kelas.academic_year ?? '-';
                const semester = kelas.semester ?? '-';
                
                tableBody.innerHTML += `
                    <tr class="animate__animated animate__fadeIn">
                        <td>${index + 1}</td>
                        <td>${kelas.name}</td>
                        <td>${kelas.description}</td>
                        <td>${kkmScore}</td>
                        <td>${academicYear}</td>
                        <td>${semester}</td>
                        <td>
                            <span class="badge bg-${kelas.status === 'active' ? 'success' : 'danger'}">
                                ${kelas.status}
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-warning" onclick="editClass(${kelas.id})">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-info" onclick="detailClass(${kelas.id})">
                                <i class="bi bi-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-primary" onclick="showAssignModal(${kelas.id})">
                                <i class="bi bi-people"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });
        }

        // Fungsi untuk melihat detail kelas
        async function detailClass(id) {
            const token = getToken();
            if (!token) return;

            try {
                const response = await fetch(`/api/v1/admin/classes/${id}`, {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });

                if (response.ok) {
                    const result = await response.json();
                    const data = result.data;
                    
                    // Format daftar siswa
                    const studentsList = data.students && data.students.length > 0
                        ? data.students.map(student => `<li>${student.name}</li>`).join('')
                        : '<li>Belum ada siswa</li>';

                    Swal.fire({
                        title: data.name,
                        html: `
                            <div class="text-start">
                                <p><strong>Deskripsi:</strong> ${data.description}</p>
                                <p><strong>KKM:</strong> ${data.kkm_score}</p>
                                <p><strong>Tahun Akademik:</strong> ${data.academic_year}</p>
                                <p><strong>Semester:</strong> ${data.semester}</p>
                                <p><strong>Status:</strong> ${data.status}</p>
                                <p><strong>Guru:</strong> ${data.teacher ? data.teacher.name : 'Belum ditentukan'}</p>
                                <p><strong>Daftar Siswa:</strong></p>
                                <ul class="text-start">
                                    ${studentsList}
                                </ul>
                            </div>
                        `,
                        width: '600px'
                    });
                } else {
                    throw new Error('Gagal mengambil detail kelas');
                }
            } catch (error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Terjadi kesalahan saat mengambil detail kelas'
                });
            }
        }

        // Fungsi untuk edit kelas
        async function editClass(id) {
            const token = getToken();
            if (!token) return;

            try {
                const response = await fetch(`/api/v1/admin/classes/${id}`, {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });

                if (response.ok) {
                    const result = await response.json();
                    const data = result.data;
                    
                    // Isi form dengan data yang ada
                    document.getElementById('className').value = data.name;
                    document.getElementById('classDescription').value = data.description;
                    document.getElementById('classKkm').value = data.kkm_score;
                    document.getElementById('classYear').value = data.academic_year;
                    document.getElementById('classSemester').value = data.semester;
                    document.getElementById('classStatus').value = data.status;

                    // Simpan ID kelas yang sedang diedit
                    document.getElementById('classForm').dataset.classId = id;

                    // Ubah judul modal
                    document.getElementById('classModalTitle').textContent = 'Edit Kelas';

                    // Tampilkan modal
                    const modal = new bootstrap.Modal(document.getElementById('classModal'));
                    modal.show();
                } else if (response.status === 401) {
                    localStorage.removeItem('token');
                    window.location.href = '/login';
                } else {
                    throw new Error('Gagal mengambil data kelas');
                }
            } catch (error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Terjadi kesalahan saat mengambil data kelas'
                });
            }
        }

        // Tambahkan event listener untuk reset form saat modal ditutup
        document.getElementById('classModal').addEventListener('hidden.bs.modal', function () {
            document.getElementById('classForm').reset();
            document.getElementById('classForm').dataset.classId;
            document.getElementById('classModalTitle').textContent = 'Tambah Kelas';
        });

        // Load data kelas saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            // Inisialisasi event listener untuk modal
            const classModal = document.getElementById('classModal');
            if (classModal) {
                classModal.addEventListener('hidden.bs.modal', function () {
                    const classForm = document.getElementById('classForm');
                    if (classForm) {
                        classForm.reset();
                        delete classForm.dataset.classId;
                    }
                    const classModalTitle = document.getElementById('classModalTitle');
                    if (classModalTitle) {
                        classModalTitle.textContent = 'Tambah Kelas';
                    }
                });
            }

            // Panggil fetchClasses
            fetchClasses();

            // Animasi untuk cards statistik
            const cards = document.querySelectorAll('.card');
            cards.forEach((card, index) => {
                card.style.animationDelay = `${0.2 * (index + 1)}s`;
            });

            // Hapus page transition setelah konten dimuat
            setTimeout(() => {
                const transition = document.querySelector('.page-transition');
                if (transition) {
                    transition.remove();
                }
            }, 1000);
        });

        // Update statistics
        function updateStatistics(classes) {
            const totalClasses = classes.length;
            const activeClasses = classes.filter(c => c.status === 'active').length;
            const inactiveClasses = classes.filter(c => c.status === 'inactive').length;

            document.getElementById('totalClasses').textContent = totalClasses;
            document.getElementById('activeClasses').textContent = activeClasses;
            document.getElementById('inactiveClasses').textContent = inactiveClasses;
        }

        // Add smooth scrolling
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });

        // Tambahkan ini untuk animasi saat klik tombol kembali
        document.querySelector('a[href="/admin/dashboard"]').addEventListener('click', function(e) {
            e.preventDefault();
            const container = document.querySelector('.container');
            container.classList.add('animate__fadeOutDown');
            
            setTimeout(() => {
                window.location.href = this.getAttribute('href');
            }, 500);
        });

        // Fungsi untuk menampilkan modal assign
        async function showAssignModal(classId) {
            const token = getToken();
            if (!token) return;

            try {
                // Ambil data guru
                const teacherResponse = await fetch('/api/v1/admin/users?role=teacher', {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });

                // Ambil data siswa
                const studentResponse = await fetch('/api/v1/admin/users?role=student', {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });

                // Ambil data kelas
                const classResponse = await fetch(`/api/v1/admin/classes/${classId}`, {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });

                if (!teacherResponse.ok || !studentResponse.ok || !classResponse.ok) {
                    throw new Error('Gagal mengambil data');
                }

                const teachers = await teacherResponse.json();
                const students = await studentResponse.json();
                const classData = await classResponse.json();

                // Isi select guru
                const teacherSelect = document.getElementById('teacherSelect');
                teacherSelect.innerHTML = '<option value="">Pilih Guru</option>';
                teachers.data.forEach(teacher => {
                    const option = document.createElement('option');
                    option.value = teacher.id;
                    option.textContent = teacher.name;
                    if (classData.data.teacher_id === teacher.id) {
                        option.selected = true;
                    }
                    teacherSelect.appendChild(option);
                });

                // Isi select siswa
                const studentSelect = document.getElementById('studentSelect');
                studentSelect.innerHTML = '';
                students.data.forEach(student => {
                    const option = document.createElement('option');
                    option.value = student.id;
                    option.textContent = student.name;
                    if (classData.data.students.some(s => s.id === student.id)) {
                        option.selected = true;
                    }
                    studentSelect.appendChild(option);
                });

                // Simpan class ID ke form
                document.getElementById('assignForm').dataset.classId = classId;

                // Tampilkan modal
                const modal = new bootstrap.Modal(document.getElementById('assignModal'));
                modal.show();

            } catch (error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Terjadi kesalahan saat mengambil data'
                });
            }
        }

        // Event listener untuk form assign
        document.getElementById('assignForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const token = getToken();
            if (!token) return;

            const classId = this.dataset.classId;
            const teacherId = document.getElementById('teacherSelect').value;
            const selectedStudents = Array.from(document.getElementById('studentSelect').selectedOptions)
                .map(option => option.value);

            try {
                // Assign guru
                if (teacherId) {
                    await fetch(`/api/v1/admin/classes/${classId}/assign-teacher`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Authorization': `Bearer ${token}`,
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ teacher_id: teacherId })
                    });
                }

                // Assign siswa
                if (selectedStudents.length > 0) {
                    await fetch(`/api/v1/admin/classes/${classId}/assign-students`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Authorization': `Bearer ${token}`,
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ student_ids: selectedStudents })
                    });
                }

                // Tutup modal dan refresh data
                const modal = bootstrap.Modal.getInstance(document.getElementById('assignModal'));
                modal.hide();
                
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Guru dan siswa berhasil ditambahkan ke kelas',
                    timer: 1500,
                    showConfirmButton: false
                });
                
                fetchClasses();

            } catch (error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Terjadi kesalahan saat menyimpan data'
                });
            }
        });
    </script>
</body>
</html> 