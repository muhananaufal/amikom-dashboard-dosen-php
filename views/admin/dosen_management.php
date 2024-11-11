<?php
session_start();
if ($_SESSION['role'] !== 'admin') {
  header('Location: /views/auth/login.php');
  exit;
}
$isActive = 'dosen management';
$title = 'Dosen Management';

require_once '../components/header.php';
require_once '../../controllers/DosenController.php';

$controller = new DosenController();
$dosenList = $controller->getAllDosenWithMataKuliah();
$mataKuliahList = $controller->getMataKuliah();
?>

<div class="bg-tertiary pt-30px pe-40px ps-40px" style="min-height: 100vh">
  <div class="pt-3px row d-flex justify-content-md-between justify-content-center">
    <div class="col-md-6">
      <h1 class="fw-semibold fs-5 secondary-color ms-md-3 d-inline" aria-label="Select Course" style="max-width: 200px">Daftar Dosen
      </h1>
    </div>

    <div class="col-md-6 d-flex flex-column flex-md-row align-items-center justify-content-md-end">
      <button class="btn btn-submit-no-shadow mb-2 mb-md-0 me-md-2" data-bs-toggle="modal" data-bs-target="#addDosenModal" style="width: 180px;">
        <i class="bi bi-plus-lg"></i> Add Dosen
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
                <th style="width: 425px" class="pb-2 px-2">No</th>
                <th style="width: 425px" class="pb-2 px-2 ">Nama Dosen</th>
                <th style="width: 425px" class="pb-2 px-2">NIDN</th>
                <th style="width: 425px" class="pb-2 px-2">Mata Kuliah</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($dosenList as $index => $dosen): ?>
                <tr>

                  <td class="border-top border-bottom py-3 d-flex justify-content-center" style="height: 81px;">
                    <button type="button" class="border-0 bg-transparent" data-bs-toggle="modal" data-bs-target="#editDosenModal<?= $dosen['id'] ?>">
                      <i class="bi bi-pencil-square fs-4 queternary-color"></i>
                    </button>
                    <button type="button" class="border-0 bg-transparent" data-bs-toggle="modal" data-bs-target="#deleteDosenModal<?= $dosen['id'] ?>">
                      <i class="bi bi-trash fs-4 text-danger"></i>
                    </button>

                  </td>
                  <td class="border-top border-bottom py-3 "><?= $index + 1 ?></td>
                  <td class="border-top border-bottom py-3"><?= $dosen['nama'] ?></td>
                  <td class="border-top border-bottom py-3"><?= $dosen['nidn'] ?></td>
                  <td class="border-top border-bottom">
                    <div class="mata-kuliah-container">
                      <?= $dosen['kode_mata_kuliah'] ?>
                    </div>
                  </td>
                </tr>
                <!-- Modal Edit Dosen -->
                <div class="modal fade" id="editDosenModal<?= $dosen['id'] ?>" tabindex="-1" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title">Edit Dosen</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>
                      <div class="modal-body">
                        <form method="post" action="../../controllers/DosenController.php?action=edit">
                          <input type="hidden" name="id" value="<?= $dosen['id'] ?>">
                          <div class="mb-3">
                            <label for="nama" class="form-label">Nama Dosen</label>
                            <input type="text" class="form-control" id="nama" name="nama" value="<?= htmlspecialchars($dosen['nama']) ?>" required>
                          </div>
                          <div class="mb-3">
                            <label for="nidn" class="form-label">NIDN</label>
                            <input type="number" class="form-control" id="nidn" name="nidn" value="<?= htmlspecialchars($dosen['nidn']) ?>" required maxlength="10">
                          </div>
                          <div class="mb-3">
                            <label for="mata_kuliah_id" class="form-label">Mata Kuliah</label>
                            <?php
                            $selectedMataKuliahIds = $controller->getDosenMataKuliah($dosen['id']);
                            foreach ($mataKuliahList as $mataKuliah): ?>
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="mata_kuliah_ids[]" value="<?= $mataKuliah['id'] ?>" id="mk<?= $mataKuliah['id'] ?>" <?= in_array($mataKuliah['id'], $selectedMataKuliahIds) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="mk<?= $mataKuliah['id'] ?>">
                                  <?= htmlspecialchars($mataKuliah['nama_mk']) ?>
                                </label>
                              </div>
                            <?php endforeach; ?>
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

                <!-- Modal Delete Dosen -->
                <div class="modal fade" id="deleteDosenModal<?= $dosen['id'] ?>" tabindex="-1" aria-labelledby="deleteDosenModalLabel<?= $dosen['id'] ?>" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="deleteDosenModalLabel<?= $dosen['id'] ?>">Delete Dosen</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                        Apakah Anda yakin ingin menghapus dosen <strong><?= htmlspecialchars($dosen['nama']) ?></strong> dengan NIDN <strong><?= htmlspecialchars($dosen['nidn']) ?></strong>?
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-submit-outline-no-shadow" data-bs-dismiss="modal">Cancel</button>

                        <!-- Form delete with POST method -->
                        <form method="post" action="/controllers/DosenController.php?action=delete">
                          <input type="hidden" name="id" value="<?= $dosen['id'] ?>">
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

<!-- Modal Tambah Dosen -->
<div class="modal fade" id="addDosenModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add Dosen</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form method="post" action="../../controllers/DosenController.php?action=add">
          <div class="mb-3">
            <label for="nama" class="form-label">Nama Dosen</label>
            <input type="text" class="form-control" id="nama" name="nama" required>
          </div>
          <div class="mb-3">
            <label for="nidn" class="form-label">NIDN</label>
            <input type="number" class="form-control" id="nidn" name="nidn" max="2024123199" min="1924010100" maxlength="10" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Mata Kuliah (Min 1)</label>
            <!-- Ubah menjadi checkbox list atau gunakan select2 -->
            <?php foreach ($mataKuliahList as $mataKuliah): ?>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="mata_kuliah_ids[]" value="<?= $mataKuliah['id'] ?>" id="mk<?= $mataKuliah['id'] ?>">
                <label class="form-check-label" for="mk<?= $mataKuliah['id'] ?>">
                  <?= htmlspecialchars($mataKuliah['nama_mk']) ?>
                </label>
              </div>
            <?php endforeach; ?>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-submit-outline-no-shadow" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-submit-no-shadow">Add Dosen</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>






<?php require_once '../components/footer.php'; ?>