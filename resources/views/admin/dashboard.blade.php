<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/animate.css@4.1.1/animate.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #858796;
            --success-color: #1cc88a;
            --info-color: #36b9cc;
            --warning-color: #f6c23e;
            --danger-color: #e74a3b;
        }

        body {
            background-color: #f8f9fc;
            font-family: 'Nunito', sans-serif;
        }

        .navbar {
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .navbar-brand {
            font-size: 1.5rem;
            color: var(--primary-color) !important;
        }

        .nav-link {
            position: relative;
            padding: 0.5rem 1rem;
            transition: all 0.3s ease;
        }

        .nav-link:after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 50%;
            background-color: var(--primary-color);
            transition: all 0.3s ease;
        }

        .nav-link:hover:after {
            width: 100%;
            left: 0;
        }

        .nav-link.active:after {
            width: 100%;
            left: 0;
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card-title {
            color: var(--secondary-color);
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
        }

        .card h2 {
            color: var(--primary-color);
            font-weight: 700;
        }

        .btn {
            border-radius: 10px;
            padding: 0.5rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .table {
            border-radius: 15px;
            overflow: hidden;
        }

        .table thead th {
            background-color: #f8f9fc;
            border-bottom: 2px solid #e3e6f0;
            color: var(--secondary-color);
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.8rem;
        }

        .badge {
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-weight: 600;
        }

        /* Dashboard Cards Animation */
        .animate__animated {
            animation-duration: 1s;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--primary-color);
            border-radius: 10px;
        }

        /* Loading Animation */
        .loading {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255,255,255,0.9);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 5px solid #f3f3f3;
            border-top: 5px solid var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .navbar-brand {
                font-size: 1.2rem;
            }

            .card {
                margin-bottom: 1rem;
            }
        }

        .navbar {
            padding: 1rem 0;
            position: relative;
        }

        .navbar-brand {
            font-size: 1.8rem;
            color: var(--primary-color) !important;
            margin-bottom: 1rem;
            width: 100%;
            text-align: center;
        }

        .navbar .navbar-nav {
            border-top: 1px solid #eee;
            padding-top: 0.8rem;
        }

        .nav-link {
            font-size: 1rem;
            padding: 0.5rem 1.5rem;
            color: var(--secondary-color);
            transition: all 0.3s ease;
        }

        .nav-link:hover {
            color: var(--primary-color);
        }

        .nav-link.active {
            color: var(--primary-color);
            font-weight: 600;
        }

        @media (max-width: 991.98px) {
            .navbar-collapse {
                width: 100%;
                text-align: center;
            }
            
            .navbar .navbar-nav {
                border-top: 1px solid #eee;
                padding-top: 1rem;
            }
            
            .navbar-toggler {
                position: absolute !important;
                top: 1rem !important;
                right: 1rem !important;
            }
            
            .position-absolute.top-0.end-0 {
                position: relative !important;
                margin-top: 1rem !important;
                text-align: center;
                width: 100%;
            }
            
            .btn-outline-danger {
                width: 100%;
                margin-top: 1rem;
            }
        }

        @media (min-width: 992px) {
            .navbar .container {
                position: relative;
            }
            
            .navbar-collapse {
                width: 100%;
                justify-content: center;
            }
            
            .navbar-nav {
                display: flex;
                justify-content: center;
                width: auto;
            }
            
            .position-absolute.top-0.end-0 {
                z-index: 1030;
            }
        }

        /* Animasi untuk navbar */
        .navbar-brand {
            animation: fadeInDown 0.5s ease-out;
        }

        .nav-link {
            animation: fadeInUp 0.5s ease-out;
            animation-fill-mode: both;
        }

        .nav-item:nth-child(1) .nav-link { animation-delay: 0.1s; }
        .nav-item:nth-child(2) .nav-link { animation-delay: 0.2s; }
        .nav-item:nth-child(3) .nav-link { animation-delay: 0.3s; }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <!-- Loading Animation -->
    <div class="loading" id="loading">
        <div class="loading-spinner"></div>
    </div>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom sticky-top">
        <div class="container">
            <div class="d-flex flex-column align-items-center w-100">
                <a class="navbar-brand fw-bold mb-3 text-center" href="#">Dashboard Admin</a>
                <button class="navbar-toggler position-absolute top-0 end-0 mt-2 me-2" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link active" href="#" onclick="showOverview()">Overview</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" onclick="showUsers()">Users</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.classes.index') }}">Classes</a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="position-absolute top-0 end-0 mt-3 me-3">
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Modals -->
    @include('admin.partials.class-modal')

    <!-- Main Content -->
    <main class="container py-4">
        <!-- Overview Section -->
        <div id="overviewSection">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card animate__animated animate__fadeInUp">
                        <div class="card-body d-flex align-items-center">
                            <div class="mr-3">
                                <i class="bi bi-people-fill text-primary" style="font-size: 2rem;"></i>
                            </div>
                            <div class="ml-3">
                                <h5 class="card-title mb-1">Total Users</h5>
                                <h2 id="totalUsers" class="mb-0">Loading...</h2>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card animate__animated animate__fadeInUp" style="animation-delay: 0.2s">
                        <div class="card-body d-flex align-items-center">
                            <div class="mr-3">
                                <i class="bi bi-book-fill text-success" style="font-size: 2rem;"></i>
                            </div>
                            <div class="ml-3">
                                <h5 class="card-title mb-1">Active Classes</h5>
                                <h2 id="totalClasses" class="mb-0">Loading...</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Users Section -->
        <div id="usersSection" class="d-none">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>User Management</h2>
                <button class="btn btn-primary" onclick="showAddUserModal()">
                    <i class="bi bi-plus-lg"></i> Add User
                </button>
            </div>
            
            <div class="card">
                <div class="card-body">
                    <div class="mb-3">
                        <select class="form-select w-auto" id="roleFilter">
                            <option value="">All Roles</option>
                            <option value="teacher">Teachers</option>
                            <option value="student">Students</option>
                        </select>
                    </div>
                    <div class="table-responsive">
                        <table class="table" id="usersTable">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Will be populated by JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Classes Section -->
        <div id="classesSection" class="d-none">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Class Management</h2>
                <button class="btn btn-primary" onclick="showAddClassModal()">
                    <i class="bi bi-plus-lg"></i> Add Class
                </button>
            </div>
            
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="classesTable">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Teacher</th>
                                    <th>Students</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Will be populated by JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Modals -->
    @include('admin.partials.user-modal')
    @include('admin.partials.class-modal')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Tambahkan di awal script
        const token = localStorage.getItem('token');
        
        if (!token) {
            alert('Token tidak ditemukan. Silakan login kembali.');
            window.location.href = '/login';
        }

        // Fungsi untuk handle submit form
        function handleClassSubmit(event) {
            event.preventDefault();
            
            const token = localStorage.getItem('token');
            if (!token) {
                alert('Token tidak ditemukan. Silakan login kembali.');
                window.location.href = '/login';
                return;
            }

            const formData = {
                name: document.getElementById('className').value,
                description: document.getElementById('classDescription').value,
                kkm_score: parseInt(document.getElementById('classKkm').value),
                academic_year: document.getElementById('classYear').value,
                semester: document.getElementById('classSemester').value,
                status: document.getElementById('classStatus').value
            };

            fetch('/api/v1/admin/classes', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`,
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(formData)
            })
            .then(response => {
                if (response.ok) {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('classModal'));
                    modal.hide();
                    alert('Kelas berhasil ditambahkan');
                    location.reload();
                } else {
                    return response.json().then(errorData => {
                        throw new Error(errorData.message || 'Terjadi kesalahan');
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error: ' + error.message);
            });
        }

        // Tambahkan event listener ke form menggunakan onsubmit di HTML
        document.getElementById('classForm').onsubmit = handleClassSubmit;

        // Fungsi untuk mengambil dan menampilkan total kelas aktif
        async function fetchActiveClasses() {
            try {
                const response = await fetch('/api/v1/admin/dashboard/overview', {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });

                if (response.ok) {
                    const result = await response.json();
                    document.getElementById('totalClasses').textContent = result.data.total_active_classes;
                    document.getElementById('totalUsers').textContent = result.data.total_users;
                } else {
                    document.getElementById('totalClasses').textContent = 'Error';
                    document.getElementById('totalUsers').textContent = 'Error';
                    console.error('Gagal mengambil data overview');
                }
            } catch (error) {
                console.error('Error:', error);
                document.getElementById('totalClasses').textContent = 'Error';
                document.getElementById('totalUsers').textContent = 'Error';
            }
        }

        // Load data saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            fetchActiveClasses();
        });

        // Hide loading animation when page is loaded
        window.addEventListener('load', function() {
            document.getElementById('loading').style.display = 'none';
        });

        // Add animation to cards when they come into view
        const observerOptions = {
            threshold: 0.1
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate__fadeInUp');
                }
            });
        }, observerOptions);

        document.querySelectorAll('.card').forEach(card => {
            observer.observe(card);
        });

        function setActiveNav(id) {
            document.querySelectorAll('.nav-link').forEach(link => {
                link.classList.remove('active');
            });
            document.querySelector(`[onclick="show${id}()"]`)?.classList.add('active');
        }

        function showOverview() {
            setActiveNav('Overview');
            document.getElementById('overviewSection').classList.remove('d-none');
            document.getElementById('usersSection').classList.add('d-none');
            document.getElementById('classesSection').classList.add('d-none');
        }

        function showUsers() {
            setActiveNav('Users');
            document.getElementById('overviewSection').classList.add('d-none');
            document.getElementById('usersSection').classList.remove('d-none');
            document.getElementById('classesSection').classList.add('d-none');
        }

        // Inisialisasi active state saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            const currentPath = window.location.pathname;
            if (currentPath.includes('classes')) {
                setActiveNav('Classes');
            } else {
                showOverview(); // Default ke Overview
            }
        });
    </script>
</body>
</html> 