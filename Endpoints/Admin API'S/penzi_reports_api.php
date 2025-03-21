<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json"); // Ensure response is JSON

// Handle CORS preflight requests
if ($_SERVER['REQUEST_METHOD'] == "OPTIONS") {
    http_response_code(200);
    exit();
}

require $_SERVER['DOCUMENT_ROOT'] . "/PENZI/config/db.php";

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'user_growth':
        sendJsonResponse(getUserGrowth($conn));
        break;
    case 'match_performance':
        sendJsonResponse(getMatchPerformance($conn));
        break;
    case 'top_locations':
        sendJsonResponse(getTopLocations($conn));
        break;
    case 'export':
        exportData($conn);
        break;
    default:
        sendJsonResponse(['error' => 'Invalid action'], 400);
}

// Helper function to send JSON response
function sendJsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode(["success" => true, "data" => $data]);
    exit();
}

// Fetch user growth trends
function getUserGrowth($conn) {
    $query = "SELECT DATE(RegistrationDate) as date, COUNT(UserID) as total 
              FROM users 
              GROUP BY date 
              ORDER BY date DESC 
              LIMIT 7";

    $result = $conn->query($query);
    if (!$result) {
        return ['error' => $conn->error];
    }

    return $result->fetch_all(MYSQLI_ASSOC);
}

// Fetch match performance data
function getMatchPerformance($conn) {
    $query = "SELECT DATE(m.RequestDate) as date, 
                     COUNT(m.MatchRequestID) as requests, 
                     COUNT(u.ConfirmationID) as confirmations 
              FROM matchrequests m 
              LEFT JOIN userconfirmations u 
              ON m.MatchRequestID = u.request_id 
              GROUP BY date 
              ORDER BY date DESC 
              LIMIT 7";

    $result = $conn->query($query);
    
    if (!$result) {
        return ['error' => $conn->error];
    }

    return $result->fetch_all(MYSQLI_ASSOC);
}

// Fetch top locations
function getTopLocations($conn) {
    $query = "SELECT town, COUNT(*) AS user_count 
              FROM users 
              GROUP BY town 
              ORDER BY user_count DESC 
              LIMIT 5";

    $result = $conn->query($query);
    
    if (!$result) {
        return ['error' => $conn->error];
    }

    return $result->fetch_all(MYSQLI_ASSOC);
}

// Export Data as CSV
function exportData($conn) {
    $allowedTables = ['users', 'matchrequests', 'userconfirmations']; // Allowed tables
    $table = $_GET['table'] ?? 'users'; // Default to 'users'

    if (!in_array($table, $allowedTables)) {
        sendJsonResponse(['error' => 'Invalid table name'], 400);
    }

    $filename = "export_{$table}_" . date('Y-m-d') . ".csv";

    header('Content-Type: text/csv');
    header("Content-Disposition: attachment; filename=\"$filename\"");

    $query = "SELECT * FROM $table";
    $result = $conn->query($query);

    if (!$result) {
        sendJsonResponse(['error' => $conn->error], 500);
    }

    $output = fopen('php://output', 'w');

    if ($result->num_rows > 0) {
        fputcsv($output, array_keys($result->fetch_assoc())); // Headers
        $result->data_seek(0);
        
        while ($row = $result->fetch_assoc()) {
            fputcsv($output, $row);
        }
    }

    fclose($output);
    exit;
}
?>
