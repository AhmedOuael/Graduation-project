<?php
session_start();

// Check if the user is an maintenance
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'maintenance') {
    header('Location: login.php');
    exit;
}

require_once '../includes/db.php';

// Read machine
$stmt = $conn->prepare("SELECT * FROM machine");
$stmt->execute();
$machine = $stmt->fetchAll(PDO::FETCH_ASSOC);



// Add a new machine
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_machine'])) {
    
    $nom = $_POST['nom'];
    $description = $_POST['description'];
    $status = $_POST['status'];
    $addedBy = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO machine (nom, description, status, addedBy) VALUES (:nom, :description, :status, :addedBy)");
    $stmt->bindParam(':nom', $nom);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':addedBy', $addedBy);
    $stmt->bindParam(':addedAt', $addedAt);

    if ($stmt->execute()) {
        $success_message = "machine added successfully.";
        header("Location: ".$_SERVER['PHP_SELF']);
        exit;
    } else {
        $error_message = "Error adding machine:" . $stmt->errorInfo()[2];
        
    }
}

// Update a machine
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_machine'])) {
    $id = $_POST['id'];
    $nom = $_POST['nom'];
    $description = $_POST['description'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE machine SET nom = :nom, description = :description, status = :status WHERE id = :id");
    $stmt->bindParam(':nom', $nom);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':id', $id);

    if ($stmt->execute()) {
        $success_message = "machine updated successfully.";
        header("Location: ".$_SERVER['PHP_SELF']);
        exit;
    } else {
        $error_message = "Error updating machine.";
    }
}

// Delete a machine
if (isset($_GET['delete_machine'])) {
    $id = $_GET['delete_machine'];

    $stmt = $conn->prepare("DELETE FROM machine WHERE id = :id");
    $stmt->bindParam(':id', $id);

    if ($stmt->execute()) {
        $success_message = "machine deleted successfully.";
        header("Location: ".$_SERVER['PHP_SELF']);
        exit;
    } else {
        $error_message = "Error deleting machine.";
    }
}

?>



<!-- HTML code to display machine, forms, and messages -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Machine Management</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2, h3 {
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .form-container {
            margin-bottom: 20px;
            background-color: #f2f2f2;
            padding: 20px;
            border-radius: 8px;
        }

        .form-container input[type="text"], 
        .form-container textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            transition: border-color 0.3s ease;
        }

        .form-container input[type="text"]:focus, 
        .form-container textarea:focus {
            border-color: #4CAF50;
            outline: none;
        }

        .form-container input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .form-container input[type="submit"]:hover {
            background-color: #45a049;
        }

        .message {
            margin-bottom: 20px;
            padding: 12px;
            border-radius: 4px;
            font-weight: bold;
        }

        .message.success {
            background-color: #dff0d8;
            color: #3c763d;
        }

        .message.error {
            background-color: #f2dede;
            color: #a94442;
        }

        .action-links {
            margin-top: 10px;
        }

        .action-links a {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 4px;
            text-decoration: none;
        }

        .action-links a.edit {
            background-color: #ccc;
            color: #333;
        }

        .action-links a.delete {
            background-color: #FF0000;
            color: #fff;
        }

        .hidden {
            display: none;
        }
/* logout css */
.logout-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #dc3545;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }

        .logout-nutton:hover {
            background-color: #c82333;
        }

    </style>
</head>
<body>
<?php include('../includes/header.php'); ?>
<div class="container">
    <h2>Machine Management</h2>

    <!-- Success or error message -->
    <?php if (isset($success_message)): ?>
        <div class="message success"><?php echo $success_message; ?></div>
    <?php elseif (isset($error_message)): ?>
        <div class="message error"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <!-- Add Machine Form -->
    <div class="form-container">
        <h3>Add Machine</h3>
        <form action="" method="post">
            <input type="text" name="nom" placeholder="Name" required>
            <textarea name="description" placeholder="Description" rows="4" required></textarea>
            <input type="text" name="status" placeholder="Status" required>
            <input type="submit" name="add_machine" value="Add Machine">
        </form>
    </div>

    <!-- Edit Machine Form (Initially hidden) -->
    <div id="editForm" class="form-container hidden">
        <h3>Edit Machine</h3>
        <form action="" method="post" onsubmit="return handleEditFormSubmit()">
            <input type="hidden" name="id" id="editId">
            <input type="text" name="nom" id="editNom" placeholder="Name" required>
            <textarea name="description" id="editDescription" placeholder="Description" rows="4" required></textarea>
            <input type="text" name="status" id="editStatus" placeholder="Status" required>
            <input type="submit" name="update_machine" value="Update Machine">
        </form>
    </div>

    <!-- Machine Table -->
    <h3>Machines</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Description</th>
                <th>Status</th>
                <th>addedBy</th>
                <th>addedAt</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($machine as $mach): ?>
                <tr>
                    <td><?php echo htmlspecialchars($mach['id']); ?></td>
                    <td><?php echo htmlspecialchars($mach['nom']); ?></td>
                    <td><?php echo htmlspecialchars($mach['description']); ?></td>
                    <td><?php echo htmlspecialchars($mach['status']); ?></td>
                    <td><?php echo htmlspecialchars($mach['addedBy']); ?></td>
                    <td><?php echo htmlspecialchars($mach['addedAt']); ?></td>
                    <td class="action-links">
                        <a class="edit" href="#" onclick="showEditForm('<?php echo $mach['id']; ?>', '<?php echo htmlspecialchars(addslashes($mach['nom'])); ?>', '<?php echo htmlspecialchars(addslashes($mach['description'])); ?>', '<?php echo htmlspecialchars(addslashes($mach['status'])); ?>')">Edit</a>
                        <a class="delete" href="?delete_machine=<?php echo $mach['id']; ?>" onclick="return confirm('Are you sure you want to delete this machine?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<div style="text-align: center; margin-top: 10px; margin-bottom: 90px;">
    <button class="logout-button">Logout</button>
</div>
<script>
function showEditForm(id, nom, description, status) {
    document.getElementById('editForm').classList.remove('hidden');
    document.getElementById('editId').value = id;
    document.getElementById('editNom').value = nom;
    document.getElementById('editDescription').value = description;
    document.getElementById('editStatus').value = status;
    window.scrollTo(0, 0);
}

function handleEditFormSubmit() {
    document.getElementById('editForm').classList.add('hidden');
    return true; // Allow form to be submitted
}

// JavaScript to handle logout
document.querySelector('.logout-button').addEventListener('click', function(event) {
        event.preventDefault(); // Prevent default button behavior
        if (confirm('Are you sure you want to log out?')) {
            window.location.href = 'logout.php'; // Redirect to logout script
        }
    });

</script>
<?php include('../includes/footer.php'); ?>
</body>
</html>


