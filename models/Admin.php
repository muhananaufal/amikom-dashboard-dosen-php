<?php
// models/Admin.php

class Admin
{
  private $pdo;

  public function __construct($pdo)
  {
    $this->pdo = $pdo;
  }

  // Mendapatkan data admin berdasarkan username


  public function getAdminByUsername($username)
  {
    $stmt = $this->pdo->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  // Memverifikasi password admin
  public function verifyPassword($username, $password)
  {
    $user = $this->getAdminByUsername($username);
    return password_verify($password, $user['password']);
  }

  // Memperbarui username dan password admin
  public function updateProfile($oldUsername, $newUsername, $newPassword = null)
  {
    $this->pdo->beginTransaction();

    try {
      // Update username
      $stmt = $this->pdo->prepare("UPDATE users SET username = :newUsername WHERE username = :oldUsername");
      $stmt->bindParam(':newUsername', $newUsername);
      $stmt->bindParam(':oldUsername', $oldUsername);
      $stmt->execute();

      // Update password jika password baru disediakan
      if ($newPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
        $stmt = $this->pdo->prepare("UPDATE users SET password = :password WHERE username = :newUsername");
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':newUsername', $newUsername);
        $stmt->execute();
      }

      $this->pdo->commit();
      return true;
    } catch (Exception $e) {
      $this->pdo->rollBack();
      throw $e;
    }
  }
}
