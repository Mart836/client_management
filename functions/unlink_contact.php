<?php
// Database connection 
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'clients'; 

$conn = new mysqli($host, $user, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the client_id and contact_id from the URL
$client_id = isset($_GET['client_id']) ? (int)$_GET['client_id'] : 0;
$contact_id = isset($_GET['contact_id']) ? (int)$_GET['contact_id'] : 0;

if ($client_id > 0 && $contact_id > 0) {
    // Perform the unlinking process (delete from client_contact table)
    $query = "DELETE FROM client_contact WHERE client_id = ? AND contact_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $client_id, $contact_id);
    
    if ($stmt->execute()) {
        echo "<script>alert('Contact unlinked successfully.'); window.location.href = '../index.php#contacts';</script>";
    } else {
        echo "<script>alert('Error unlinking contact: " . $stmt->error . "'); window.location.href = '../index.php#contacts';</script>";
    }
    $stmt->close();
    
} else {
    echo "Invalid parameters.";
}

$conn->close();
?>
