<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">Dashboard Siswa</a>
            <div class="navbar-nav ms-auto">
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-link text-danger">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <main class="container py-4">
        <h2 class="mb-4">Tugas Anda</h2>

        <div class="row">
            @foreach($tasks as $task)
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">{{ $task->title }}</h5>
                        <p class="card-text">{{ Str::limit($task->description, 100) }}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge bg-{{ $task->status === 'done' ? 'success' : 'warning' }}">
                                {{ $task->status }}
                            </span>
                            <small>Deadline: {{ $task->deadline->format('d M Y') }}</small>
                        </div>
                        @if($task->status !== 'done')
                        <div class="mt-3">
                            <button class="btn btn-primary btn-sm" onclick="submitTask({{ $task->id }})">
                                Submit Tugas
                            </button>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 