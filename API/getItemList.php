<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

include 'conn.php';

// Get POST data (support JSON body or Form Data)
$input = json_decode(file_get_contents('php://input'), true);

$indexParam = isset($input['index']) ? $input['index'] : (isset($_POST['index']) ? $_POST['index'] : 0);
$countParam = isset($input['count']) ? intval($input['count']) : (isset($_POST['count']) ? intval($_POST['count']) : 10);
$orderParam = isset($input['order']) ? intval($input['order']) : (isset($_POST['order']) ? intval($_POST['order']) : 1);

// 1. Get Total Count (needed for 'last' keyword and boundary checks)
$countSql = "SELECT COUNT(*) as total FROM items";
$countResult = $conn->query($countSql);
$totalRows = 0;
if ($countResult) {
    $row = $countResult->fetch_assoc();
    $totalRows = intval($row['total']);
}

// 2. Determine Target Index (0-based)
$targetIndex = 0;
if (strtolower((string)$indexParam) === 'last') {
    $targetIndex = $totalRows - 1;
} else {
    $targetIndex = intval($indexParam);
}

// Validate bounds
if ($targetIndex < 0) $targetIndex = 0;
if ($targetIndex >= $totalRows) $targetIndex = $totalRows - 1;

// Handle empty DB case
if ($totalRows === 0) {
    echo json_encode([]);
    exit;
}

// 3. Calculate SQL OFFSET and LIMIT based on Order
$offset = 0;
$limit = $countParam;

if ($orderParam === 1) {
    // Forward: Start at targetIndex, take count
    // Example: (4, 3, 1) -> Start at 4, take 3 -> Indices 4, 5, 6
    $offset = $targetIndex;
} else {
    // Backward: End at targetIndex, take count
    // Example: (9, 4, -1) -> End at 9, take 4 -> Indices 6, 7, 8, 9
    // Range: [targetIndex - count + 1, targetIndex]
    $calculatedOffset = $targetIndex - $countParam + 1;
    
    if ($calculatedOffset < 0) {
        // Requested more items than exist before the index
        // Example: Index 2, Count 5 -> Range [-2, 2] -> Actual [0, 2]
        $limit = $countParam + $calculatedOffset; // 5 + (-2) = 3
        $offset = 0;
    } else {
        $offset = $calculatedOffset;
    }
}

// Safety check
if ($limit < 0) $limit = 0;

// 4. Fetch Data
// Sorted by Views (Popularity) DESC, then ID ASC for stability
$sql = "SELECT i.*, c.full_name as creator_name 
        FROM items i 
        LEFT JOIN contributors c ON i.created_by = c.id
        ORDER BY i.views DESC, i.id ASC
        LIMIT $limit OFFSET $offset";

$result = $conn->query($sql);

$items = array();

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        
        // Format Tags
        $tagsArray = [];
        if (!empty($row['tags'])) {
            $tagsArray = array_map('trim', explode(',', $row['tags']));
        }

        // Calculate Trend
        $price = floatval($row['price']);
        $prevPrice = floatval($row['previous_price']);
        $change = abs($price - $prevPrice);
        
        $trend = 'neutral';
        if ($price > $prevPrice) {
            $trend = 'up';
        } elseif ($price < $prevPrice) {
            $trend = 'down';
        }

        $item = [
            'id' => intval($row['id']),
            'name' => $row['name'],
            'category' => $row['category'],
            'unit' => $row['unit'],
            'price' => $price,
            'previous_price' => $prevPrice,
            'change' => round($change, 2),
            'trend' => $trend,
            'icon' => $row['icon'],
            'created_by' => $row['creator_name'] ? $row['creator_name'] : 'Unknown', 
            'tags' => $tagsArray,
            'status' => $row['status'],
            'views' => intval($row['views']),
            'last_updated' => $row['last_updated']
        ];
        
        array_push($items, $item);
    }
}

// Return JSON
echo json_encode($items, JSON_PRETTY_PRINT);

$conn->close();
?>
