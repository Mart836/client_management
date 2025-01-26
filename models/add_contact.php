<?php
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'clients'; // Update if different

$conn = new mysqli($host, $user, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = $conn->real_escape_string($_POST['firstName']);
    $lastName = $conn->real_escape_string($_POST['lastName']);
    $email = $conn->real_escape_string($_POST['email']);

    // Ensure email is unique
    $checkEmailQuery = "SELECT id FROM contacts WHERE email = '$email'";
    $result = $conn->query($checkEmailQuery);

    if ($result->num_rows > 0) {
        die("Error: A contact with this email already exists.");
    }

    // Insert contact into the database
    $sql = "INSERT INTO contacts (first_name, last_name, email) VALUES ('$firstName', '$lastName', '$email')";
    if ($conn->query($sql)) {
        echo "Contact added successfully!";
    } else {
        echo "Error: " . $conn->error;
    }
}

$conn->close();
header("Location: ../index.php"); // Redirect back to the main page
exit();
?>
