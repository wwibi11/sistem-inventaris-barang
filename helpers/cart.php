<?php
// ============================================
// CART HELPERS (TEMP LOANS)
// ============================================

require_once __DIR__ . '/functions.php';

/**
 * Add item to cart
 */
function addToCart($item_id, $quantity = 1, $condition = 'baik', $notes = '') {
    $session_id = session_id();
    
    // Check if item already in cart
    $existing = fetchOne(
        "SELECT id, quantity FROM temp_loans 
         WHERE session_id = ? AND item_id = ?",
        [$session_id, $item_id]
    );
    
    if ($existing) {
        // Update quantity
        $new_quantity = $existing['quantity'] + $quantity;
        return update(
            'temp_loans',
            ['quantity' => $new_quantity],
            'id = ?',
            [$existing['id']]
        );
    }
    
    // Insert new
    return insert('temp_loans', [
        'session_id' => $session_id,
        'item_id' => $item_id,
        'quantity' => $quantity,
        'condition_before' => $condition,
        'notes' => $notes
    ]);
}

/**
 * Get cart items
 */
function getCartItems() {
    $session_id = session_id();
    
    return fetchAll(
        "SELECT t.*, i.code, i.name, i.photo, i.quantity as stock,
                i.`condition` as item_condition, i.`status` as item_status,
                c.name as category_name
         FROM temp_loans t
         LEFT JOIN items i ON t.item_id = i.id
         LEFT JOIN categories c ON i.category_id = c.id
         WHERE t.session_id = ?
         ORDER BY t.created_at DESC",
        [$session_id]
    );
}

/**
 * Get cart total items
 */
function getCartTotal() {
    $session_id = session_id();
    $result = fetchOne(
        "SELECT SUM(quantity) as total FROM temp_loans WHERE session_id = ?",
        [$session_id]
    );
    return $result['total'] ?? 0;
}

/**
 * Get cart total unique items
 */
function getCartCount() {
    $session_id = session_id();
    $result = fetchOne(
        "SELECT COUNT(*) as count FROM temp_loans WHERE session_id = ?",
        [$session_id]
    );
    return $result['count'] ?? 0;
}

/**
 * Remove item from cart
 */
function removeFromCart($temp_id) {
    $session_id = session_id();
    return delete(
        'temp_loans',
        'id = ? AND session_id = ?',
        [$temp_id, $session_id]
    );
}

/**
 * Clear cart
 */
function clearCart() {
    $session_id = session_id();
    return delete('temp_loans', 'session_id = ?', [$session_id]);
}

/**
 * Update cart item quantity
 */
function updateCartQuantity($temp_id, $quantity) {
    $session_id = session_id();
    
    if ($quantity <= 0) {
        return removeFromCart($temp_id);
    }
    
    return update(
        'temp_loans',
        ['quantity' => $quantity],
        'id = ? AND session_id = ?',
        [$temp_id, $session_id]
    );
}

/**
 * Process loan from cart
 */
function processLoan($borrower_id, $loan_date, $expected_return_date, $notes = '') {
    global $pdo;
    
    $cart_items = getCartItems();
    
    if (empty($cart_items)) {
        return ['success' => false, 'message' => 'Keranjang kosong'];
    }
    
    $total_items = array_sum(array_column($cart_items, 'quantity'));
    $user_id = getCurrentUserId();
    
    try {
        beginTransaction();
        
        // Generate loan code
        $loan_code = generateLoanCode();
        
        // Insert loan
        $loan_id = insert('loans', [
            'code' => $loan_code,
            'borrower_id' => $borrower_id,
            'loan_date' => $loan_date,
            'expected_return_date' => $expected_return_date,
            'total_items' => $total_items,
            'status' => 'dipinjam',
            'notes' => $notes,
            'created_by' => $user_id
        ]);
        
        // Insert loan details and update items
        foreach ($cart_items as $item) {
            // Check stock again
            $stock = fetchColumn(
                "SELECT quantity FROM items WHERE id = ?",
                [$item['item_id']]
            );
            
            if ($stock < $item['quantity']) {
                rollback();
                return [
                    'success' => false,
                    'message' => "Stok tidak cukup untuk: " . $item['name']
                ];
            }
            
            // Insert loan detail
            insert('loan_details', [
                'loan_id' => $loan_id,
                'item_id' => $item['item_id'],
                'quantity' => $item['quantity'],
                'condition_before' => $item['condition_before'],
                'status' => 'dipinjam',
                'notes' => $item['notes']
            ]);
            
            // Update item quantity and status
            update(
                'items',
                [
                    'quantity' => 'quantity - ' . $item['quantity'],
                    'status' => 'dipinjam'
                ],
                'id = ?',
                [$item['item_id']]
            );
        }
        
        // Clear cart
        clearCart();
        
        commit();
        
        return [
            'success' => true,
            'loan_id' => $loan_id,
            'code' => $loan_code
        ];
        
    } catch (Exception $e) {
        rollback();
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

/**
 * Check if item is in cart
 */
function isInCart($item_id) {
    $session_id = session_id();
    $result = fetchOne(
        "SELECT id FROM temp_loans WHERE session_id = ? AND item_id = ?",
        [$session_id, $item_id]
    );
    return $result ? true : false;
}

/**
 * Get item quantity in cart
 */
function getCartItemQuantity($item_id) {
    $session_id = session_id();
    $result = fetchOne(
        "SELECT quantity FROM temp_loans WHERE session_id = ? AND item_id = ?",
        [$session_id, $item_id]
    );
    return $result['quantity'] ?? 0;
}