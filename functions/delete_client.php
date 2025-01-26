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

// Handle client deletion
if (isset($_GET['id'])) {
    $clientId = $_GET['id'];

    // First, delete related contacts from the client_contact table
    $deleteContactsQuery = "DELETE FROM client_contact WHERE client_id = $clientId";
    if ($conn->query($deleteContactsQuery)) {
        // Now, delete the client from the clients table
        $deleteClientQuery = "DELETE FROM clients WHERE id = $clientId";
        if ($conn->query($deleteClientQuery)) {
            header("Location: ../index.php"); // Redirect to the client list
            exit();
        } else {
            echo "Error deleting client: " . $conn->error;
        }
    } else {
        echo "Error deleting related contacts: " . $conn->error;
    }
} else {
    echo "No client ID provided.";
}

// Close the database connection
$conn->close();
?>
