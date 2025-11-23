<?php
include 'API/conn.php';

function checkTable($conn, $tableName) {
    echo "Table: $tableName\n";
    $result = $conn->query("DESCRIBE $tableName");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            echo " - " . $row['Field'] . " (" . $row['Type'] . ")\n";
        }
    } else {
        echo " - Error: " . $conn->error . "\n";
    }
    echo "\n";
}

checkTable($conn, 'items');
checkTable($conn, 'contributors');

$conn->close();
?>
