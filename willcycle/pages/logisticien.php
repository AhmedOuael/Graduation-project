<?php
session_start();

// Check if the user is a logistician
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'logisticien') {
    header('Location: login.php');
    exit;
}

require_once '../includes/db.php';

// Read transportation
$stmt = $conn->prepare("SELECT * FROM transportation");
$stmt->execute();
$transportations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Add a new transportation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_transportation'])) {
    $name = $_POST['name'];
    $available = $_POST['available'];
    $contact = $_POST['contact'];
    $capacity = $_POST['capacity'];
    $createdBy = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO transportation (name, available, contact, capacity, createdBy) VALUES (:name, :available, :contact, :capacity, :createdBy)");
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':available', $available);
    $stmt->bindParam(':contact', $contact);
    $stmt->bindParam(':capacity', $capacity);
    $stmt->bindParam(':addedBy', $addedBy);
    $stmt->bindParam(':addedAt', $addedAt);

    if ($stmt->execute()) {
        $success_message = "Transportation added successfully.";
        header("Location: ".$_SERVER['PHP_SELF']);
        exit;
    } else {
        $error_message = "Error adding transportation.";
    }
}

// Update a transportation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_transportation'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $available = $_POST['available'];
    $contact = $_POST['contact'];
    $capacity = $_POST['capacity'];

    $stmt = $conn->prepare("UPDATE transportation SET name = :name, available = :available, contact = :contact, capacity = :capacity WHERE id = :id");
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':available', $available);
    $stmt->bindParam(':contact', $contact);
    $stmt->bindParam(':capacity', $capacity);
    $stmt->bindParam(':addedBy', $addedBy);
    $stmt->bindParam(':addedAt', $addedAt);
    $stmt->bindParam(':id', $id);

    if ($stmt->execute()) {
        $success_message = "Transportation updated successfully.";
        header("Location: ".$_SERVER['PHP_SELF']);
        exit;
    } else {
        $error_message = "Error updating transportation.";
    }
}

// Delete a transportation
if (isset($_GET['delete_transportation'])) {
    $id = $_GET['delete_transportation'];

    $stmt = $conn->prepare("DELETE FROM transportation WHERE id = :id");
    $stmt->bindParam(':id', $id);

    if ($stmt->execute()) {
        $success_message = "Transportation deleted successfully.";
        header("Location: ".$_SERVER['PHP_SELF']);
        exit;
    } else {
        $error_message = "Error deleting transportation.";
    }
}
?>

<!-- HTML code to display transportation, forms, and messages -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transportation Management</title>
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
        .form-container input[type="number"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            transition: border-color 0.3s ease;
        }

        .form-container input[type="text"]:focus, 
        .form-container input[type="number"]:focus {
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
            background-color: #007bff;
            color: #fff;
        }

        .action-links a.delete {
            background-color: #dc3545;
            color: #fff;
        }

/* Popup Styles */
.modal {
    display: none;
    position: fixed;
    z-index: 1;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0, 0, 0, 0.4);
    padding-top: 60px;
}
.modal-content {
    background-color: #f2f2f2;
    margin: 5% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
    border-radius: 8px;
    position: relative;
}

.close {
    color: #aaa;
    position: absolute;
    top: 10px;
    right: 10px;
    font-size: 28px;
    font-weight: bold;
}

.close:hover,
.close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
}

.modal-header h2 {
    text-align: center;
    color: #333;
}

.modal-body input[type="text"],
.modal-body input[type="number"] {
    width: 100%;
    padding: 10px;
    margin-bottom: 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
    transition: border-color 0.3s ease;
}

.modal-body input[type="text"]:focus,
.modal-body input[type="number"]:focus {
    border-color: #4CAF50;
    outline: none;
}

.modal-footer {
    padding-top: 20px;
    text-align: center;
}

.modal-footer input[type="submit"] {
    background-color: #4CAF50; 
    color: white;
    padding: 14px 28px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.modal-footer input[type="submit"]:hover {
    background-color: #45a049;
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
    <h2>Transportation Management</h2>

    <!-- Success or error message -->
    <?php if (isset($success_message)): ?>
        <div class="message success"><?php echo $success_message; ?></div>
    <?php elseif (isset($error_message)): ?>
        <div class="message error"><?php echo $error_message; ?></div>
    <?php endif; ?>

    
    <!-- Add Transportation Form -->
<div class="form-container">
    <h3>Add Transportation</h3>
    <form action="" method="post">
        <input type="text" name="name" placeholder="Name" required>
        <select class="dropdown-list" name="available" required>
            <option value="Available">Available</option>
            <option value="Not Available">Not Available</option>
            <option value="En Route">En Route</option>
        </select>
        <input type="text" name="contact" placeholder="Contact" required>
        <input type="number" name="capacity" placeholder="Capacity" required>
        <input type="submit" name="add_transportation" value="Add Transportation">
    </form>
</div>


    <!-- Transportation Table -->
    <h3>Transportations</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Available</th>
                <th>Contact</th>
                <th>Capacity</th>
                <th>addedBy</th>
                <th>addedAt</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
    <?php foreach ($transportations as $transportation): ?>
        <tr>
            <td><?php echo $transportation['id']; ?></td>
            <td id="name_<?php echo $transportation['id']; ?>"><?php echo $transportation['name']; ?></td>
            <td id="available_<?php echo $transportation['id']; ?>"><?php echo $transportation['available']; ?></td>
            <td id="contact_<?php echo $transportation['id']; ?>"><?php echo $transportation['contact']; ?></td>
            <td id="capacity_<?php echo $transportation['id']; ?>"><?php echo $transportation['capacity']; ?></td>
            <td id="createdBy_<?php echo $transportation['id']; ?>"><?php echo $transportation['createdBy']; ?></td>
            <td id="createdAt_<?php echo $transportation['id']; ?>"><?php echo $transportation['createdAt']; ?></td>
            <td class="action-links">
                <a class="edit" href="#" onclick="openPopup('<?php echo $transportation['id']; ?>')">Edit</a>
                <a class="delete" href="?delete_transportation=<?php echo $transportation['id']; ?>" onclick="return confirm('Are you sure you want to delete this transportation?')">Delete</a>
            </td>
        </tr>
    <?php endforeach; ?>
</tbody>

    </table>
</div>

<!-- Popup for editing transportation -->
<div id="editPopup" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closePopup()">&times;</span>
        <div class="modal-header">
            <h2>Edit Transportation</h2>
        </div>
        <div class="modal-body">
            <form id="editForm" action="" method="post">
                <input type="hidden" name="id" id="editId">
                <input type="text" name="name" id="editName" placeholder="Name" required>
                <select class="dropdown-list" name="available" id="editAvailable" required>
                    <option value="Available">Available</option>
                    <option value="Not Available">Not Available</option>
                    <option value="En Route">En Route</option>
                </select>
                <input type="text" name="contact" id="editContact" placeholder="Contact" required>
                <input type="number" name="capacity" id="editCapacity" placeholder="Capacity" required>
                <div class="modal-footer">
                    <input type="submit" name="update_transportation" value="Update Transportation">
                </div>
            </form>
        </div>
    </div>
</div>

<div style="text-align: center; margin-top: 10px; margin-bottom: 90px;">
    <button class="logout-button">Logout</button>
</div>

<script>
    function openPopup(id) {
        console.log("Opening popup for ID: ", id);
        document.getElementById("editId").value = id;
        var name = document.getElementById("name_" + id).innerText;
        var available = document.getElementById("available_" + id).innerText;
        var contact = document.getElementById("contact_" + id).innerText;
        var capacity = document.getElementById("capacity_" + id).innerText;

        document.getElementById("editName").value = name;
        document.getElementById("editAvailable").value = available;
        document.getElementById("editContact").value = contact;
        document.getElementById("editCapacity").value = capacity;

        document.getElementById("editPopup").style.display = "block";
    }

    function closePopup() {
        document.getElementById("editPopup").style.display = "none";
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

           
