<?php
// models/Dosen.php

class Dosen
{
  private $pdo;

  public function __construct($pdo)
  {
    $this->pdo = $pdo;
  }

  // Mendapatkan semua data dosen
  public function getAllDosen()
  {
    $stmt = $this->pdo->prepare("SELECT * FROM dosen");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  // Mendapatkan data dosen berdasarkan ID
  public function getDosenById($id)
  {
    $stmt = $this->pdo->prepare("SELECT * FROM dosen WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  public function getDosenByMataKuliahId($mataKuliahId)
  {
    $stmt = $this->pdo->prepare("
      SELECT d.* 
      FROM dosen d
      JOIN dosen_mata_kuliah dm ON d.id = dm.dosen_id
      WHERE dm.mata_kuliah_id = ?");
    $stmt->execute([$mataKuliahId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  // Menambahkan data dosen
  public function addDosen($nidn, $nama, $user_id)
  {
    $stmt = $this->pdo->prepare("INSERT INTO dosen (nidn, nama, user_id) VALUES (?, ?, ?)");
    return $stmt->execute([$nidn, $nama, $user_id]);
  }

  // Mengedit data dosen
  public function updateDosen($id, $nidn, $nama)
  {
    $stmt = $this->pdo->prepare("UPDATE dosen SET nidn = ?, nama = ? WHERE id = ?");
    return $stmt->execute([$nidn, $nama, $id]);
  }

  // Menghapus data dosen
  public function deleteDosen($id)
  {
    $stmt = $this->pdo->prepare("DELETE FROM dosen WHERE id = ?");
    return $stmt->execute([$id]);
  }
}
