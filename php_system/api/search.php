<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(401);
    exit('Unauthorized');
}

require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$type = $_GET['type'] ?? '';
$search = $_GET['search'] ?? '';

if (!in_array($type, ['ofac', 'iibs'])) {
    http_response_code(400);
    exit('Invalid type');
}

$table_name = $type . '_data';

try {
    if (empty($search)) {
        // Get all records
        $query = "SELECT * FROM " . $table_name . " ORDER BY created_at DESC LIMIT 100";
        $stmt = $db->prepare($query);
    } else {
        // Search records
        $query = "SELECT * FROM " . $table_name . " WHERE 
                  source_media LIKE :search OR 
                  resolution LIKE :search OR 
                  individual_corporation_involved LIKE :search OR 
                  first_name LIKE :search OR 
                  middle_name LIKE :search OR 
                  last_name LIKE :search OR 
                  corporation_name_fullname LIKE :search OR 
                  alternate_name_alias_1 LIKE :search OR 
                  alternate_name_alias_2 LIKE :search 
                  ORDER BY created_at DESC LIMIT 100";
        $stmt = $db->prepare($query);
        $search_param = '%' . $search . '%';
        $stmt->bindParam(':search', $search_param);
    }

    $stmt->execute();
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($records) > 0) {
        echo '<div class="table-responsive">';
        echo '<table class="table table-striped table-hover">';
        echo '<thead class="table-dark">';
        echo '<tr>';
        echo '<th>ID</th>';
        echo '<th>Source Media</th>';
        echo '<th>Source Date</th>';
        echo '<th>Resolution</th>';
        echo '<th>Individual/Corp</th>';
        echo '<th>First Name</th>';
        echo '<th>Middle Name</th>';
        echo '<th>Last Name</th>';
        echo '<th>Name Ext</th>';
        echo '<th>Corporation Name</th>';
        echo '<th>Alias 1</th>';
        echo '<th>Alias 2</th>';
        echo '<th>Created</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';

        foreach ($records as $record) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($record['id']) . '</td>';
            echo '<td>' . htmlspecialchars($record['source_media']) . '</td>';
            echo '<td>' . htmlspecialchars($record['source_media_date']) . '</td>';
            echo '<td>' . htmlspecialchars(substr($record['resolution'], 0, 50)) . (strlen($record['resolution']) > 50 ? '...' : '') . '</td>';
            echo '<td>' . htmlspecialchars($record['individual_corporation_involved']) . '</td>';
            echo '<td>' . htmlspecialchars($record['first_name']) . '</td>';
            echo '<td>' . htmlspecialchars($record['middle_name']) . '</td>';
            echo '<td>' . htmlspecialchars($record['last_name']) . '</td>';
            echo '<td>' . htmlspecialchars($record['name_ext']) . '</td>';
            echo '<td>' . htmlspecialchars($record['corporation_name_fullname']) . '</td>';
            echo '<td>' . htmlspecialchars($record['alternate_name_alias_1']) . '</td>';
            echo '<td>' . htmlspecialchars($record['alternate_name_alias_2']) . '</td>';
            echo '<td>' . htmlspecialchars($record['created_at']) . '</td>';
            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table>';
        echo '</div>';
        echo '<div class="mt-3">';
        echo '<small class="text-muted">Showing ' . count($records) . ' records</small>';
        echo '</div>';
    } else {
        echo '<div class="alert alert-info">';
        echo '<h6>No records found</h6>';
        if (!empty($search)) {
            echo '<p>No records match your search criteria: <strong>' . htmlspecialchars($search) . '</strong></p>';
        } else {
            echo '<p>No records available in the database.</p>';
        }
        echo '</div>';
    }

} catch (PDOException $exception) {
    echo '<div class="alert alert-danger">';
    echo '<h6>Database Error</h6>';
    echo '<p>Error retrieving records: ' . htmlspecialchars($exception->getMessage()) . '</p>';
    echo '</div>';
}
?>
