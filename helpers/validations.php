<?php
// ============================================
// VALIDATION HELPERS
// ============================================

/**
 * Validate required fields
 */
function validateRequired($data, $fields) {
    $errors = [];
    foreach ($fields as $field) {
        if (!isset($data[$field]) || empty(trim($data[$field]))) {
            $errors[] = ucfirst(str_replace('_', ' ', $field)) . ' wajib diisi';
        }
    }
    return $errors;
}

/**
 * Validate email
 */
function validateEmail($email) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return 'Email tidak valid';
    }
    return null;
}

/**
 * Validate phone
 */
function validatePhone($phone) {
    if (!preg_match('/^(08|62|0)[0-9]{8,13}$/', $phone)) {
        return 'Nomor telepon tidak valid';
    }
    return null;
}

/**
 * Validate date
 */
function validateDate($date, $format = 'Y-m-d') {
    $d = DateTime::createFromFormat($format, $date);
    if (!$d || $d->format($format) !== $date) {
        return 'Tanggal tidak valid';
    }
    return null;
}

/**
 * Validate date range
 */
function validateDateRange($start, $end) {
    if (strtotime($start) > strtotime($end)) {
        return 'Tanggal mulai harus lebih kecil dari tanggal akhir';
    }
    return null;
}

/**
 * Validate quantity
 */
function validateQuantity($quantity, $min = 1, $max = null) {
    if (!is_numeric($quantity) || $quantity < $min) {
        return "Jumlah minimal $min";
    }
    if ($max !== null && $quantity > $max) {
        return "Jumlah maksimal $max";
    }
    return null;
}

/**
 * Validate stock availability
 */
function validateStock($item_id, $quantity) {
    $stock = fetchColumn(
        "SELECT quantity FROM items WHERE id = ? AND status = 'tersedia'",
        [$item_id]
    );
    
    if ($stock < $quantity) {
        return "Stok tidak mencukupi. Tersisa: $stock";
    }
    return null;
}

/**
 * Validate unique field
 */
function validateUnique($table, $field, $value, $excludeId = null) {
    $sql = "SELECT COUNT(*) FROM $table WHERE $field = ?";
    $params = [$value];
    
    if ($excludeId) {
        $sql .= " AND id != ?";
        $params[] = $excludeId;
    }
    
    $count = fetchColumn($sql, $params);
    
    if ($count > 0) {
        return ucfirst(str_replace('_', ' ', $field)) . " sudah digunakan";
    }
    return null;
}

/**
 * Validate image upload
 */
function validateImage($file, $maxSize = null, $allowedTypes = null) {
    $maxSize = $maxSize ?? MAX_FILE_SIZE;
    $allowedTypes = $allowedTypes ?? ALLOWED_EXTENSIONS;
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return 'Gagal upload file';
    }
    
    if ($file['size'] > $maxSize) {
        return 'Ukuran file terlalu besar (maks ' . ($maxSize / 1024 / 1024) . 'MB)';
    }
    
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedTypes)) {
        return 'Format file tidak didukung. Gunakan: ' . implode(', ', $allowedTypes);
    }
    
    return null;
}

/**
 * Validate password strength
 */
function validatePassword($password) {
    $errors = [];
    
    if (strlen($password) < 8) {
        $errors[] = 'Password minimal 8 karakter';
    }
    if (!preg_match('/[A-Z]/', $password)) {
        $errors[] = 'Password harus mengandung huruf besar';
    }
    if (!preg_match('/[a-z]/', $password)) {
        $errors[] = 'Password harus mengandung huruf kecil';
    }
    if (!preg_match('/[0-9]/', $password)) {
        $errors[] = 'Password harus mengandung angka';
    }
    
    return $errors;
}

/**
 * Validate confirmation password
 */
function validatePasswordConfirmation($password, $confirmation) {
    if ($password !== $confirmation) {
        return 'Konfirmasi password tidak cocok';
    }
    return null;
}