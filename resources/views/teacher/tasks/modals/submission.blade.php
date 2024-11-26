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
                            <!-- Daftar submission akan dimuat di sini -->
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
                    <button type="submit" class="btn btn-primary">Simpan Nilai</button>
                </form>
            </div>
        </div>
    </div>
</div> 