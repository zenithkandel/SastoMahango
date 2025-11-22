<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *"); // Allow requests from any origin (for development)

include 'conn.php';

// Fetch items with creator's name
$sql = "SELECT i.*, c.full_name as creator_name 
        FROM items i 
        LEFT JOIN contributors c ON i.created_by = c.id
        ORDER BY i.views DESC"; // Default sort by popularity

$result = $conn->query($sql);

$items = array();

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        
        // 1. Format Tags: Convert comma-separated string to array
        $tagsArray = [];
        if (!empty($row['tags'])) {
            // Explode by comma and trim whitespace from each tag
            $tagsArray = array_map('trim', explode(',', $row['tags']));
        }

        // 2. Calculate Trend and Change (since we removed them from DB columns)
        $price = floatval($row['price']);
        $prevPrice = floatval($row['previous_price']);
        $change = abs($price - $prevPrice);
        
        $trend = 'neutral';
        if ($price > $prevPrice) {
            $trend = 'up';
        } elseif ($price < $prevPrice) {
            $trend = 'down';
        }

        // 3. Construct the Item Object
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
            // Replace created_by ID with Name
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
