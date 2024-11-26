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
                            <i class="bi bi-person-circle"></i> <span id="dropdownTeacherName">Loading...</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                                    Edit Profil
                                </a>
                            </li>
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
                <h5 class="card-title">Selamat Pagi <span id="teacherName">Loading...</span>!</h5>
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
                                            <a href="{{ route('teacher.tasks.index', $class->id) }}" 
                                               class="btn btn-primary">
                                                <i class="bi bi-list-task"></i> Manajemen Tugas
                                            </a>
                                            <button type="button" class="btn btn-success btn-sm" onclick="window.location.href='{{ route('teacher.tasks.index', $class->id) }}?type=group'">
                                                <i class="bi bi-people"></i> Manajemen Kelompok
                                            </button>
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

    <!-- Modal Edit Profil -->
    <div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProfileModalLabel">Edit Profil</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Alert untuk error messages -->
                    <div id="profileErrorMessages" class="alert alert-danger d-none">
                        <ul class="mb-0"></ul>
                    </div>

                    <form id="editProfileForm" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" id="editName" name="name" maxlength="255">
                        </div>
                        
                        <div class="mb-3">
                            <label for="phone" class="form-label">Nomor Telepon</label>
                            <input type="tel" class="form-control" id="editPhone" name="phone" maxlength="20">
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Alamat</label>
                            <textarea class="form-control" id="editAddress" name="address" maxlength="255" rows="3"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="avatar" class="form-label">Foto Profil</label>
                            <input type="file" class="form-control" id="editAvatar" name="avatar" accept="image/jpeg,image/png,image/jpg">
                            <small class="text-muted">Format: JPEG, PNG, JPG. Maksimal 2MB</small>
                        </div>

                        <div class="modal-footer px-0 pb-0">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary" id="submitProfileBtn">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Manajemen Kelompok -->
    <div class="modal fade" id="manageGroupsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Manajemen Kelompok</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Form Tambah Kelompok -->
                    <form id="createGroupForm" class="mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title">Tambah Kelompok Baru</h6>
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
                                    <label class="form-label">Anggota Kelompok</label>
                                    <select class="form-select" name="member_ids[]" multiple required>
                                        <!-- Akan diisi secara dinamis -->
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary">Tambah Kelompok</button>
                            </div>
                        </div>
                    </form>

                    <!-- Daftar Kelompok -->
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title">Daftar Kelompok</h6>
                            <div id="groupsList">
                                <!-- Akan diisi secara dinamis -->
                            </div>
                        </div>
                    </div>
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

        // Fungsi untuk mendapatkan salam berdasarkan waktu
        function getGreeting() {
            const hour = new Date().getHours();
            if (hour >= 3 && hour < 11) return "Selamat Pagi";
            if (hour >= 11 && hour < 15) return "Selamat Siang";
            if (hour >= 15 && hour < 18) return "Selamat Sore";
            return "Selamat Malam";
        }

        // Ambil data profil saat halaman dimuat
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
                    // Update nama di welcome message
                    document.getElementById('teacherName').textContent = data.user.name;
                    // Update nama di dropdown
                    document.getElementById('dropdownTeacherName').textContent = data.user.name;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('teacherName').textContent = 'Guru';
                document.getElementById('dropdownTeacherName').textContent = 'Guru';
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const token = localStorage.getItem('token');
            const modal = document.getElementById('editProfileModal');
            const form = document.getElementById('editProfileForm');
            const errorMessages = document.getElementById('profileErrorMessages');
            const errorList = errorMessages.querySelector('ul');
            const submitBtn = document.getElementById('submitProfileBtn');

            // Isi form saat modal dibuka
            modal.addEventListener('show.bs.modal', function() {
                fetch('/api/v1/profile', {
                    headers: {
                        'Accept': 'application/json',
                        'Authorization': `Bearer ${token}`
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.user) {
                        document.getElementById('editName').value = data.user.name || '';
                        document.getElementById('editPhone').value = data.user.phone || '';
                        document.getElementById('editAddress').value = data.user.address || '';
                    }
                })
                .catch(error => console.error('Error:', error));
            });

            // Reset form dan error messages saat modal ditutup
            modal.addEventListener('hidden.bs.modal', function() {
                form.reset();
                errorMessages.classList.add('d-none');
                errorList.innerHTML = '';
            });

            // Handle form submission
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                // Reset error messages
                errorMessages.classList.add('d-none');
                errorList.innerHTML = '';
                
                // Disable submit button dan tampilkan loading
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...';

                try {
                    const formData = new FormData(this);
                    
                    const response = await fetch('/api/v1/profile/update', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Authorization': `Bearer ${token}`
                        },
                        body: formData
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        throw data;
                    }

                    // Tutup modal
                    bootstrap.Modal.getInstance(modal).hide();
                    
                    // Update nama yang ditampilkan
                    if (data.user && data.user.name) {
                        document.getElementById('teacherName').textContent = data.user.name;
                        document.getElementById('dropdownTeacherName').textContent = data.user.name;
                    }

                    // Tampilkan pesan sukses
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message || 'Profil berhasil diperbarui'
                    }).then(() => {
                        // Reload halaman untuk memperbarui semua data
                        location.reload();
                    });

                } catch (error) {
                    errorMessages.classList.remove('d-none');
                    
                    if (error.errors) {
                        Object.values(error.errors).forEach(messages => {
                            messages.forEach(message => {
                                errorList.innerHTML += `<li>${message}</li>`;
                            });
                        });
                    } else if (error.message) {
                        errorList.innerHTML = `<li>${error.message}</li>`;
                    }
                } finally {
                    // Reset submit button
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = 'Simpan Perubahan';
                }
            });
        });

        // Fungsi untuk mengelola kelompok
        function manageGroups(taskId) {
            const modal = new bootstrap.Modal(document.getElementById('manageGroupsModal'));
            
            // Reset form dan daftar
            document.getElementById('createGroupForm').reset();
            document.getElementById('groupsList').innerHTML = 'Loading...';
            
            // Load daftar kelompok
            fetch(`/api/v1/teacher/classes/${classId}/tasks/${taskId}/groups`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                let groupsHtml = '';
                
                if (data.length === 0) {
                    groupsHtml = '<p class="text-muted">Belum ada kelompok</p>';
                } else {
                    groupsHtml = '<div class="list-group">';
                    data.forEach(group => {
                        groupsHtml += `
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-1">${group.name}</h6>
                                    <span class="badge bg-primary">${group.members.length}/${group.max_members} Anggota</span>
                                </div>
                                <p class="mb-1 text-muted small">${group.description || '-'}</p>
                                <div class="mt-2">
                                    <strong class="small">Anggota:</strong>
                                    <ul class="list-unstyled mb-0">
                                        ${group.members.map(member => `
                                            <li class="small">• ${member.name}</li>
                                        `).join('')}
                                    </ul>
                                </div>
                            </div>
                        `;
                    });
                    groupsHtml += '</div>';
                }
                
                document.getElementById('groupsList').innerHTML = groupsHtml;
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('groupsList').innerHTML = 
                    '<div class="alert alert-danger">Gagal memuat daftar kelompok</div>';
            });
            
            // Handle form submission
            document.getElementById('createGroupForm').onsubmit = function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                
                fetch(`/api/v1/teacher/classes/${classId}/tasks/${taskId}/groups`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.errors) {
                        throw new Error(Object.values(data.errors).flat().join('\n'));
                    }
                    
                    // Refresh daftar kelompok
                    manageGroups(taskId);
                    
                    // Reset form
                    this.reset();
                    
                    // Tampilkan pesan sukses
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Kelompok baru telah ditambahkan',
                        timer: 1500,
                        showConfirmButton: false
                    });
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: error.message || 'Gagal menambahkan kelompok'
                    });
                });
            };
            
            modal.show();
        }
    </script>
</body>
</html> 