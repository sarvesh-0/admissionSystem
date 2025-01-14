<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

// Include database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "admission_system";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if 'id' is passed in the URL
if (isset($_GET['id'])) {
    // Escape the input to prevent SQL injection
    $admission_id = $conn->real_escape_string($_GET['id']);

    // SQL to delete the record
    $sql = "DELETE FROM admission_form WHERE admission_id = '$admission_id'";
    
    if ($conn->query($sql) === TRUE) {
        echo "<script>
                alert('Record deleted successfully.');
                window.location.href = 'report.php';
              </script>";
        exit;
    } else {
        echo "Error: " . $conn->error;
    }
} else {
    echo "No ID provided.";
}

$conn->close();
?>
