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

// Check if the request is to delete all contacts
$delete_all = isset($_GET['delete_all']) && $_GET['delete_all'] == 1;

if ($delete_all) {
    // Delete all contacts
    $query = "DELETE FROM contacts";
    $stmt = $conn->prepare($query);

    if ($stmt->execute()) {
        // Redirect to the contacts page after successful deletion
        header("Location: ../index.php?deleted=1#contacts");
        exit;
    } else {
        echo "Error deleting all contacts: " . $stmt->error;
    }

    $stmt->close();
} elseif ($contact_id > 0) {
    // Delete a single contact
    $query = "DELETE FROM contacts WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $contact_id);

    if ($stmt->execute()) {
        // Redirect to the contacts page after successful deletion
        header("Location: ../index.php?deleted=1#contacts");
        exit;
    } else {
        echo "Error deleting contact: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Invalid request.";
}

$conn->close();
?>