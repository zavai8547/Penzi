<?php 
header('Content-Type: application/json');
require '../config/db.php'; 

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'user_growth':
        echo json_encode(getUserGrowth($conn));
        break;
    case 'match_performance':
        echo json_encode(getMatchPerformance($conn));
        break;
    case 'top_locations':
        echo json_encode(getTopLocations($conn));
        break;
    case 'export':
        exportData($conn);
        break;
    default:
        echo json_encode(['error' => 'Invalid action']);
}

// Fetch user growth trends
function getUserGrowth($conn) {
    $query = "SELECT DATE(created_at) as date, COUNT(id) as total 
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
    $query = "SELECT DATE(m.request_date) as date, 
                     COUNT(m.id) as requests, 
                     COUNT(u.id) as confirmations 
              FROM matchrequests m 
              LEFT JOIN userconfirmations u 
              ON DATE(m.request_date) = DATE(u.confirmation_date)
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
    $query = "SELECT location, COUNT(id) as total 
              FROM users 
              GROUP BY location 
              ORDER BY total DESC 
              LIMIT 5";

    $result = $conn->query($query);
    if (!$result) {
        return ['error' => $conn->error];
    }

    return $result->fetch_all(MYSQLI_ASSOC);
}

// Export Data as CSV
function exportData($conn) {
    $table = $_GET['table'] ?? 'users'; // Default to 'users' if not specified
    $filename = "export_{$table}_" . date('Y-m-d') . ".csv";

    header('Content-Type: text/csv');
    header("Content-Disposition: attachment; filename=\"$filename\"");

    $query = "SELECT * FROM $table";
    $result = $conn->query($query);

    if (!$result) {
        echo json_encode(['error' => $conn->error]);
        exit;
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
