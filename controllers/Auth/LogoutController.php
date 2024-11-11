<?php
// controllers/Auth/LogoutController.php

class LogoutController
{
  public function logout()
  {
    session_start();
    session_unset();
    session_destroy();
    header("Location: /views/auth/login.php"); // Redirect to login page
    exit(); // Ensure the script stops after redirect
  }
}

// Check for POST request and execute logout if needed
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $controller = new LogoutController();
  $controller->logout();
}
