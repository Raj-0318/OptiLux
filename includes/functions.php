<?php
function getProducts($conn, $limit = 8, $category_id = null) {
    $sql = "SELECT p.*, b.name as brand_name 
            FROM products p
            LEFT JOIN brands b ON p.brand_id = b.id";
            
    if ($category_id) {
        $sql .= " WHERE p.category_id = " . intval($category_id);
    }
    
    $sql .= " ORDER BY p.created_at DESC LIMIT " . intval($limit);
    
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
}

function getCartCount($conn, $user_id) {
    // Session fallback for guests could be added here
    if (!$user_id) return 0;
    
    $stmt = $conn->prepare("SELECT SUM(quantity) as total FROM cart WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $result = $stmt->fetch();
    return $result['total'] ?? 0;
}

function formatPrice($price) {
    return '₹' . number_format($price, 2);
}

function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}
?>


