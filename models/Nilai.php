<?php
// models/Nilai.php

class Nilai
{
  private $pdo;

  public function __construct($pdo)
  {
    $this->pdo = $pdo;
  }

  // Mendapatkan semua nilai
  public function getAllNilai()
  {
    $stmt = $this->pdo->prepare("
            SELECT n.*, m.nama AS mahasiswa_nama, mk.nama_mk, d.nama AS dosen_nama
            FROM nilai n
            JOIN mahasiswa m ON n.mahasiswa_id = m.id
            JOIN mata_kuliah mk ON n.mata_kuliah_id = mk.id
            JOIN dosen d ON n.dosen_id = d.id
        ");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  // Mendapatkan nilai berdasarkan mahasiswa ID
  public function getNilaiByMahasiswaId($mahasiswa_id)
  {
    $stmt = $this->pdo->prepare("
            SELECT n.*, mk.nama_mk, d.nama AS dosen_nama
            FROM nilai n
            JOIN mata_kuliah mk ON n.mata_kuliah_id = mk.id
            JOIN dosen d ON n.dosen_id = d.id
            WHERE n.mahasiswa_id = ?
        ");
    $stmt->execute([$mahasiswa_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  // Menambahkan atau mengubah nilai mahasiswa
  public function addOrUpdateNilai($mahasiswa_id, $mata_kuliah_id, $dosen_id, $kehadiran, $tugas, $kuis, $responsi, $uts, $uas)
  {
    $stmt = $this->pdo->prepare("
            INSERT INTO nilai (mahasiswa_id, mata_kuliah_id, dosen_id, kehadiran, tugas, kuis, responsi, uts, uas)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE kehadiran = VALUES(kehadiran), tugas = VALUES(tugas), kuis = VALUES(kuis),
                                    responsi = VALUES(responsi), uts = VALUES(uts), uas = VALUES(uas)
        ");
    return $stmt->execute([$mahasiswa_id, $mata_kuliah_id, $dosen_id, $kehadiran, $tugas, $kuis, $responsi, $uts, $uas]);
  }

  // Menghapus nilai mahasiswa
  public function deleteNilai($id)
  {
    $stmt = $this->pdo->prepare("DELETE FROM nilai WHERE id = ?");
    return $stmt->execute([$id]);
  }
}
