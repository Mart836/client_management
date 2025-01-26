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

// Fetch clients and their contact counts
$clientsQuery = "SELECT c.id, c.name, c.client_code, COUNT(cc.contact_id) AS contact_count 
                 FROM clients c 
                 LEFT JOIN client_contact cc ON c.id = cc.client_id 
                 GROUP BY c.id 
                 ORDER BY c.name ASC";
$clients = $conn->query($clientsQuery);
if (!$clients) {
    die("Invalid query (Clients): " . $conn->error);
}

// Fetch contacts with linked client name
$contactsQuery = "SELECT con.id, con.first_name, con.last_name, con.email, 
                         cc.client_id, cl.name AS client_name
                  FROM contacts con
                  LEFT JOIN client_contact cc ON con.id = cc.contact_id
                  LEFT JOIN clients cl ON cc.client_id = cl.id
                  ORDER BY con.last_name, con.first_name ASC";
$contacts = $conn->query($contactsQuery);
if (!$contacts) {
    die("Invalid query (Contacts): " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client-Contact Management</title>
    <!-- External CSS -->
    <link rel="stylesheet" href="assets/styles.css">
    <!-- jQuery Library -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="tabs">
        <div class="tab active" data-tab="general">General</div>
        <div class="tab" data-tab="contacts">Contacts</div>
    </div>

    <!-- General Tab -->
    <div id="general" class="content active">
        <h2>Clients</h2>
        <button onclick="toggleForm('addClientForm')">Add New Client</button>
        <div id="addClientForm" class="form-section">
            <form method="POST" action="models/add_client.php">
                <label for="clientName">Client Name:</label>
                <input type="text" name="clientName" required>
                <button type="submit">Save Client</button>
            </form>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Client Name</th>
                    <th>Client Code</th>
                    <th class="center">No. of Contacts Linked</th>
                    <th class="center"></th> 
                </tr>
            </thead>
            <tbody>
                <?php if ($clients->num_rows > 0): ?>
                    <?php while ($row = $clients->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td><?= htmlspecialchars($row['client_code']) ?></td>
                            <td class="center"><?= htmlspecialchars($row['contact_count']) ?></td>
                            <td class="center">
                                <a href="functions/edit_client.php?id=<?= $row['id'] ?>">Edit</a> <!-- Edit Link -->
                                <a href="functions/delete_client.php?id=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this client?')">Delete</a> <!-- Delete Link -->
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">No client(s) found.</td> 
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Contacts Tab -->
    <div id="contacts" class="content">
        <h2>Contacts</h2>
        <button onclick="toggleForm('addContactForm')">Add New Contact</button>
        <div id="addContactForm" class="form-section">
            <form method="POST" action="models/add_contact.php">
                <label for="firstName">First Name:</label>
                <input type="text" name="firstName" required>
                <label for="lastName">Last Name:</label>
                <input type="text" name="lastName" required>
                <label for="email">Email:</label>
                <input type="email" name="email" required>
                <button type="submit">Save Contact</button>
            </form>
        </div>

        <h3>Link Contact to Client</h3>
        <form method="POST" action="models/link_contact.php">
            <label for="contact">Contact:</label>
            <select name="contact" required>
                <option value="">Select Contact</option>
                <?php
                // Fetch contacts from the database
                $contactQuery = "SELECT * FROM contacts";
                $contactResult = $conn->query($contactQuery);

                // Display contacts in the dropdown
                while ($contact = $contactResult->fetch_assoc()) {
                    echo '<option value="' . $contact['id'] . '">' . htmlspecialchars($contact['first_name']) . ' ' . htmlspecialchars($contact['last_name']) . '</option>';
                }
                ?>
            </select>
            <label for="client">Client:</label>
            <select name="client" required>
                <option value="">Select Client</option>
                <?php
                // Fetch clients from the database
                $clientQuery = "SELECT * FROM clients";
                $clientResult = $conn->query($clientQuery);

                // Display clients in the dropdown
                while ($client = $clientResult->fetch_assoc()) {
                    echo '<option value="' . $client['id'] . '">' . htmlspecialchars($client['name']) . '</option>';
                }
                ?>
            </select>
            <button type="submit">Link</button>
        </form>

        <table>
            <thead>
                <tr>
                    <th>Contact Name</th>
                    <th>Email</th>
                    <th>Linked Client</th>
                    <th class="center"></th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Display each contact in the table
                if ($contacts->num_rows > 0) {
                    while ($contact = $contacts->fetch_assoc()) {
                        // Create the full name
                        $fullName = $contact['first_name'] . ' ' . $contact['last_name'];
                        $clientName = isset($contact['client_name']) ? $contact['client_name'] : 'No Client Linked';

                        echo '<tr>';
                        echo '<td>' . htmlspecialchars($fullName) . '</td>';
                        echo '<td>' . htmlspecialchars($contact['email']) . '</td>';
                        echo '<td>' . htmlspecialchars($clientName) . '</td>';
                        echo '<td class="center">';
                        echo '<a href="functions/delete_contact.php?id=' . $contact['id'] . '" onclick="return confirm(\'Are you sure you want to delete this contact?\')">Delete</a>';

                        // Show Unlink option if the contact is linked to a client
                        if (isset($contact['client_id']) && $contact['client_id'] !== NULL) {
                            echo ' <a href="functions/unlink_contact.php?client_id=' . $contact['client_id'] . '&contact_id=' . $contact['id'] . '">Unlink</a>';
                        }

                        echo '</td>';
                        echo '</tr>';
                    }
                } else {
                    echo '<tr><td colspan="4">No contacts found.</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>

    <script>
        document.querySelectorAll('.tab').forEach(tab => {
            tab.addEventListener('click', () => {
                document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
                document.querySelectorAll('.content').forEach(c => c.classList.remove('active'));
                tab.classList.add('active');
                document.getElementById(tab.dataset.tab).classList.add('active');
            });
        });

        function toggleForm(formId) {
            const form = document.getElementById(formId);
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
        }
    </script>
</body>
</html>
