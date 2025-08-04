<?php
require_once 'config/database.php';

echo "<h2>Database Initialization</h2>";

$database = new Database();
if ($database->createDatabase()) {
    echo "<p style='color: green;'>✓ Database and tables created successfully!</p>";
    echo "<p>Database: data_management_system</p>";
    echo "<p>Tables created:</p>";
    echo "<ul>";
    echo "<li>ofac_data - for OFAC records</li>";
    echo "<li>iibs_data - for IIBS records</li>";
    echo "</ul>";
    echo "<p><a href='login.php'>Go to Login Page</a></p>";
} else {
    echo "<p style='color: red;'>✗ Database creation failed!</p>";
    echo "<p>Please check your MariaDB/MySQL connection settings in config/database.php</p>";
}
?>
