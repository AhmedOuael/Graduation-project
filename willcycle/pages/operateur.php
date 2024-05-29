<?php
session_start();

// Check if the user is an operator
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'operateur') {
    header('Location: login.php');
    exit;
}

require_once '../includes/db.php';

// Read products
$stmt = $conn->prepare("SELECT * FROM product");
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Read reports
$stmt = $conn->prepare("SELECT r.*, p.name AS product_name FROM reports r JOIN product p ON r.productId = p.id");
$stmt->execute();
$reports = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Read machines
$stmt = $conn->prepare("SELECT * FROM machine");
$stmt->execute();
$machines = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Read fonctionnemnt
$stmt = $conn->prepare("SELECT f.*, m.nom AS machine_name FROM fonctionnemnt f JOIN machine m ON f.machineId = m.id");
$stmt->execute();
$fonctionnemnt = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ... (CRUD operations for products and reports, similar to the previous code)

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
    $stmt->bindParam(':addedBy', $updateBy);
    $stmt->bindParam(':addedAt', $updateAt);

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




// Add a new fonctionnemnt
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_fonctionnemnt'])) {
    $fonctionnement = $_POST['fonctionnement'];
    $machineId = $_POST['machineId'];
    $updateBy = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO fonctionnemnt (fonctionnement, machineId, updateBy) VALUES (:fonctionnement, :machineId, :updateBy)");
    $stmt->bindParam(':fonctionnement', $fonctionnement);
    $stmt->bindParam(':machineId', $machineId);
    $stmt->bindParam(':updateBy', $updateBy);

    if ($stmt->execute()) {
        $success_message = "Fonctionnemnt added successfully.";
        header("Location: ".$_SERVER['PHP_SELF']);
        exit;
    } else {
        $error_message = "Error adding fonctionnemnt.";
    }
}

// Update a fonctionnemnt
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_fonctionnemnt'])) {
    $id = $_POST['id'];
    $fonctionnement = $_POST['fonctionnement'];
    $machineId = $_POST['machineId'];

    $stmt = $conn->prepare("UPDATE fonctionnemnt SET fonctionnement = :fonctionnement, machineId = :machineId WHERE id = :id");
    $stmt->bindParam(':fonctionnement', $fonctionnement);
    $stmt->bindParam(':machineId', $machineId);
    $stmt->bindParam(':id', $id);

    if ($stmt->execute()) {
        $success_message = "Fonctionnemnt updated successfully.";
        header("Location: ".$_SERVER['PHP_SELF']);
        exit;
    } else {
        $error_message = "Error updating fonctionnemnt.";
    }
}

// Delete a fonctionnemnt
if (isset($_GET['delete_fonctionnemnt'])) {
    $id = $_GET['delete_fonctionnemnt'];

    $stmt = $conn->prepare("DELETE FROM fonctionnemnt WHERE id = :id");
    $stmt->bindParam(':id', $id);

    if ($stmt->execute()) {
        $success_message = "Fonctionnemnt deleted successfully.";
        header("Location: ".$_SERVER['PHP_SELF']);
        exit;
    } else {
        $error_message = "Error deleting fonctionnemnt.";
    }
}
?>

<!-- HTML code to display products, reports, machines, fonctionnemnt, forms, and messages -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Operateur Dashboard</title>
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
        }

        .action-links a.edit {
            background-color: #ccc;
            color: #333;
        }

        .action-links a.delete {
            background-color: #FF0000;
            color: #fff;
        }
        .dropdown-list {
        width: 100%;
        padding: 10px;
        margin-bottom: 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
        transition: border-color 0.3s ease;
    }

    /* Style for dropdown list when focused */
    .dropdown-list:focus {
        border-color: #4CAF50;
        outline: none;
    }
    /* Styles for popup */
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
    <h2>Operateur Dashboard</h2>

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
            <input type="text" name="nom" placeholder="nom" required>
            <textarea name="description" placeholder="Description" rows="4" required></textarea>
            <input type="text" name="status" placeholder="Status" required>
            <input type="submit" name="add_machine" value="Add Machine">
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
            <?php foreach ($machines as $machine): ?>
                <tr>
                    <td><?php echo $machine['id']; ?></td>
                    <td><?php echo $machine['nom']; ?></td>
                    <td><?php echo $machine['description']; ?></td>
                    <td><?php echo $machine['status']; ?></td>
                    <td><?php echo $machine['addedBy']; ?></td>
                    <td><?php echo $machine['addedAt']; ?></td>
                    <td class="action-links">
    <a class="edit" href="javascript:void(0);" onclick="openEditMachineModal(<?php echo $machine['id']; ?>, '<?php echo addslashes($machine['nom']); ?>', '<?php echo addslashes($machine['description']); ?>', '<?php echo addslashes($machine['status']); ?>')">Edit</a>
    <a class="delete" href="?delete_machine=<?php echo $machine['id']; ?>" onclick="return confirm('Are you sure you want to delete this machine?')">Delete</a>
</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Add Fonctionnemnt Form -->
    <div class="form-container">
        <h3>Add Fonctionnemnt</h3>
        <form action="" method="post">
            <input type="text" name="fonctionnement" placeholder="Fonctionnement" required>
            <select class="dropdown-list" name="machineId" required>
                <option value="" disabled selected>Select Machine</option>
                <?php foreach ($machines as $machine): ?>
                    <option value="<?php echo $machine['id']; ?>"><?php echo $machine['nom']; ?></option>
                <?php endforeach; ?>
            </select>
            <input type="submit" name="add_fonctionnemnt" value="Add Fonctionnemnt">
        </form>
    </div>

    <!-- Fonctionnemnt Table -->
    <h3>Fonctionnemnt</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Fonctionnement</th>
                <th>Machine</th>
                <th>updateBy</th>
                <th>updateAt</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($fonctionnemnt as $fonc): ?>
                <tr>
                    <td><?php echo $fonc['id']; ?></td>
                    <td><?php echo $fonc['fonctionnement']; ?></td>
                    <td><?php echo $fonc['machine_name']; ?></td>
                    <td><?php echo $fonc['updateBy']; ?></td>
                    <td><?php echo $fonc['updateAt']; ?></td>
                    <td class="action-links">
    <a class="edit" href="javascript:void(0);" onclick="openEditFonctionnemntModal(<?php echo $fonc['id']; ?>, '<?php echo addslashes($fonc['fonctionnement']); ?>', <?php echo $fonc['machineId']; ?>)">Edit</a>
    <a class="delete" href="?delete_fonctionnemnt=<?php echo $fonc['id']; ?>" onclick="return confirm('Are you sure you want to delete this fonctionnemnt?')">Delete</a>
</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

 <!-- Edit Machine Modal -->
<div id="editMachineModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeEditMachineModal()">&times;</span>
        <h2>Edit Machine</h2>
        <form id="editMachineForm" action="" method="post">
            <input type="hidden" id="edit_machine_id" name="id">
            <input type="text" id="edit_machine_nom" name="nom" placeholder="Nom" required class="modal-input">
            <textarea id="edit_machine_description" name="description" placeholder="Description" rows="4" required class="modal-input"></textarea>
            <input type="text" id="edit_machine_status" name="status" placeholder="Status" required class="modal-input">
            <input type="submit" name="update_machine" value="Update Machine" class="modal-submit">
        </form>
    </div>
</div>

<!-- Edit Fonctionnemnt Modal -->
<div id="editFonctionnemntModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeEditFonctionnemntModal()">&times;</span>
        <h2>Edit Fonctionnemnt</h2>
        <form id="editFonctionnemntForm" action="" method="post">
            <input type="hidden" id="edit_fonctionnemnt_id" name="id">
            <input type="text" id="edit_fonctionnemnt_fonctionnement" name="fonctionnement" placeholder="Fonctionnement" required class="modal-input">
            <select id="edit_fonctionnemnt_machineId" class="dropdown-list modal-input" name="machineId" required>
                <option value="">Select Machine</option>
                <?php foreach ($machines as $machine): ?>
                    <option value="<?php echo $machine['id']; ?>"><?php echo $machine['nom']; ?></option>
                <?php endforeach; ?>
            </select>
            <input type="submit" name="update_fonctionnemnt" value="Update Fonctionnemnt" class="modal-submit">
        </form>
    </div>
</div>
<div style="text-align: center; margin-top: 10px; margin-bottom: 90px;">
    <button class="logout-button">Logout</button>
</div>

<script>
    // Get the modals
    var machineModal = document.getElementById('editMachineModal');
    var fonctionnemntModal = document.getElementById('editFonctionnemntModal');

    // When the user clicks on the button, open the modal
    function openEditMachineModal(machineId, nom, description, status) {
        document.getElementById("edit_machine_id").value = machineId;
        document.getElementById("edit_machine_nom").value = nom;
        document.getElementById("edit_machine_description").value = description;
        document.getElementById("edit_machine_status").value = status;
        document.getElementById("editMachineModal").style.display = "block";
    }

    function openEditFonctionnemntModal(fonctionnemntId, fonctionnement, machineId) {
        document.getElementById("edit_fonctionnemnt_id").value = fonctionnemntId;
        document.getElementById("edit_fonctionnemnt_fonctionnement").value = fonctionnement;
        document.getElementById("edit_fonctionnemnt_machineId").value = machineId;
        document.getElementById("editFonctionnemntModal").style.display = "block";
    }

    function closeEditMachineModal() {
        document.getElementById("editMachineModal").style.display = "none";
    }

    function closeEditFonctionnemntModal() {
        document.getElementById("editFonctionnemntModal").style.display = "none";
    }

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
        if (event.target == machineModal) {
            closeEditMachineModal();
        } else if (event.target == fonctionnemntModal) {
            closeEditFonctionnemntModal();
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


</div>
<?php include('../includes/footer.php'); ?>
</body>
</html>

