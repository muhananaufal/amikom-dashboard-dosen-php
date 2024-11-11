<?php



session_start();
if ($_SESSION['role'] !== 'dosen') {
  header('Location: /views/auth/login.php');
  exit;
}
$isActive = 'mahasiswa management';
$title = 'Mahasiswa Management';

require_once '../components/header.php';
require_once '../../config/db.php';
require_once '../../controllers/MahasiswaController.php';
require_once '../../controllers/MataKuliahController.php';
require_once '../../controllers/DosenController.php';

$pdo = connectDatabase();
$dosenController = new DosenController($pdo);
$mataKuliahController = new MataKuliahController($pdo);
$mahasiswaController = new MahasiswaController($pdo);

$user_id = $_SESSION['user_id'];
$dosen_id = $dosenController->getDosenIdByUserId($user_id);

// Now use dosen_id to get mata kuliah list
$mataKuliahList = $dosenController->getMataKuliahByDosen($dosen_id);

$students = $mahasiswaController->getAllMahasiswaWithDetails();

?>



<div class="bg-tertiary pt-30px pe-40px ps-40px" style="min-height: 100vh">
  <div class="pt-3px row d-flex justify-content-md-between justify-content-center">
    <?php
    // Fetch Mata Kuliah dan Dosen dari database
    ?>

    <div class="col-md-6">
      <!-- Mata Kuliah Dropdown -->
      <select class="form-select select-mata-kuliah fw-semibold fs-5 secondary-color ms-md-3 d-inline" id="mataKuliahSelect" style="max-width: 200px" name="mata_kuliah">
        <option selected disabled value="">Pilih Mata Kuliah</option>
        <?php
        // Fetch all Mata Kuliah along with their Dosen from the controller
        $courses = $mahasiswaController->getAllMataKuliahWithDosen();
        foreach ($courses as $course) {
          echo '<option value="' . $course['id'] . '">' . htmlspecialchars($course['kode_mk']) . '</option>';
        }
        ?>
      </select>



    </div>


    <div class="col-md-6 d-flex flex-column flex-md-row align-items-center justify-content-md-end ">


      <form class="d-flex" role="search">
        <input class="form-control btn-submit-outline-no-shadow" type="search" placeholder="Search..." aria-label="Search" style="width: 180px; min-width: 180px;">
      </form>
    </div>
  </div>

  <div class="row g-4 pt-2">
    <div class="col-12">
      <div class="bg-lightcustom shadow-sm rounded-4 d-flex align-items-center px-3 py-2">
        <div class="p-3 table-responsive">
          <table class="text-center" style="width: 100%;" id="mahasiswaTable">
            <thead>

            </thead>
            <tbody>
              <tr>
                <td colspan="3">Silahkan pilih mata kuliah terlebih dahulu</td>
              </tr>


            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal for Edit (initially hidden) -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editModalLabel">Edit Mahasiswa</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="editForm" action="/controllers/DosenController.php?action=editNilai" method="POST">
          <input type="hidden" id="edit-hidden" name="id">
          <input type="hidden" id="edit-hidden-student-id" name="student_id">
          <input type="hidden" id="edit-hidden-course-id" name="course_id">
          <div class="mb-3">
            <label for="editNim" class="form-label">NIM Mahasiswa</label>
            <input type="text" class="form-control" id="editNim" name="nim" min="01.01.0001" max="90.90.9999" maxlength="10" pattern="[0-9.]*" disabled>
          </div>
          <div class="mb-3">
            <label for="editNama" class="form-label">Nama Mahasiswa</label>
            <input type="text" class="form-control" id="editNama" name="nama" disabled>
          </div>
          <hr>
          <!-- Nilai -->

          <div class="mb-3">
            <label for="editKehadiran" class="form-label">Kehadiran Mahasiswa</label>
            <input type="number" step="0.01" min="0" max="100" required class="form-control" id="editKehadiran" name="kehadiran">
          </div>
          <div class="mb-3">
            <label for="editTugas" class="form-label">Tugas Mahasiswa</label>
            <input type="number" step="0.01" min="0" max="100" required class="form-control" id="editTugas" name="tugas">
          </div>
          <div class="mb-3">
            <label for="editKuis" class="form-label">Kuis Mahasiswa</label>
            <input type="number" step="0.01" min="0" max="100" required class="form-control" id="editKuis" name="kuis">
          </div>
          <div class="mb-3">
            <label for="editResponsi" class="form-label">Responsi Mahasiswa</label>
            <input type="number" step="0.01" min="0" max="100" required class="form-control" id="editResponsi" name="responsi">
          </div>
          <div class="mb-3">
            <label for="editUts" class="form-label">UTS Mahasiswa</label>
            <input type="number" step="0.01" min="0" max="100" required class="form-control" id="editUts" name="uts">
          </div>
          <div class="mb-3">
            <label for="editUas" class="form-label">UAS Mahasiswa</label>
            <input type="number" step="0.01" min="0" max="100" required class="form-control" id="editUas" name="uas">
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
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteModalLabel">Delete Nilai Mahasiswa</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Apakah Anda yakin ingin menghapus semua nilai mahasiswa <strong><?= htmlspecialchars($mahasiswa['nama']) ?></strong> dengan NIM <strong><?= htmlspecialchars($dosen['nidn']) ?></strong>?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-submit-outline-no-shadow" data-bs-dismiss="modal">Cancel</button>

        <!-- Form delete with POST method -->
        <form action="/controllers/DosenController.php?action=hapusNilai" method="POST" id="deleteMahasiswaForm">
          <input type="hidden" id="delete-hidden" name="id">
          <input type="hidden" id="delete-hidden-student-id" name="student_id">
          <input type="hidden" id="delete-hidden-course-id" name="course_id">
          <button type="submit" class="btn btn-danger">Delete</button>
        </form>
      </div>
    </div>
  </div>
</div>


<script>
  $(document).ready(function() {
    $('#mataKuliahSelect').on('change', function() {

      var dosenId = <?php echo json_encode($dosen_id); ?>;
      console.log('Coba dosen id', dosenId)
      var mataKuliahId = $('#mataKuliahSelect').val();


      if (dosenId && mataKuliahId) {
        $.ajax({
          url: '/controllers/MahasiswaController.php?action=filterTable', // Ganti dengan path controller PHP Anda
          type: 'GET',
          data: {

            dosenId: dosenId,
            mataKuliahId: mataKuliahId
          },
          success: function(response) {
            var mahasiswaTableBody = $('#mahasiswaTable tbody');
            mahasiswaTableBody.empty();
            if (response.length > 0) {
              var header = `      <tr>
        <th style="width: 40px" class="pb-2 px-2">Action</th>
        <th style="width: 200px" class="pb-2 px-2">No</th>
        <th style="width: 200px" class="disabled-color pb-2 px-2">NIM</th>
        <th style="width: 200px" class="disabled-color pb-2 px-2">Nama Mahasiswa</th>
        <th style="width: 200px" class="pb-2 px-2">Kehadiran</th>
        <th style="width: 200px" class="pb-2 px-2">Tugas</th>
        <th style="width: 200px" class="pb-2 px-2">Kuis</th>
        <th style="width: 200px" class="pb-2 px-2">Responsi</th>
        <th style="width: 200px" class="pb-2 px-2">UTS</th>
        <th style="width: 200px" class="pb-2 px-2">UAS</th>
        <th style="width: 200px" class="pb-2 px-2">Keterangan</th>
      </tr>`
              mahasiswaTableBody.append(header);
              response.forEach(function(mahasiswa, index) {
                console.log('Ini mahasiswa', mahasiswa)
                var row = `
                <tr>
                  <td class="border-top border-bottom py-3 d-flex justify-content-center" style="height: 81px;">
                    <button type="button" class="border-0 bg-transparent edit-mahasiswa" data-bs-toggle="modal" data-bs-target="#editModal"
                    data-id="${mahasiswa.id}"
                    data-nim="${mahasiswa.nim}"
                    data-nama_mahasiswa="${mahasiswa.nama_mahasiswa}"
                    data-kehadiran="${mahasiswa.kehadiran}"
                    data-tugas="${mahasiswa.tugas}"
                    data-kuis="${mahasiswa.kuis}"
                    data-responsi="${mahasiswa.responsi}"
                    data-uts="${mahasiswa.uts}"
                    data-uas="${mahasiswa.uas}">
                    <i class="bi bi-pencil-square fs-4 queternary-color"></i>
                    </button>

                    <button type="button" class="border-0 bg-transparent delete-mahasiswa" data-bs-toggle="modal" data-bs-target="#deleteModal"
                    data-id="${mahasiswa.id}"
                    data-nim="${mahasiswa.nim}"
                    data-nama_mahasiswa="${mahasiswa.nama_mahasiswa}"
                    data-kehadiran="${mahasiswa.kehadiran}"
                    data-tugas="${mahasiswa.tugas}"
                    data-kuis="${mahasiswa.kuis}"
                    data-responsi="${mahasiswa.responsi}"
                    data-uts="${mahasiswa.uts}"
                    data-uas="${mahasiswa.uas}">
                      <i class="bi bi-trash fs-4 text-danger"></i>
                    </button>
                  </td>
                  <td class="border-top border-bottom py-3">${index + 1}</td>
                  <td class="border-top border-bottom py-3">${mahasiswa.nim}</td>
                  <td class="border-top border-bottom py-3">${mahasiswa.nama_mahasiswa}</td>
                  <td class="border-top border-bottom py-3">${mahasiswa.kehadiran}</td>
                  <td class="border-top border-bottom py-3">${mahasiswa.tugas}</td>
                  <td class="border-top border-bottom py-3">${mahasiswa.kuis}</td>
                  <td class="border-top border-bottom py-3">${mahasiswa.responsi}</td>
                  <td class="border-top border-bottom py-3">${mahasiswa.uts}</td>
                  <td class="border-top border-bottom py-3">${mahasiswa.uas}</td>
                  <td class="border-top border-bottom py-3" style="min-width: 100px;">
                    <span class="btn btn-content" style="min-width: 100px; cursor: default">${mahasiswa.keterangan}</span>
                  </td>
                </tr>

                
              `;

                mahasiswaTableBody.append(row);




              });
              // Event listener untuk tombol edit
              // Setelah tabel diisi, atur event listener untuk tombol edit
              $('.edit-mahasiswa').on('click', function() {
                // Ambil data dari atribut data-*
                let studentId = $(this).data('id');
                let courseId = $('#mataKuliahSelect').val();
                let nim = $(this).data('nim');

                let nama_mahasiswa = $(this).data('nama_mahasiswa');
                let kehadiran = $(this).data('kehadiran');
                let tugas = $(this).data('tugas');
                let kuis = $(this).data('kuis');
                let responsi = $(this).data('responsi');
                let uts = $(this).data('uts');
                let uas = $(this).data('uas');
                console.log('This itu apa?', $(this).data())


                // Pastikan input di modal diisi dengan data yang sesuai
                $('#edit-hidden').val(dosenId); // Menggunakan nilai dosen_id dari JavaScript
                $('#edit-hidden-student-id').val(studentId);
                $('#edit-hidden-course-id').val(courseId);
                $('#editNim').val(nim); // Mengisi property value dari input NIM
                $('#editNama').val(nama_mahasiswa); // Mengisi property value dari input Nama
                $('#editKehadiran').val(kehadiran); // Mengisi property value dari input Nama
                $('#editTugas').val(tugas); // Mengisi property value dari input Nama
                $('#editKuis').val(kuis); // Mengisi property value dari input Nama
                $('#editResponsi').val(responsi); // Mengisi property value dari input Nama
                $('#editUts').val(uts); // Mengisi property value dari input Nama
                $('#editUas').val(uas); // Mengisi property value dari input Nama




              });
              $('.delete-mahasiswa').on('click', function() {
                // Ambil data dari atribut data-*
                let studentId = $(this).data('id');
                let courseId = $('#mataKuliahSelect').val();
                let nim = $(this).data('nim');
                let nama_mahasiswa = $(this).data('nama_mahasiswa');

                $('#delete-hidden').val(dosenId); // Menggunakan nilai dosen_id dari JavaScript
                $('#delete-hidden-student-id').val(studentId);
                $('#delete-hidden-course-id').val(courseId);

                $('.modal-body strong').first().text(nama_mahasiswa);
                $('.modal-body strong').last().text(nim);

                // Set nilai ID di input hidden untuk form delete
                // $('#deleteModal input[name="id"]').val(id);
                $('#delete-hidden').val(id);

              });

            } else {
              var mahasiswaTableBody = $('#mahasiswaTable tbody');
              mahasiswaTableBody.empty()
              mahasiswaTableBody.append('<tr><td colspan="11">Tidak data ada mahasiswa</td></tr>');; // Kosongkan tabel jika filter belum lengkap

            }

          },
          error: function() {
            alert('Terjadi kesalahan dalam memuat data mahasiswa.');

          }
        });
      }
    });
  });
</script>





<?php require_once '../components/footer.php'; ?>