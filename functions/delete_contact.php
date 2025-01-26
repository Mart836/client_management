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

// Get the contact ID from the URL
$contact_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($contact_id > 0) {
    // Delete the contact from the contacts table
    $query = "DELETE FROM contacts WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $contact_id);

    if ($stmt->execute()) {
        // Redirect to the contacts page after successful deletion
        header("Location: ../index.php#contacts");
        exit;
    } else {
        echo "Error deleting contact: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Invalid contact ID.";
}

$conn->close();
?>
