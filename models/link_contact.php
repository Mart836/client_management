<?php
// Database connection
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'clients'; // Update if different

$conn = new mysqli($host, $user, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate inputs
    $contactId = intval($_POST['contact']); // Ensure it's an integer
    $clientId = intval($_POST['client']);  // Ensure it's an integer

    if (empty($contactId) || empty($clientId)) {
        die("Error: Both Contact and Client must be selected.");
    }

    // Check if the contact is already linked to the client
    $checkQuery = "SELECT * FROM client_contact WHERE contact_id = $contactId AND client_id = $clientId";
    $result = $conn->query($checkQuery);

    if ($result->num_rows > 0) {
        die("Error: This contact is already linked to the selected client.");
    }

    // Insert the link into the `client_contact` table
    $linkQuery = "INSERT INTO client_contact (client_id, contact_id) VALUES ($clientId, $contactId)";
    if ($conn->query($linkQuery)) {
        echo "Contact successfully linked to the client!";
    } else {
        die("Error linking contact: " . $conn->error);
    }
}

// Close the database connection
$conn->close();

// Redirect back to the main page
header("Location: ../index.php");
exit();
?>
