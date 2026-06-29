<?php
// ============================================
// DATABASE CONFIGURATION
// ============================================

define('DB_HOST', 'localhost');
define('DB_NAME', 'inventaris_db');
define('DB_USER', 'root');
define('DB_PASS', '');

// ============================================
// DATABASE CONNECTION FUNCTION
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
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
            ]
        );
        return $pdo;
    } catch (PDOException $e) {
        die("Koneksi database gagal: " . $e->getMessage());
    }
}

// ============================================
// DATABASE HELPER FUNCTIONS
// ============================================

/**
 * Execute query with parameters
 */
function query($sql, $params = []) {
    $pdo = getDbConnection();
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt;
}

/**
 * Fetch all rows
 */
function fetchAll($sql, $params = []) {
    return query($sql, $params)->fetchAll();
}

/**
 * Fetch single row
 */
function fetchOne($sql, $params = []) {
    return query($sql, $params)->fetch();
}

/**
 * Fetch single column value
 */
function fetchColumn($sql, $params = []) {
    return query($sql, $params)->fetchColumn();
}

/**
 * Insert data into table
 */
function insert($table, $data) {
    $pdo = getDbConnection();
    $columns = implode(', ', array_keys($data));
    $placeholders = ':' . implode(', :', array_keys($data));
    $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($data);
    return $pdo->lastInsertId();
}

/**
 * Update data in table
 */
function update($table, $data, $where, $whereParams = []) {
    $pdo = getDbConnection();
    $set = [];
    foreach ($data as $key => $value) {
        $set[] = "$key = :$key";
    }
    $sql = "UPDATE $table SET " . implode(', ', $set) . " WHERE $where";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array_merge($data, $whereParams));
    return $stmt->rowCount();
}

/**
 * Delete data from table
 */
function delete($table, $where, $params = []) {
    $pdo = getDbConnection();
    $sql = "DELETE FROM $table WHERE $where";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->rowCount();
}

/**
 * Get total count
 */
function getCount($table, $where = '', $params = []) {
    $sql = "SELECT COUNT(*) FROM $table";
    if ($where) {
        $sql .= " WHERE $where";
    }
    return fetchColumn($sql, $params);
}

/**
 * Begin transaction
 */
function beginTransaction() {
    $pdo = getDbConnection();
    return $pdo->beginTransaction();
}

/**
 * Commit transaction
 */
function commit() {
    $pdo = getDbConnection();
    return $pdo->commit();
}

/**
 * Rollback transaction
 */
function rollback() {
    $pdo = getDbConnection();
    return $pdo->rollBack();
}

/**
 * Get last inserted ID
 */
function lastInsertId() {
    $pdo = getDbConnection();
    return $pdo->lastInsertId();
}

// ============================================
// SESSION HELPER
// ============================================

function setCurrentUserId($userId) {
    $_SESSION['current_user_id'] = $userId;
    // Set for triggers
    $pdo = getDbConnection();
    $pdo->exec("SET @current_user_id = " . (int)$userId);
}

function getCurrentUserId() {
    return $_SESSION['user']['id'] ?? 0;
}