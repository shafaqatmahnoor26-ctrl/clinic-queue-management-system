<?php
include 'db.php';
header('Content-Type: application/json');

$today = date('Y-m-d');

// Fetch today's active token
$sql = "SELECT token_number FROM tokens WHERE status = 'calling' AND created_at = '$today' ORDER BY id DESC LIMIT 1";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode(['token' => $row['token_number']]);
} else {
    echo json_encode(['token' => null]);
}
?>