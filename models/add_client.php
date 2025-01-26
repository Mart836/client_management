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

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate client name
    $clientName = $conn->real_escape_string($_POST['clientName']);

    if (empty($clientName)) {
        die("Error: Client name cannot be empty.");
    }

    // Insert the client into the database
    $sql = "INSERT INTO clients (name) VALUES ('$clientName')";

    if ($conn->query($sql)) {
        echo "Client added successfully!";
    } else {
        die("Error adding client: " . $conn->error);
    }
}

// Close the database connection
$conn->close();

// Redirect back to the main page
header("Location: ../index.php");
exit();
?>
