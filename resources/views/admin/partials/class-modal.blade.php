<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelas</title>
</head>
<body>
<div class="modal fade" id="classModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="classModalTitle">Tambah Kelas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="classForm" onsubmit="return handleClassSubmit(event)">
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
</body>
</html>
