<?php
// database.php

function loadEnv($path)
{
  if (!file_exists($path)) {
    return;
  }

  $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
  foreach ($lines as $line) {
    if (strpos(trim($line), '#') === 0) {
      continue;
    }

    list($name, $value) = explode('=', $line, 2);
    $_ENV[trim($name)] = trim($value);
  }
}

loadEnv(__DIR__ . '/.env');

function connectDatabase()
{
  $host = $_ENV['DB_HOST'] ?? 'localhost';
  $db = $_ENV['DB_NAME'] ?? 'backend-uas';
  $user = $_ENV['DB_USER'] ?? 'root';
  $pass = $_ENV['DB_PASS'] ?? '';

  try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $pdo;
  } catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    return null;
  }
}
