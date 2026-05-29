<?php
require 'config/database.php';

$stmt = $pdo->query("SELECT 1");
echo "Koneksi berhasil";