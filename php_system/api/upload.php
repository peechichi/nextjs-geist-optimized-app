<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

require_once '../config/database.php';

// Simple Excel reader function (basic CSV-like functionality)
function readExcelFile($filePath) {
    $data = [];
    
    // For now, we'll handle CSV files or simple Excel files
    // In a production environment, you would use PHPSpreadsheet library
    
    $extension = pathinfo($filePath, PATHINFO_EXTENSION);
    
    if ($extension === 'csv') {
        if (($handle = fopen($filePath, "r")) !== FALSE) {
            $headers = fgetcsv($handle, 1000, ",");
            while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if (count($row) === count($headers)) {
                    $data[] = array_combine($headers, $row);
                }
            }
            fclose($handle);
        }
    } else {
        // For Excel files, we'll provide a simple implementation
        // Note: This is a basic implementation. For full Excel support, install PHPSpreadsheet
        echo json_encode(['success' => false, 'message' => 'Please convert Excel file to CSV format or install PHPSpreadsheet library for full Excel support']);
        exit();
    }
    
    return $data;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

$type = $_POST['type'] ?? '';
if (!in_array($type, ['ofac', 'iibs'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid type']);
    exit();
}

if (!isset($_FILES['excel_file']) || $_FILES['excel_file']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'No file uploaded or upload error']);
    exit();
}

$uploadedFile = $_FILES['excel_file'];
$allowedExtensions = ['xlsx', 'xls', 'csv'];
$fileExtension = strtolower(pathinfo($uploadedFile['name'], PATHINFO_EXTENSION));

if (!in_array($fileExtension, $allowedExtensions)) {
    echo json_encode(['success' => false, 'message' => 'Invalid file type. Please upload Excel or CSV file']);
    exit();
}

// Create uploads directory if it doesn't exist
$uploadDir = '../uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$uploadPath = $uploadDir . uniqid() . '_' . $uploadedFile['name'];

if (!move_uploaded_file($uploadedFile['tmp_name'], $uploadPath)) {
    echo json_encode(['success' => false, 'message' => 'Failed to save uploaded file']);
    exit();
}

try {
    $database = new Database();
    $db = $database->getConnection();
    
    if (!$db) {
        throw new Exception('Database connection failed');
    }
    
    $data = readExcelFile($uploadPath);
    
    if (empty($data)) {
        throw new Exception('No data found in the uploaded file');
    }
    
    $table_name = $type . '_data';
    $inserted = 0;
    $errors = [];
    
    // Expected column mappings (case-insensitive)
    $columnMappings = [
        'source_media' => ['source media', 'source_media'],
        'source_media_date' => ['source media date', 'source_media_date'],
        'resolution' => ['resolution'],
        'individual_corporation_involved' => ['individual / corporation involved', 'individual_corporation_involved', 'individual/corporation involved'],
        'first_name' => ['first name', 'first_name'],
        'middle_name' => ['middle name', 'middle_name'],
        'last_name' => ['last name', 'last_name'],
        'name_ext' => ['name ext', 'name_ext'],
        'corporation_name_fullname' => ['corporation name / fullname', 'corporation_name_fullname', 'corporation name/fullname'],
        'alternate_name_alias_1' => ['alternate name / alias', 'alternate_name_alias_1', 'alternate name/alias', 'alternate name alias 1'],
        'alternate_name_alias_2' => ['alternate name / alias 2', 'alternate_name_alias_2', 'alternate name/alias 2', 'alternate name alias 2']
    ];
    
    // Prepare insert statement
    $query = "INSERT INTO " . $table_name . " (
        source_media, source_media_date, resolution, individual_corporation_involved,
        first_name, middle_name, last_name, name_ext, corporation_name_fullname,
        alternate_name_alias_1, alternate_name_alias_2
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $db->prepare($query);
    
    foreach ($data as $rowIndex => $row) {
        try {
            // Map columns (case-insensitive)
            $mappedRow = [];
            $rowKeys = array_map('strtolower', array_keys($row));
            
            foreach ($columnMappings as $dbColumn => $possibleNames) {
                $mappedRow[$dbColumn] = '';
                foreach ($possibleNames as $possibleName) {
                    $key = array_search(strtolower($possibleName), $rowKeys);
                    if ($key !== false) {
                        $originalKey = array_keys($row)[$key];
                        $mappedRow[$dbColumn] = trim($row[$originalKey]);
                        break;
                    }
                }
            }
            
            // Convert date format if needed
            $date = $mappedRow['source_media_date'];
            if (!empty($date)) {
                $dateObj = DateTime::createFromFormat('m/d/Y', $date);
                if (!$dateObj) {
                    $dateObj = DateTime::createFromFormat('Y-m-d', $date);
                }
                if (!$dateObj) {
                    $dateObj = DateTime::createFromFormat('d/m/Y', $date);
                }
                if ($dateObj) {
                    $mappedRow['source_media_date'] = $dateObj->format('Y-m-d');
                } else {
                    $mappedRow['source_media_date'] = null;
                }
            } else {
                $mappedRow['source_media_date'] = null;
            }
            
            $stmt->execute([
                $mappedRow['source_media'],
                $mappedRow['source_media_date'],
                $mappedRow['resolution'],
                $mappedRow['individual_corporation_involved'],
                $mappedRow['first_name'],
                $mappedRow['middle_name'],
                $mappedRow['last_name'],
                $mappedRow['name_ext'],
                $mappedRow['corporation_name_fullname'],
                $mappedRow['alternate_name_alias_1'],
                $mappedRow['alternate_name_alias_2']
            ]);
            
            $inserted++;
            
        } catch (Exception $e) {
            $errors[] = "Row " . ($rowIndex + 2) . ": " . $e->getMessage();
        }
    }
    
    // Clean up uploaded file
    unlink($uploadPath);
    
    $message = "Successfully imported $inserted records";
    if (!empty($errors)) {
        $message .= ". Errors: " . implode(", ", array_slice($errors, 0, 5));
        if (count($errors) > 5) {
            $message .= " and " . (count($errors) - 5) . " more errors";
        }
    }
    
    echo json_encode([
        'success' => true,
        'message' => $message,
        'inserted' => $inserted,
        'errors' => count($errors)
    ]);
    
} catch (Exception $e) {
    // Clean up uploaded file
    if (file_exists($uploadPath)) {
        unlink($uploadPath);
    }
    
    echo json_encode([
        'success' => false,
        'message' => 'Upload failed: ' . $e->getMessage()
    ]);
}
?>
