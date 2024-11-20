<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelas</title>
</head>
<body>
<div class="modal fade" id="classModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Kelas Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="classForm">
                    <div class="mb-3">
                        <label class="form-label">Nama Kelas</label>
                        <input type="text" class="form-control" id="className" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Guru</label>
                        <select class="form-select" id="teacherId" required>
                            <option value="">Pilih Guru</option>
                            <!-- Akan diisi oleh JavaScript -->
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Pilih Murid</label>
                        <div class="border p-3 rounded" style="max-height: 200px; overflow-y: auto;">
                            <div class="d-flex justify-content-end mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="selectAllStudents">
                                    <label class="form-check-label" for="selectAllStudents">
                                        Pilih Semua
                                    </label>
                                </div>
                            </div>
                            <div id="studentsList">
                                <!-- Akan diisi oleh JavaScript -->
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="classDescription" rows="3"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">KKM</label>
                            <input type="number" class="form-control" id="classKkm" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Tahun Ajaran</label>
                            <input type="text" class="form-control" id="classYear" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Semester</label>
                            <select class="form-select" id="classSemester" required>
                                <option value="1">Semester 1</option>
                                <option value="2">Semester 2</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" id="classStatus" required>
                            <option value="active">Aktif</option>
                            <option value="inactive">Tidak Aktif</option>
                        </select>
                    </div>

                    <div class="text-end">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div> 
</body>
</html>
