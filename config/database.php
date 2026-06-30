<?php
// config/database.php

// ============================================
// DATABASE CONFIGURATION & HELPER
// ============================================

// Konfigurasi Database
define('DB_HOST', 'localhost');
define('DB_NAME', 'inventaris_db');
define('DB_USER', 'root');
define('DB_PASS', '');

// ============================================
// KONEKSI DATABASE
// ============================================

function getDbConnection() {
    try {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]
        );
        return $pdo;
    } catch (PDOException $e) {
        die("Koneksi database gagal: " . $e->getMessage());
    }
}

// ============================================
// HELPER FUNCTIONS DATABASE
// ============================================

function query($sql, $params = []) {
    $pdo = getDbConnection();
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt;
}

function fetchAll($sql, $params = []) {
    return query($sql, $params)->fetchAll();
}

function fetchOne($sql, $params = []) {
    return query($sql, $params)->fetch();
}

function fetchColumn($sql, $params = []) {
    return query($sql, $params)->fetchColumn();
}

function insert($table, $data) {
    $pdo = getDbConnection();
    
    // Ambil kolom dan tambahkan backtick untuk reserved keywords
    $columns = array_keys($data);
    $escapedColumns = array_map(function($col) {
        return "`$col`";
    }, $columns);
    
    $columnsStr = implode(', ', $escapedColumns);
    $placeholders = ':' . implode(', :', $columns);
    
    $sql = "INSERT INTO $table ($columnsStr) VALUES ($placeholders)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($data);
    return $pdo->lastInsertId();
}

function update($table, $data, $where, $whereParams = []) {
    $pdo = getDbConnection();
    $set = [];
    foreach ($data as $key => $value) {
        $set[] = "`$key` = :$key";
    }
    $sql = "UPDATE $table SET " . implode(', ', $set) . " WHERE $where";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array_merge($data, $whereParams));
    return $stmt->rowCount();
}

function delete($table, $where, $params = []) {
    $pdo = getDbConnection();
    $sql = "DELETE FROM $table WHERE $where";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->rowCount();
}

function getCount($table, $where = '', $params = []) {
    $sql = "SELECT COUNT(*) FROM $table";
    if ($where) {
        $sql .= " WHERE $where";
    }
    return fetchColumn($sql, $params);
}

function beginTransaction() {
    $pdo = getDbConnection();
    return $pdo->beginTransaction();
}

function commit() {
    $pdo = getDbConnection();
    return $pdo->commit();
}

function rollback() {
    $pdo = getDbConnection();
    return $pdo->rollBack();
}

function lastInsertId() {
    $pdo = getDbConnection();
    return $pdo->lastInsertId();
}