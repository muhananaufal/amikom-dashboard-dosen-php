<?php
session_start();
if ($_SESSION['role'] !== 'admin') {
  header('Location: /views/auth/login.php');
  exit;
}

$isActive = 'matakuliah management';
$title = 'Mata Kuliah Management';

require_once '../components/header.php';
require_once '../../config/db.php';
require_once '../../controllers/MataKuliahController.php';

$mataKuliahController = new MataKuliahController();
$subjects = $mataKuliahController->getAllMataKuliah();

?>

<div class="bg-tertiary pt-30px pe-40px ps-40px" style="min-height: 100vh">
  <div class="pt-3px row d-flex justify-content-md-between justify-content-center">
    <div class="col-md-6">
      <h1 class="fw-semibold fs-5 secondary-color ms-md-3 d-inline" aria-label="Select Course" style="max-width: 200px">Daftar Mata Kuliah
      </h1>
    </div>

    <div class="col-md-6 d-flex flex-column flex-md-row align-items-center justify-content-md-end">
      <button class="btn btn-submit-no-shadow mb-2 mb-md-0 me-md-2" data-bs-toggle="modal" data-bs-target="#addMataKuliahModal" style="width: 180px;">
        <i class="bi bi-plus-lg"></i> Add Mata Kuliah
      </button>

      <form class="d-flex" role="search">
        <input class="form-control btn-submit-outline-no-shadow" type="search" placeholder="Search..." aria-label="Search" style="width: 180px; min-width: 180px;">
      </form>
    </div>



  </div>

  <div class="row g-4 pt-2">
    <div class="col-12">
      <div class="bg-lightcustom shadow-sm rounded-4 d-flex align-items-center px-3 py-2" style="max-width: 1800px !important">
        <div class="p-3 table-responsive">
          <table class="text-center" style="width: 100%;">
            <thead>
              <tr>
                <th style="width: 40px" class="pb-2 px-2">Action</th>
                <th style="width: 400px" class="pb-2 px-2">No</th>
                <th style="width: 400px" class="pb-2 px-2 ">Kode Mata Kuliah (Auto)</th>
                <th style="width: 400px" class="pb-2 px-2">Nama Mata Kuliah</th>
                <th style="width: 400px" class="pb-2 px-2">Nomor Mata Kuliah</th>
                <th style="width: 400px" class="pb-2 px-2">SKS</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($subjects as $index => $subject): ?>
                <tr>
                  <td class="border-top border-bottom py-3 d-flex justify-content-center" style="height: 81px;">
                    <button type="button" class="border-0 bg-transparent" data-bs-toggle="modal" data-bs-target="#editModal<?= $subject['id'] ?>">
                      <i class="bi bi-pencil-square fs-4 queternary-color"></i>
                    </button>
                    <button type="button" class="border-0 bg-transparent" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $subject['id'] ?>">
                      <i class="bi bi-trash fs-4 text-danger"></i>
                    </button>
                  </td>
                  <td class="border-top border-bottom py-3"><?= $index + 1 ?></td>
                  <td class="border-top border-bottom py-3"><?= $subject['kode_mk'] ?></td>
                  <td class="border-top border-bottom py-3"><?= $subject['nama_mk'] ?></td>
                  <td class="border-top border-bottom py-3"><?= $subject['nomor_mk'] ?></td>
                  <td class="border-top border-bottom py-3"><?= $subject['sks'] ?></td>
                </tr>

                <!-- Edit Modal -->
                <div class="modal fade" id="editModal<?= $subject['id'] ?>" tabindex="-1" aria-labelledby="editModalLabel<?= $subject['id'] ?>" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel<?= $student['nim'] ?>">Edit Mata Kuliah</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                        <form method="post" action="/controllers/MataKuliahController.php?action=update">
                          <input type="hidden" name="id" value="<?= $subject['id'] ?>">
                          <div class="mb-3">
                            <label for="kode_mk_edit_<?= $subject['id'] ?>" class="form-label disabled-color">Kode Mata Kuliah</label>
                            <input type="text" class="form-control disabled-color" id="kode_mk_edit_<?= $subject['id'] ?>" name="kode_mk" value="<?= htmlspecialchars($subject['kode_mk']) ?>" readonly required>
                          </div>
                          <div class="mb-3">
                            <label for="nama_mk_edit_<?= $subject['id'] ?>" class="form-label">Nama Mata Kuliah</label>
                            <input type="text" class="form-control" id="nama_mk_edit_<?= $subject['id'] ?>" name="nama_mk" required oninput="generateKodeMataKuliah('edit_<?= $subject['id'] ?>')" value="<?= htmlspecialchars($subject['nama_mk']) ?>">
                          </div>
                          <div class="mb-3">
                            <label for="nomor_mk_edit_<?= $subject['id'] ?>" class="form-label">Nomor Mata Kuliah</label>
                            <input type="number" class="form-control" id="nomor_mk_edit_<?= $subject['id'] ?>" name="nomor_mk" required oninput="generateKodeMataKuliah('edit_<?= $subject['id'] ?>')" value="<?= htmlspecialchars($subject['nomor_mk']) ?>" max="50" min="01" maxlength="2">
                          </div>
                          <div class="mb-3">
                            <label for="sks" class="form-label">SKS</label>
                            <select class="form-select" id="sks" name="sks" required>
                              <option value="4" <?php if ($subject['sks'] == 4) echo 'selected'; ?>>4</option>
                              <option value="2" <?php if ($subject['sks'] == 2) echo 'selected'; ?>>2</option>

                            </select>
                          </div>

                          <div class="modal-footer">
                            <button type="button" class="btn btn-submit-outline-no-shadow" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-submit-no-shadow">Update</button>
                          </div>
                        </form>
                      </div>
                    </div>
                  </div>
                </div>
                <!-- Delete Modal -->
                <div class="modal fade" id="deleteModal<?= $subject['id'] ?>" tabindex="-1" aria-labelledby="deleteModalLabel<?= $subject['id'] ?>" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel<?= $subject['id'] ?>">Delete Mata Kuliah</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                        Apakah Anda yakin ingin menghapus mata kuliah <strong><?= htmlspecialchars($subject['nama_mk']) ?></strong>?
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-submit-outline-no-shadow" data-bs-dismiss="modal">Cancel</button>

                        <!-- Form delete with POST method -->
                        <form method="post" action="/controllers/MataKuliahController.php?action=delete">
                          <input type="hidden" name="id" value="<?= $subject['id'] ?>">
                          <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                      </div>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal Add Mata Kuliah -->
<div class="modal fade" id="addMataKuliahModal" tabindex="-1" aria-labelledby="addMataKuliahModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addMataKuliahModalLabel">Add Mata Kuliah</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form method="post" action="../../controllers/MataKuliahController.php?action=add">
          <div class="mb-3">
            <label for="kode_mk_create" class="form-label disabled-color">Kode Mata Kuliah</label>
            <input type="text" class="form-control disabled-color" id="kode_mk_create" name="kode_mk" readonly required>
          </div>
          <div class="mb-3">
            <label for="nama_mk_create" class="form-label">Nama Mata Kuliah</label>
            <input type="text" class="form-control" id="nama_mk_create" name="nama_mk" required oninput="generateKodeMataKuliah('create')">
          </div>
          <div class="mb-3">
            <label for="nomor_mk_create" class="form-label">Nomor Mata Kuliah</label>
            <input type="number" class="form-control" id="nomor_mk_create" name="nomor_mk" required oninput="generateKodeMataKuliah('create')" max="50" min="01" maxlength="2">
          </div>
          <div class="mb-3">
            <label for="sks" class="form-label">SKS</label>
            <select class="form-select" id="sks" name="sks" required>
              <option value="4">4</option>
              <option value="2">2</option>
            </select>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-submit-outline-no-shadow" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-submit-no-shadow">Add Mata Kuliah</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>


<script>
  // JavaScript untuk memperbarui kode mata kuliah secara otomatis
  function generateKodeMataKuliah(modalId) {
    let namaMk = document.getElementById(`nama_mk_${modalId}`).value;
    let nomorMk = document.getElementById(`nomor_mk_${modalId}`).value;

    // Ambil tiga huruf pertama dari setiap kata dan gabungkan
    let kode = namaMk.split(' ').map(k => k.substr(0, 3)).join('').toLowerCase();
    kode = kode.substring(0, 6) + nomorMk.toString().padStart(2, '0'); // 6 karakter + nomor 2 digit

    document.getElementById(`kode_mk_${modalId}`).value = kode;
  }
</script>


<?php require_once '../components/footer.php'; ?>