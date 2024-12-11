<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kerjakan Tugas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-light">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="{{ route('student.class.detail', ['id' => $classId]) }}">
                <i class="bi bi-arrow-left"></i> Kembali ke Kelas
            </a>
            <div class="d-flex align-items-center">
                <span class="text-white me-3" id="timerDisplay"></span>
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
    </nav>

    <!-- Main Content -->
    <div class="container py-4">
        <div class="card mb-4">
            <div class="card-body">
                <h4 class="card-title" id="taskTitle">Memuat...</h4>
                <p class="text-muted mb-0" id="taskDescription">Memuat...</p>
                <small class="text-danger">
                    <i class="bi bi-clock"></i> Deadline: <span id="taskDeadline">Memuat...</span>
                </small>
            </div>
        </div>

        <form id="quizForm">
            <div id="questionsList">
                <!-- Soal akan dimuat di sini -->
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <button type="button" class="btn btn-secondary" id="prevQuestion" style="display: none;">
                            <i class="bi bi-arrow-left"></i> Soal Sebelumnya
                        </button>
                        <button type="button" class="btn btn-primary" id="nextQuestion">
                            Soal Selanjutnya <i class="bi bi-arrow-right"></i>
                        </button>
                        <button type="submit" class="btn btn-success" id="submitQuiz" style="display: none;">
                            <i class="bi bi-check-circle"></i> Selesai
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const taskId = {{ $taskId }};
        let currentQuestion = 0;
        let questions = [];
        let userAnswers = {}; // Untuk menyimpan jawaban sementara

        $(document).ready(function() {
            loadTaskDetails();
            loadQuestions();

            $('#quizForm').on('submit', function(e) {
                e.preventDefault();
                submitAnswers();
            });

            $('#nextQuestion').click(function() {
                if (currentQuestion < questions.length - 1) {
                    currentQuestion++;
                    showQuestion(currentQuestion);
                }
            });

            $('#prevQuestion').click(function() {
                if (currentQuestion > 0) {
                    currentQuestion--;
                    showQuestion(currentQuestion);
                }
            });
        });

        function loadTaskDetails() {
            $.ajax({
                url: `/api/v1/student/tasks/${taskId}`,
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('token')}`,
                    'Accept': 'application/json'
                },
                success: function(response) {
                    const task = response.data.task;
                    
                    // Update judul dan deskripsi tugas
                    $('#taskTitle').text(task.title || 'Sistem Reproduksi Manusia');
                    $('#taskDescription').text(task.description || 'Kerjakan soal-soal berikut dengan teliti');
                    
                    // Format dan update deadline
                    const deadline = new Date(task.deadline);
                    const formattedDeadline = deadline.toLocaleString('id-ID', {
                        weekday: 'long',
                        day: 'numeric',
                        month: 'long',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                    $('#taskDeadline').text(formattedDeadline);

                    // Mulai hitung mundur
                    startCountdown(deadline);
                },
                error: function(xhr) {
                    // Tampilkan data default jika gagal memuat dari API
                    $('#taskTitle').text('Sistem Reproduksi Manusia');
                    $('#taskDescription').text('Kerjakan soal-soal berikut dengan teliti');
                    $('#taskDeadline').text('10 Januari 2025 21:10');
                }
            });
        }

        // Fungsi untuk menghitung mundur waktu
        function startCountdown(deadline) {
            function updateTimer() {
                const now = new Date().getTime();
                const distance = new Date(deadline).getTime() - now;

                if (distance < 0) {
                    clearInterval(timerInterval);
                    $('#timerDisplay').text('Waktu habis!');
                    $('#submitQuiz').prop('disabled', true);
                    return;
                }

                // Hitung waktu tersisa
                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                // Tampilkan timer dengan format yang lebih baik
                $('#timerDisplay').html(`
                    <i class="bi bi-clock"></i> Sisa Waktu: 
                    ${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}
                `);
            }

            // Update timer setiap detik
            updateTimer();
            const timerInterval = setInterval(updateTimer, 1000);
        }

        function loadQuestions() {
            // Contoh data soal (seharusnya diambil dari API)
            questions = [
                {
                    id: 1,
                    question: "Apa fungsi utama sistem reproduksi manusia?",
                    options: [
                        "Menghasilkan keturunan",
                        "Mencerna makanan",
                        "Mengatur suhu tubuh",
                        "Mengedarkan darah"
                    ],
                    correctAnswer: 0
                },
                {
                    id: 2,
                    question: "Organ reproduksi utama pada pria adalah...",
                    options: [
                        "Ovarium",
                        "Testis",
                        "Uterus",
                        "Vagina"
                    ],
                    correctAnswer: 1
                },
                // Tambahkan soal lainnya
            ];

            showQuestion(currentQuestion);
        }

        function showQuestion(index) {
            const question = questions[index];
            let html = `
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Soal ${index + 1} dari ${questions.length}</h5>
                        <p class="card-text">${question.question}</p>
                        <div class="options">
            `;

            question.options.forEach((option, i) => {
                // Tambahkan checked jika sudah ada jawaban sebelumnya
                const isChecked = userAnswers[question.id] === i.toString() ? 'checked' : '';
                html += `
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="question${question.id}" 
                            id="option${i}" value="${i}" ${isChecked}>
                        <label class="form-check-label" for="option${i}">
                            ${option}
                        </label>
                    </div>
                `;
            });

            html += `
                        </div>
                    </div>
                </div>
            `;

            $('#questionsList').html(html);
            
            // Tambahkan event listener untuk menyimpan jawaban
            $(`input[name="question${question.id}"]`).on('change', function() {
                userAnswers[question.id] = $(this).val();
            });

            updateNavigationButtons();
        }

        function updateNavigationButtons() {
            $('#prevQuestion').toggle(currentQuestion > 0);
            $('#nextQuestion').toggle(currentQuestion < questions.length - 1);
            $('#submitQuiz').toggle(currentQuestion === questions.length - 1);
        }

        function submitAnswers() {
            // Cek apakah semua soal sudah dijawab
            let unansweredQuestions = [];
            questions.forEach((q, index) => {
                if (!userAnswers[q.id]) {
                    unansweredQuestions.push(index + 1);
                }
            });

            if (unansweredQuestions.length > 0) {
                Swal.fire({
                    title: 'Peringatan!',
                    html: `Ada soal yang belum dijawab:<br>Soal nomor ${unansweredQuestions.join(', ')}`,
                    icon: 'warning',
                    confirmButtonText: 'Kembali'
                });
                return;
            }

            // Konfirmasi sebelum submit
            Swal.fire({
                title: 'Konfirmasi Pengumpulan',
                text: 'Apakah Anda yakin ingin mengumpulkan tugas ini? Jawaban tidak dapat diubah setelah dikumpulkan.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Kumpulkan',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#dc3545'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Redirect ke halaman kelas dengan parameter status
                    Swal.fire({
                        title: 'Berhasil!',
                        text: 'Tugas berhasil dikumpulkan',
                        icon: 'success',
                        confirmButtonColor: '#28a745'
                    }).then(() => {
                        // Pastikan classId tersedia
                        const classId = new URLSearchParams(window.location.search).get('classId');
                        // Redirect dengan menggunakan window.location.href
                        window.location.href = `/student/classes/${classId}?status=submitted&taskId=${taskId}`;
                    });
                }
            });
        }
    </script>
</body>
</html> 