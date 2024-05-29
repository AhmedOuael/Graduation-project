<?php
session_start();

// Check if the user is an engineer
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ingénieur') {
    header('Location: login.php');
    exit;
}

require_once '../includes/db.php';

// Read methods
$stmt = $conn->prepare("SELECT * FROM methods");
$stmt->execute();
$methods = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Add a new method
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_method'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $createdBy = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO methods (name, description, createdBy) VALUES (:name, :description, :createdBy)");
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':createdBy', $createdBy);
    $stmt->bindParam(':createdAt', $createdAt);

    if ($stmt->execute()) {
        $success_message = "Method added successfully.";
        header("Location: ".$_SERVER['PHP_SELF']);
        exit;
    } else {
        $error_message = "Error adding method.";
    }
}

// Update a method
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_method'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $description = $_POST['description'];

    $stmt = $conn->prepare("UPDATE methods SET name = :name, description = :description WHERE id = :id");
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':id', $id);

    if ($stmt->execute()) {
        $success_message = "Method updated successfully.";
        header("Location: ".$_SERVER['PHP_SELF']);
        exit;
    } else {
        $error_message = "Error updating method.";
    }
}

// Delete a method
if (isset($_GET['delete_method'])) {
    $id = $_GET['delete_method'];

    $stmt = $conn->prepare("DELETE FROM methods WHERE id = :id");
    $stmt->bindParam(':id', $id);

    if ($stmt->execute()) {
        $success_message = "Method deleted successfully.";
        header("Location: ".$_SERVER['PHP_SELF']);
        exit;
    } else {
        $error_message = "Error deleting method.";
    }
}
?>


<!-- HTML code to display methods, forms, and messages -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Method Management</title>
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

        h2 {
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

        /* Styles for modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
        }

        .modal-content {
            background-color: #f2f2f2;
            margin: 10% auto;
            padding: 20px;
            border-radius: 8px;
            width: 80%;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        .modal-input {
            width: calc(100% - 40px);
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            transition: border-color 0.3s ease;
        }

        .modal-input:focus {
            border-color: #4CAF50;
            outline: none;
        }

        .modal-submit {
            background-color: #4CAF50;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            width: 100%;
        }

        .modal-submit:hover {
            background-color: #45a049;
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
    <h2>Method Management</h2>

    <!-- Success or error message -->
    <?php if (isset($success_message)): ?>
        <div class="message success"><?php echo $success_message; ?></div>
    <?php elseif (isset($error_message)): ?>
        <div class="message error"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <!-- Add Method Form -->
    <div class="form-container">
        <h3>Add Method</h3>
        <form action="" method="post">
            <input type="text" name="name" placeholder="Name" required>
            <textarea name="description" placeholder="Description" rows="4" required></textarea>
            <input type="submit" name="add_method" value="Add Method">
        </form>
    </div>

    <!-- Methods Table -->
    <h3>Methods</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Description</th>
                <th>createdBy</th>
                <th>createdAt</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($methods as $method): ?>
                <tr>
                    <td><?php echo $method['id']; ?></td>
                    <td><?php echo $method['name']; ?></td>
                    <td><?php echo $method['description']; ?></td>
                    <td><?php echo $method['createdBy']; ?></td>
                    <td><?php echo $method['createdAt']; ?></td>
                    <td class="action-links">
                        <a class="edit" href="javascript:void(0);" onclick="openEditModal(<?php echo $method['id']; ?>, '<?php echo addslashes($method['name']); ?>', '<?php echo addslashes($method['description']); ?>')">Edit</a>
                        <a class="delete" href="?delete_method=<?php echo $method['id']; ?>" onclick="return confirm('Are you sure you want to delete this method?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Edit Method Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeEditModal()">&times;</span>
            <h2>Edit Method</h2>
            <form id="editForm" action="" method="post">
                <input type="hidden" id="edit_method_id" name="id">
                <input type="text" id="edit_name" name="name" placeholder="Name" required class="modal-input">
                <textarea id="edit_description" name="description" placeholder="Description" rows="4" required class="modal-input"></textarea>
                <input type="submit" name="update_method" value="Update Method" class="modal-submit">
            </form>
        </div>
    </div>
</div>
<div style="text-align: center; margin-top: 10px; margin-bottom: 90px;">
    <button class="logout-button">Logout</button>
</div>
<script>
    // Get the modal
    var modal = document.getElementById('editModal');

    // When the user clicks on the button, open the modal
    function openEditModal(methodId, name, description) {
        document.getElementById("edit_method_id").value = methodId;
        document.getElementById("edit_name").value = name;
        document.getElementById("edit_description").value = description;
        document.getElementById("editModal").style.display = "block";
    }

    function closeEditModal() {
        document.getElementById("editModal").style.display = "none";
    }

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
        if (event.target == modal) {
            closeEditModal();
        }
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

                        