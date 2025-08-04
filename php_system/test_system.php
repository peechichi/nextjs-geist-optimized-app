<?php
echo "<h2>System Test</h2>";

// Test 1: Check PHP version
echo "<h3>1. PHP Version Check</h3>";
if (version_compare(PHP_VERSION, '7.4.0') >= 0) {
    echo "<p style='color: green;'>✓ PHP Version: " . PHP_VERSION . " (Compatible)</p>";
} else {
    echo "<p style='color: red;'>✗ PHP Version: " . PHP_VERSION . " (Requires 7.4+)</p>";
}

// Test 2: Check required extensions
echo "<h3>2. Required Extensions</h3>";
$required_extensions = ['pdo', 'pdo_mysql', 'json'];
foreach ($required_extensions as $ext) {
    if (extension_loaded($ext)) {
        echo "<p style='color: green;'>✓ $ext extension loaded</p>";
    } else {
        echo "<p style='color: red;'>✗ $ext extension not loaded</p>";
    }
}

// Test 3: Check database connection
echo "<h3>3. Database Connection Test</h3>";
try {
    require_once 'config/database.php';
    $database = new Database();
    $db = $database->getConnection();
    
    if ($db) {
        echo "<p style='color: green;'>✓ Database connection successful</p>";
        
        // Test if tables exist
        $tables = ['ofac_data', 'iibs_data'];
        foreach ($tables as $table) {
            $stmt = $db->prepare("SHOW TABLES LIKE ?");
            $stmt->execute([$table]);
            if ($stmt->rowCount() > 0) {
                echo "<p style='color: green;'>✓ Table '$table' exists</p>";
            } else {
                echo "<p style='color: orange;'>⚠ Table '$table' not found - run init_database.php</p>";
            }
        }
    } else {
        echo "<p style='color: red;'>✗ Database connection failed</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Database error: " . $e->getMessage() . "</p>";
}

// Test 4: Check file permissions
echo "<h3>4. File Permissions</h3>";
$upload_dir = 'uploads';
if (!is_dir($upload_dir)) {
    if (mkdir($upload_dir, 0777, true)) {
        echo "<p style='color: green;'>✓ Created uploads directory</p>";
    } else {
        echo "<p style='color: red;'>✗ Cannot create uploads directory</p>";
    }
} else {
    echo "<p style='color: green;'>✓ Uploads directory exists</p>";
}

if (is_writable($upload_dir)) {
    echo "<p style='color: green;'>✓ Uploads directory is writable</p>";
} else {
    echo "<p style='color: red;'>✗ Uploads directory is not writable</p>";
}

// Test 5: Check sample files
echo "<h3>5. Sample Files</h3>";
$sample_files = ['sample_data/sample_ofac.csv', 'sample_data/sample_iibs.csv'];
foreach ($sample_files as $file) {
    if (file_exists($file)) {
        echo "<p style='color: green;'>✓ $file exists</p>";
    } else {
        echo "<p style='color: red;'>✗ $file not found</p>";
    }
}

echo "<hr>";
echo "<h3>Next Steps:</h3>";
echo "<ol>";
echo "<li>If database tables don't exist, run <a href='init_database.php'>init_database.php</a></li>";
echo "<li>Access the system at <a href='index.php'>index.php</a></li>";
echo "<li>Login with username: <strong>admin</strong>, password: <strong>admin123</strong></li>";
echo "</ol>";
?>
