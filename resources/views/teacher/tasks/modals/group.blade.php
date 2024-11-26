<!-- Modal Manajemen Kelompok -->
<div class="modal fade" id="manageGroupsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Manajemen Kelompok</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="groupsList">
                    <!-- Daftar kelompok akan dimuat di sini -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Buat Kelompok -->
<div class="modal fade" id="createGroupModal" tabindex="-1">
    <div class="modal-dialog">
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
                        <textarea class="form-control" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Anggota Kelompok</label>
                        <div id="availableStudentsList">
                            <!-- Daftar siswa akan dimuat di sini -->
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan Kelompok</button>
                </form>
            </div>
        </div>
    </div>
</div> 