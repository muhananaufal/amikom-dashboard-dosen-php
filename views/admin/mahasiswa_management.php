<?php



session_start();
if ($_SESSION['role'] !== 'admin') {
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

$students = $mahasiswaController->getAllMahasiswaWithDetails();
$mataKuliahList = $mataKuliahController->getAllMataKuliah();
$dosenList = $dosenController->getAllDosen();

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
        <option value="allMahasiswa" id="allMahasiswa">Tampilkan Semua Mahasiswa</option>
        <?php
        // Fetch all Mata Kuliah along with their Dosen from the controller
        $courses = $mahasiswaController->getAllMataKuliahWithDosen();
        foreach ($courses as $course) {
          echo '<option value="' . $course['id'] . '">' . htmlspecialchars($course['kode_mk']) . '</option>';
        }
        ?>
      </select>


      <!-- Dosen Dropdown (initially empty, filled dynamically based on Mata Kuliah) -->
      <select class="form-select select-mata-kuliah fw-semibold fs-5 secondary-color ms-md-3 d-inline" id="dosenSelect" style="max-width: 200px" name="dosen" disabled>
        <option value="" selected disabled>Pilih Dosen</option>
      </select>
    </div>


    <div class="col-md-6 d-flex flex-column flex-md-row align-items-center justify-content-md-end">
      <button class="btn btn-submit-no-shadow mb-2 mb-md-0 me-md-2" data-bs-toggle="modal" data-bs-target="#addMahasiswaModal" style="width: 180px;">
        <i class="bi bi-plus-lg"></i> Add Mahasiswa
      </button>

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
                <td colspan="3">Tidak ada mahasiswa terdaftar</td>
              </tr>


            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal Add Mahasiswa -->
<div class="modal fade" id="addMahasiswaModal" tabindex="-1" aria-labelledby="addMahasiswaModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addMahasiswaModalLabel">Add Mahasiswa</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form method="post" action="../../controllers/MahasiswaController.php?action=add">
          <div class="mb-3">
            <label for="nim" class="form-label">NIM (XX.XX.XXXX)</label>
            <input type="text" class="form-control" id="nim" name="nim" required min="01.01.0001" max="90.90.9999" maxlength="10" pattern="[0-9.]*">
          </div>
          <!-- Input Nama Mahasiswa -->
          <div class="mb-3">
            <label for="nama" class="form-label">Nama Mahasiswa</label>
            <input type="text" class="form-control" id="nama" name="nama" required>
          </div>


          <!-- Course and Instructor Selection -->
          <div class="mb-3">
            <label class="form-label">Select Mata Kuliah and Dosen</label>
            <?php
            $courses = $mahasiswaController->getAllMataKuliahWithDosen();
            foreach ($courses as $course) {
              echo '<div class="form-check">';
              echo '<input class="form-check-input" type="checkbox" name="mata_kuliah[]" value="' . $course['id'] . '" id="course' . $course['id'] . '" data-course-id="' . $course['id'] . '">';
              echo '<label class="form-check-label" for="course' . $course['id'] . '">' . htmlspecialchars($course['kode_mk']) . '</label>';

              // Instructor dropdown, hidden initially and shown when the checkbox is selected
              echo '<select class="form-select mt-2" name="dosen[' . $course['id'] . ']" id="dosen' . $course['id'] . '" style="display:none;">';
              echo '<option value="" selected disabled name="dosen[]">Select Dosen</option>';
              foreach ($course['dosen'] as $dosen) {
                echo '<option value="' . $dosen['id'] . '">' . htmlspecialchars($dosen['nama']) . '</option>';
              }
              echo '</select>';

              echo '</div>';
            }
            ?>
          </div>

          <!-- Modal Footer -->
          <div class="modal-footer d-flex justify-content-between">
            <div>
              <button type="button" class="btn btn-outline-success" data-bs-dismiss="modal">Import From Excel</button>

            </div>
            <div>

              <button type="button" class="btn btn-submit-outline-no-shadow" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-submit-no-shadow">Add Mahasiswa</button>
            </div>
          </div>
        </form>
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
        <form id="editForm" action="/controllers/MahasiswaController.php?action=edit" method="POST">
          <input type="hidden" id="edit-hidden" name="id">
          <div class="mb-3">
            <label for="editNim" class="form-label">NIM Mahasiswa</label>
            <input type="text" class="form-control" id="editNim" name="nim" required min="01.01.0001" max="90.90.9999" maxlength="10" pattern="[0-9.]*">
          </div>
          <div class="mb-3">
            <label for="editNama" class="form-label">Nama Mahasiswa</label>
            <input type="text" class="form-control" id="editNama" name="nama" required>
          </div>
          <!-- Course and Instructor Selection -->
          <div class="mb-3">
            <label class="form-label" style="display:block;" id="selectMataKuliahAndDosen">Select Mata Kuliah and Dosen</label>
            <?php
            $courses = $mahasiswaController->getAllMataKuliahWithDosen();
            foreach ($courses as $course) {
              echo '<div class="form-check"
              >';
              echo '<input class="form-check-input" type="checkbox" name="mata_kuliah[]" value="' . $course['id'] . '" id="course' . $course['id'] . '" data-course-id="' . $course['id'] . '">';
              echo '<label class="form-check-label" for="course' . $course['id'] . '">' . htmlspecialchars($course['kode_mk']) . '</label>';

              // Instructor dropdown, hidden initially and shown when the checkbox is selected
              echo '<select class="dosen-select form-select mt-2" name="dosen[' . $course['id'] . ']" id="dosen' . $course['id'] . '">';
              echo '<option value="" selected disabled>Select Dosen</option>';
              foreach ($course['dosen'] as $dosen) {
                echo '<option value="' . $dosen['id'] . '" data-id="' . $dosen['id'] . '">' . htmlspecialchars($dosen['nama']) . '</option>';
              }
              echo '</select>';

              echo '</div>';
            }
            ?>
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
        <h5 class="modal-title" id="deleteModalLabel">Delete Mahasiswa</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Apakah Anda yakin ingin menghapus mahasiswa <strong><?= htmlspecialchars($dosen['nama']) ?></strong> dengan NIM <strong><?= htmlspecialchars($dosen['nidn']) ?></strong>?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-submit-outline-no-shadow" data-bs-dismiss="modal">Cancel</button>

        <!-- Form delete with POST method -->
        <form action="/controllers/MahasiswaController.php?action=delete" method="POST" id="deleteMahasiswaForm">
          <input type="hidden" name="id" value="" id="delete-hidden">
          <button type="submit" class="btn btn-danger">Delete</button>
        </form>
      </div>
    </div>
  </div>
</div>


<script>
  document.getElementById('mataKuliahSelect').addEventListener('change', function() {
    const mataKuliahId = this.value;
    const dosenSelect = document.getElementById('dosenSelect');
    // Clear previous Dosen options
    dosenSelect.innerHTML = '<option value="" selected disabled>Pilih Dosen</option>';
    dosenSelect.disabled = true;

    // if (mataKuliahId === 'allMahasiswa') {
    //   fetch(`/controllers/MahasiswaController.php?action=getAllMahasiswaWithDetails`)
    //     .then(response => {
    //       if (!response.ok) throw new Error("Network response was not ok");
    //       return response.json();
    //     })
    //     .then(data => {
    //       console.log("Data mahasiswa received:", data); // Debugging line

    //       // Populate the Dosen dropdown with options
    //       data.forEach(mahasiswa => {
    //         const option = document.createElement('option');
    //         option.value = mahasiswa.id;
    //         option.textContent = mahasiswa.nama;
    //         console.log(mahasiswa)
    //       });

    //     })
    //     .catch(error => console.error('Error fetching dosen:', error));
    // }

    if (mataKuliahId && mataKuliahId !== 'allMahasiswa') {
      console.log("Fetching dosen for Mata Kuliah ID:", mataKuliahId); // Debugging line

      // Fetch Dosen options based on Mata Kuliah selection
      fetch(`/controllers/MahasiswaController.php?action=getDosenByMataKuliah&mataKuliahId=${mataKuliahId}`)
        .then(response => {
          if (!response.ok) throw new Error("Network response was not ok");
          return response.json();
        })
        .then(data => {
          console.log("Dosen data received:", data); // Debugging line
          // Clear previous Dosen options
          dosenSelect.innerHTML = '<option value="" selected disabled>Pilih Dosen</option>';
          dosenSelect.disabled = true;

          // Populate the Dosen dropdown with options
          data.forEach(dosen => {
            const option = document.createElement('option');
            option.value = dosen.id;
            option.textContent = dosen.nama;
            dosenSelect.appendChild(option);
          });

          // Enable Dosen dropdown after populating
          dosenSelect.disabled = false;
        })
        .catch(error => console.error('Error fetching dosen:', error));
    }
    // 
  });

  $(document).ready(function() {
    loadMahasiswaTanpaMataKuliah();

    function loadMahasiswaTanpaMataKuliah() {
      $.ajax({
        url: '/controllers/MahasiswaController.php?action=getMahasiswaTanpaMataKuliah',
        type: 'GET',
        success: function(response) {
          var mahasiswaTableBody = $('#mahasiswaTable tbody');
          mahasiswaTableBody.empty();

          if (response.length > 0) {
            var header = `      <tr>
        <th style="width: 40px" class="pb-2 px-2">Action</th>
        <th style="width: 200px" class="pb-2 px-2">No</th>
        <th style="width: 200px" class="pb-2 px-2">NIM</th>
        <th style="width: 200px" class="pb-2 px-2">Nama Mahasiswa</th>
        <th style="width: 200px" class="disabled-color pb-2 px-2">Kehadiran</th>
        <th style="width: 200px" class="disabled-color pb-2 px-2">Tugas</th>
        <th style="width: 200px" class="disabled-color pb-2 px-2">Kuis</th>
        <th style="width: 200px" class="disabled-color pb-2 px-2">Responsi</th>
        <th style="width: 200px" class="disabled-color pb-2 px-2">UTS</th>
        <th style="width: 200px" class="disabled-color pb-2 px-2">UAS</th>
        <th style="width: 200px" class="disabled-color pb-2 px-2">Keterangan</th>
      </tr>`
            mahasiswaTableBody.append(header);
            response.forEach(function(mahasiswa, index) {

              var row = `
              <tr>
              <td class="border-top border-bottom py-3 d-flex justify-content-center" style="height: 81px;">
                    <button type="button" class="border-0 bg-transparent edit-mahasiswa edit-mahasiswa-no-mk" data-bs-toggle="modal" data-bs-target="#editModal"
                    data-id="${mahasiswa.id}"
                    data-nim="${mahasiswa.nim}"
                    data-nama_mahasiswa="${mahasiswa.nama_mahasiswa}">
                      <i class="bi bi-pencil-square fs-4 queternary-color"></i>
                    </button>
                    <button type="button" class="border-0 bg-transparent delete-mahasiswa" data-bs-toggle="modal" data-bs-target="#deleteModal"
                    data-id="${mahasiswa.id}"
                    data-nim="${mahasiswa.nim}"
                    data-nama_mahasiswa="${mahasiswa.nama_mahasiswa}">
                      <i class="bi bi-trash fs-4 text-danger"></i>
                    </button>
                  </td>
                <td class="border-top border-bottom py-3">${index + 1}</td>
                <td class="border-top border-bottom py-3">${mahasiswa.nim}</td>
                <td class="border-top border-bottom py-3">${mahasiswa.nama}</td>
                <td colspan="8" class="border-top border-bottom py-3 text-center">Tidak ada mata kuliah dan nilai terkait</td>
              </tr>
            `;
              mahasiswaTableBody.append(row);
            });
            $('.edit-mahasiswa-no-mk').on('click', function() {
              // Ambil data dari atribut data-*
              let id = $(this).data('id'); // Ambil ID untuk form
              let nim = $(this).data('nim');
              let nama_mahasiswa = $(this).data('nama_mahasiswa');

              // Pastikan input di modal diisi dengan data yang sesuai
              $('#editNim').val(nim); // Mengisi property value dari input NIM
              $('#editNama').val(nama_mahasiswa); // Mengisi property value dari input Nama
              $('#edit-hidden').val(id);

              // Reset tampilan checkbox dan dropdown dosen
              $('input[name="mata_kuliah[]"]').prop('checked', false);
              $('select[name^="dosen"]').hide().val(""); // Sembunyikan dan reset semua dropdown dosen

              // Tampilkan semua elemen form-check
              $('.form-check').show();

              // Tampilkan modal
              $('#editModal').modal('show');


              document.querySelectorAll('input[name="mata_kuliah[]"]').forEach(courseCheckbox => {
                courseCheckbox.addEventListener('change', function() {
                  const courseId = this.getAttribute('data-course-id');
                  const dosenSelect = document.getElementById('dosen' + courseId);
                  $('select[name^="dosen[' + courseId + ']"]').show();
                  // Show the select box if the course checkbox is checked, otherwise hide it
                  if (this.checked) {
                    dosenSelect.style.display = 'block';
                  } else {
                    dosenSelect.style.display = 'none';
                    dosenSelect.selectedIndex = 0; // Reset the select box if unchecked
                  }
                });
              });
            });
            $('.delete-mahasiswa').on('click', function() {
              let nim = $(this).data('nim');
              let nama_mahasiswa = $(this).data('nama_mahasiswa');
              let id = $(this).data('id');
              $('.modal-body strong').first().text(nama_mahasiswa);
              $('.modal-body strong').last().text(nim);

              // Set nilai ID di input hidden untuk form delete
              // $('#deleteModal input[name="id"]').val(id);
              $('#delete-hidden').val(id);

              console.log("Mahasiswa yang akan dihapus:", {
                id,
                nim,
                nama_mahasiswa
              });
              console.log("ID mahasiswa yang disiapkan untuk dihapus:", $('#delete-hidden').val());
            });
          } else {
            mahasiswaTableBody.append('<tr><td colspan="11">Tidak  ada mahasiswa tanpa mata kuliah. Silahkan pilih mata kuliah terlebih dahulu</td></tr>');
          }
        },
        error: function() {
          alert('Gagal memuat data mahasiswa tanpa mata kuliah.');
        }
      });
    }


    $('#dosenSelect, #mataKuliahSelect').on('change', function() {
      var dosenId = $('#dosenSelect').val();
      var mataKuliahId = $('#mataKuliahSelect').val();


      if (dosenId && mataKuliahId && mataKuliahId !== 'allMahasiswa') {
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
        <th style="width: 200px" class="pb-2 px-2">NIM</th>
        <th style="width: 200px" class="pb-2 px-2">Nama Mahasiswa</th>
        <th style="width: 200px" class="disabled-color pb-2 px-2">Kehadiran</th>
        <th style="width: 200px" class="disabled-color pb-2 px-2">Tugas</th>
        <th style="width: 200px" class="disabled-color pb-2 px-2">Kuis</th>
        <th style="width: 200px" class="disabled-color pb-2 px-2">Responsi</th>
        <th style="width: 200px" class="disabled-color pb-2 px-2">UTS</th>
        <th style="width: 200px" class="disabled-color pb-2 px-2">UAS</th>
        <th style="width: 200px" class="disabled-color pb-2 px-2">Keterangan</th>
      </tr>`
              mahasiswaTableBody.append(header);
              response.forEach(function(mahasiswa, index) {
                var row = `
                <tr>
                  <td class="border-top border-bottom py-3 d-flex justify-content-center" style="height: 81px;">
                    <button type="button" class="border-0 bg-transparent edit-mahasiswa" data-bs-toggle="modal" data-bs-target="#editModal"
                    data-id="${mahasiswa.id}"
                    data-nim="${mahasiswa.nim}"
                    data-nama_mahasiswa="${mahasiswa.nama_mahasiswa}">
                      <i class="bi bi-pencil-square fs-4 queternary-color"></i>
                    </button>
                    <button type="button" class="border-0 bg-transparent delete-mahasiswa" data-bs-toggle="modal" data-bs-target="#deleteModal"
                    data-id="${mahasiswa.id}"
                    data-nim="${mahasiswa.nim}"
                    data-nama_mahasiswa="${mahasiswa.nama_mahasiswa}">
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
                let id = $(this).data('id'); // Ambil ID untuk form
                let nim = $(this).data('nim');
                let nama_mahasiswa = $(this).data('nama_mahasiswa');

                // Pastikan input di modal diisi dengan data yang sesuai
                $('#editNim').val(nim); // Mengisi property value dari input NIM
                $('#editNama').val(nama_mahasiswa); // Mengisi property value dari input Nama
                $('#edit-hidden').val(id);
                $('#selectMataKuliahAndDosen').hide(); // Sembunyikan dan reset semua dropdown dosen

                console.log('Delete ID:', $('#delete-hidden').val());





              });
              $('.delete-mahasiswa').on('click', function() {
                // Ambil data dari atribut data-*
                let nim = $(this).data('nim');
                let nama_mahasiswa = $(this).data('nama_mahasiswa');
                let id = $(this).data('id'); // Ambil ID untuk form
                $('.modal-body strong').first().text(nama_mahasiswa);
                $('.modal-body strong').last().text(nim);

                // Set nilai ID di input hidden untuk form delete
                // $('#deleteModal input[name="id"]').val(id);
                $('#delete-hidden').val(id);

                console.log("Mahasiswa yang akan dihapus:", {
                  id,
                  nim,
                  nama_mahasiswa
                });
                console.log("ID mahasiswa yang disiapkan untuk dihapus:", $('#delete-hidden').val());
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
      } else if (mataKuliahId === 'allMahasiswa') {

        $.ajax({
          url: '/controllers/MahasiswaController.php?action=getAllMahasiswaWithDetails',
          type: 'GET',
          success: function(response) {
            var mahasiswaTableBody = $('#mahasiswaTable tbody');
            mahasiswaTableBody.empty();
            if (response.length > 0) {
              var header = `      <tr>
        <th style="width: 40px" class="pb-2 px-2">Action</th>
        <th style="width: 400px" class="pb-2 px-2">No</th>
        <th style="width: 400px" class="pb-2 px-2">NIM</th>
        <th style="width: 400px" class="pb-2 px-2">Nama Mahasiswa</th>
        <th style="width: 800px" class="pb-2 px-2">Daftar Mata Kuliah dengan Dosen</th>
      </tr>`
              mahasiswaTableBody.append(header);
              response.forEach(function(mahasiswa, index) {
                console.log("Mahasiswa:", mahasiswa, 'Index', index);
                var row = `
              <tr>
              <td class="border-top border-bottom py-3 d-flex justify-content-center" style="height: 81px;">
                    <button type="button" class="border-0 bg-transparent edit-mahasiswa edit-mahasiswa-mk" data-bs-toggle="modal" data-bs-target="#editModal"
                    data-id="${mahasiswa.id}"
                    data-nim="${mahasiswa.nim}"
                    data-nama_mahasiswa="${mahasiswa.nama_mahasiswa}">
                      <i class="bi bi-pencil-square fs-4 queternary-color"></i>
                    </button>
                    <button type="button" class="border-0 bg-transparent delete-mahasiswa" data-bs-toggle="modal" data-bs-target="#deleteModal"
                    data-id="${mahasiswa.id}"
                    data-nim="${mahasiswa.nim}"
                    data-nama_mahasiswa="${mahasiswa.nama_mahasiswa}">
                      <i class="bi bi-trash fs-4 text-danger"></i>
                    </button>
                  </td>
                <td class="border-top border-bottom py-3">${index + 1}</td>
                <td class="border-top border-bottom py-3">${mahasiswa.nim}</td>
                <td class="border-top border-bottom py-3">${mahasiswa.nama_mahasiswa
}</td>
                <td colspan="8" class="border-top border-bottom py-3 text-center">
                  <div class="mata-kuliah-container">
                  
                ${mahasiswa.mata_kuliah_dosen !== null ? mahasiswa.mata_kuliah_dosen : '<span class="text-danger fo">Belum memiliki mata kuliah dan dosen</span>'}
                  </div>
                
                </td>
              </tr>
            `;
                mahasiswaTableBody.append(row);
              });



              $('.edit-mahasiswa-mk').on('click', function() {
                let id = $(this).data('id'); // Mahasiswa ID

                $.ajax({
                  url: `/controllers/MahasiswaController.php?action=getMahasiswaDetails&id=${id}`,
                  type: 'GET',
                  success: function(mahasiswa) {
                    if (mahasiswa) {
                      // Populate NIM and Nama fields
                      $('#editNim').val(mahasiswa.nim);
                      $('#editNama').val(mahasiswa.nama);
                      $('#edit-hidden').val(mahasiswa.id);

                      // Reset mata kuliah checkboxes and dosen dropdowns
                      // Populate mata kuliah checkboxes and dosen dropdowns with current values
                      mahasiswa.mata_kuliah_dosen.forEach(function(item) {
                        console.log("Processing item:", item); // Debugging

                        // Centang checkbox mata kuliah yang sesuai
                        // $(`input[name="mata_kuliah[]"][value="${item.mata_kuliah_id}"]`).prop('checked', true);
                        // Pilih dropdown dosen berdasarkan mata kuliah ID
                        // let dosenSelect = $(`#dosen${item.mata_kuliah_id}`);
                        // console.log('Dosen select element:', dosenSelect); // Debugging elemen select yang ditemukan

                        // Hilangkan 'selected' dari semua opsi di dropdown dosen ini

                        // const selectedOption = $(`#dosen${item.mata_kuliah_id} option:selected`);

                        // // Mengambil nilai data-id
                        // const dosenId = selectedOption.data('id'); // Ini mengakses data-id
                        // console.log("Dosen ID:", dosenId); // Pastika

                        // $(`option[value=""]`).removeAttr('selected');
                        const opt = $(`option[value="${item.dosen_id}"]`);
                        console.log('Coba opt', opt)
                        if (opt === item.dosen_id) {

                          $(`option[name="dosen[]"][value="${item.dosen_id}"]`).attr('selected', 'selected');
                        }


                        // Trigger 'change' pada dropdown agar perubahan terdeteksi
                        dosenSelect.trigger('change');
                      });


                      // Show modal


                      // Tampilkan semua elemen form-check
                      $('.form-check').show();


                    } else {
                      alert('Mahasiswa not found');
                    }
                  },
                  error: function() {
                    alert('Error fetching mahasiswa details');
                  }
                });
              });
              $('.delete-mahasiswa').on('click', function() {
                let nim = $(this).data('nim');
                let nama_mahasiswa = $(this).data('nama_mahasiswa');
                let id = $(this).data('id');
                $('.modal-body strong').first().text(nama_mahasiswa);
                $('.modal-body strong').last().text(nim);

                // Set nilai ID di input hidden untuk form delete
                // $('#deleteModal input[name="id"]').val(id);
                $('#delete-hidden').val(id);

                console.log("Mahasiswa yang akan dihapus:", {
                  id,
                  nim,
                  nama_mahasiswa
                });
                console.log("ID mahasiswa yang disiapkan untuk dihapus:", $('#delete-hidden').val());
              });
            } else {
              mahasiswaTableBody.append('<tr><td colspan="11">Tidak  ada mahasiswa tanpa mata kuliah</td></tr>');
            }
          },
          error: function() {
            alert('Gagal memuat data mahasiswa tanpa mata kuliah.');
          }
        });


      } else {
        var mahasiswaTableBody = $('#mahasiswaTable tbody');
        mahasiswaTableBody.empty()
        mahasiswaTableBody.append('<tr><td colspan="11">Silahkan pilih mata kuliah dan dosen terlebih dahulu</td></tr>');; // Kosongkan tabel jika filter belum lengkap
      }
    });
  });



  document.querySelectorAll('input[name="mata_kuliah[]"]').forEach(courseCheckbox => {
    courseCheckbox.addEventListener('change', function() {
      const courseId = this.getAttribute('data-course-id');
      const dosenSelect = document.getElementById('dosen' + courseId);

      // Show the select box if the course checkbox is checked, otherwise hide it
      if (this.checked) {
        dosenSelect.style.display = 'block';
      } else {
        dosenSelect.style.display = 'none';
        dosenSelect.selectedIndex = 0; // Reset the select box if unchecked
      }
    });
  });


  fetch(`/controllers/MahasiswaController.php?action=getDosenByMataKuliah&mataKuliahId=${mataKuliahId}`)
    .then(response => {
      console.log("Network response status:", response.status); // Debugging line
      if (!response.ok) throw new Error("Network response was not ok");
      return response.json();
    })
    .then(data => {
      console.log("Dosen data received:", data); // Debugging line
      if (data.error) throw new Error(data.error); // Check for PHP errors
      // Populate the Dosen dropdown with options
      data.forEach(dosen => {
        const option = document.createElement('option');
        option.value = dosen.id;
        option.textContent = dosen.nama;
        dosenSelect.appendChild(option);
      });
      dosenSelect.disabled = false; // Enable after populating
    })
    .catch(error => console.error('Error fetching dosen:', error));
</script>





<?php require_once '../components/footer.php'; ?>