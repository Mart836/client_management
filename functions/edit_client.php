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

// Fetch client by ID for editing
if (isset($_GET['id'])) {
    $clientId = (int)$_GET['id']; // Ensure it's an integer
    $clientQuery = "SELECT * FROM clients WHERE id = $clientId";
    $clientResult = $conn->query($clientQuery);

    if ($clientResult && $clientResult->num_rows > 0) {
        $client = $clientResult->fetch_assoc();
    } else {
        die("Client not found or invalid ID.");
    }
} else {
    die("No client ID provided.");
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
        // Redirect to index.php with the 'general' tab active
        header("Location: ../index.php");
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
    <link rel="stylesheet" href="assets/edit.css">
    <title>Edit Client</title>
    <style>
        /* Add your existing styles here */
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background: #ffffff;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        h2 {
            font-size: 1.8rem;
            margin-bottom: 1rem;
            color: #333;
            text-align: center;
        }

        .form {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        label {
            font-weight: bold;
            color: #555;
        }

        input[type="text"] {
            padding: 0.8rem;
            font-size: 1rem;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: 100%;
        }

        button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 0.8rem;
            font-size: 1rem;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #0056b3;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 1rem;
            color: #007bff;
            text-decoration: none;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Edit Client</h2>
        <form method="POST" class="form">
            <label for="clientName">Client Name:</label>
            <input type="text" name="clientName" value="<?= htmlspecialchars($client['name'] ?? '') ?>" required>

            <label for="clientCode">Client Code:</label>
            <input type="text" name="clientCode" value="<?= htmlspecialchars($client['client_code'] ?? '') ?>" required>

            <button type="submit">Update Client</button>
        </form>

        <a href="index.php?tab=general" class="back-link">Back to Client List</a>
    </div>
</body>
</html>
