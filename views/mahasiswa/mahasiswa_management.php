<?php



session_start();
if ($_SESSION['role'] !== 'mahasiswa') {
  header('Location: /views/auth/login.php');
  exit;
}
$isActive = 'mahasiswa management';
$title = 'Mahasiswa Management';

require_once '../components/header.php';
require_once '../../controllers/MahasiswaController.php';

$pdo = connectDatabase();
$mahasiswaController = new MahasiswaController($pdo);
$user_id = $_SESSION['user_id'];

$mahasiswa_id = $mahasiswaController->getMahasiswaIdByUserId($user_id);

$nilaiList = $mahasiswaController->getNilaiByMahasiswaId($mahasiswa_id);
?>

<div class="bg-tertiary pt-30px pe-40px ps-40px" style="min-height: 100vh">
  <div class="row g-4 pt-2">
    <div class="col-12">
      <div class="bg-lightcustom shadow-sm rounded-4 d-flex align-items-center px-3 py-2">
        <div class="p-3 table-responsive">
          <table class="text-center" style="width: 100%;" id="mahasiswaTable">
            <thead>
              <tr>
                <th style="width: 200px" class="pb-2 px-2">No</th>
                <th style="width: 200px" class="pb-2 px-2">Kode Mata Kuliah</th>
                <th style="width: 200px" class="pb-2 px-2">Nama Mata Kuliah</th>
                <th style="width: 200px" class="pb-2 px-2">Nama Dosen</th>
                <th style="width: 200px" class="pb-2 px-2">Kehadiran</th>
                <th style="width: 200px" class="pb-2 px-2">Tugas</th>
                <th style="width: 200px" class="pb-2 px-2">Kuis</th>
                <th style="width: 200px" class="pb-2 px-2">Responsi</th>
                <th style="width: 200px" class="pb-2 px-2">UTS</th>
                <th style="width: 200px" class="pb-2 px-2">UAS</th>
                <th style="width: 200px" class="pb-2 px-2">Keterangan</th>
              </tr>
            </thead>
            <tbody>
              <?php
              if (count($nilaiList) > 0) {
                foreach ($nilaiList as $index => $nilai): ?>
                  <tr>
                    <td class="border-top border-bottom py-3"><?= $index + 1 ?></td>
                    <td class="border-top border-bottom py-3"><?= htmlspecialchars($nilai['kode_mk']) ?></td>
                    <td class="border-top border-bottom py-3"><?= htmlspecialchars($nilai['nama_mk']) ?></td>
                    <td class="border-top border-bottom py-3"><?= htmlspecialchars($nilai['nama_dosen']) ?></td>
                    <td class="border-top border-bottom py-3"><?= htmlspecialchars($nilai['kehadiran']) ?></td>
                    <td class="border-top border-bottom py-3"><?= htmlspecialchars($nilai['tugas']) ?></td>
                    <td class="border-top border-bottom py-3"><?= htmlspecialchars($nilai['kuis']) ?></td>
                    <td class="border-top border-bottom py-3"><?= htmlspecialchars($nilai['responsi']) ?></td>
                    <td class="border-top border-bottom py-3"><?= htmlspecialchars($nilai['uts']) ?></td>
                    <td class="border-top border-bottom py-3"><?= htmlspecialchars($nilai['uas']) ?></td>
                    <td class="border-top border-bottom py-3">
                      <span class="btn btn-content" style="min-width: 100px; cursor: default">
                        <?= htmlspecialchars($nilai['keterangan']) ?>
                      </span>
                    </td>
                  </tr>
                <?php endforeach;
              } else { ?>
                <tr>
                  <td colspan="11" class="border-top border-bottom py-3 text-center">Tidak ada kelas dan nilai terkait</td>
                </tr>
              <?php } ?>

            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once '../components/footer.php'; ?>