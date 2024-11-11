<?php

class LoginController
{
  private $pdo;

  public function __construct($pdo)
  {
    $this->pdo = $pdo;
  }

  // Login untuk admin berdasarkan username
  public function loginAdmin($username, $password)
  {
    $stmt = $this->pdo->prepare('SELECT * FROM users WHERE username = :username AND role = "admin"');
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
      $_SESSION['user_id'] = $user['id'];
      $_SESSION['username'] = $username;
      $_SESSION['role'] = $user['role'];
      return true;
    }
    return false;
  }

  // Login untuk dosen berdasarkan nidn
  public function loginDosen($nidn, $password)
  {
    $stmt = $this->pdo->prepare('
      SELECT users.* FROM users 
      JOIN dosen ON users.id = dosen.user_id 
      WHERE dosen.nidn = :nidn AND users.role = "dosen"
    ');
    $stmt->bindParam(':nidn', $nidn);
    $stmt->execute();
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
      $_SESSION['user_id'] = $user['id'];
      $_SESSION['nidn'] = $nidn;
      $_SESSION['role'] = 'dosen';
      return true;
    }
    return false;
  }

  // Login untuk mahasiswa berdasarkan nim
  public function loginMahasiswa($nim, $password)
  {
    $stmt = $this->pdo->prepare('
      SELECT users.* FROM users 
      JOIN mahasiswa ON users.id = mahasiswa.user_id 
      WHERE mahasiswa.nim = :nim AND users.role = "mahasiswa"
    ');
    $stmt->bindParam(':nim', $nim);
    $stmt->execute();
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
      $_SESSION['user_id'] = $user['id'];
      $_SESSION['nim'] = $nim;
      $_SESSION['role'] = 'mahasiswa';
      return true;
    }
    return false;
  }
}
