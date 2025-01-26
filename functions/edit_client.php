<?php
// Database connection
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'clients'; // Update if your DB name is different

$conn = new mysqli($host, $user, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch client by ID for editing
if (isset($_GET['id'])) {
    $clientId = (int)$_GET['id']; // Ensure it's an integer to avoid SQL injection
    $clientQuery = "SELECT * FROM clients WHERE id = $clientId";
    $clientResult = $conn->query($clientQuery);

    if ($clientResult->num_rows > 0) {
        $client = $clientResult->fetch_assoc();
    } else {
        die("Client not found");
    }
}

// Handle form submission for updating client
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $clientName = $_POST['clientName'];
    $clientCode = $_POST['clientCode'];

    // Sanitize and escape inputs
    $clientName = $conn->real_escape_string($clientName);
    $clientCode = $conn->real_escape_string($clientCode);

    // Update query to modify client data
    $updateQuery = "UPDATE clients SET name = '$clientName', client_code = '$clientCode' WHERE id = $clientId";

    if ($conn->query($updateQuery)) {
        header("Location: index.php"); // Redirect to the client list after updating
        exit();
    } else {
        echo "Error updating client: " . $conn->error;
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/styles.css">
    <title>Edit Client</title>
</head>
<body>

    <div class="container">
        <h2>Edit Client</h2>
        <form method="POST" class="form">
            <label for="clientName">Client Name:</label>
            <input type="text" name="clientName" value="<?= htmlspecialchars($client['name']) ?>" required>

            <label for="clientCode">Client Code:</label>
            <input type="text" name="clientCode" value="<?= htmlspecialchars($client['client_code']) ?>" required>

            <button type="submit">Update Client</button>
        </form>

        <a href="index.php" class="back-link">Back to Client List</a>
    </div>

</body>
</html>
