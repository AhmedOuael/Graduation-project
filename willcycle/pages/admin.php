<?php
session_start();

// Check if the user is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

require_once '../includes/db.php';

// Read users
$stmt = $conn->prepare("SELECT * FROM users");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Add a new user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (:username, :password, :role)");
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', $password);
    $stmt->bindParam(':role', $role);

    if ($stmt->execute()) {
        $success_message = "User added successfully.";
        header("Location: ".$_SERVER['PHP_SELF']);
        exit;
    } else {
        $error_message = "Error adding user.";
    }
}

// Update a user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {
    $id = $_POST['id'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    $stmt = $conn->prepare("UPDATE users SET username = :username, password = :password, role = :role WHERE id = :id");
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', $password);
    $stmt->bindParam(':role', $role);
    $stmt->bindParam(':id', $id);

    if ($stmt->execute()) {
        $success_message = "User updated successfully.";
        header("Location: ".$_SERVER['PHP_SELF']);
        exit;
    } else {
        $error_message = "Error updating user.";
    }
}

// Delete a user
if (isset($_GET['delete_user'])) {
    $id = $_GET['delete_user'];

    $stmt = $conn->prepare("DELETE FROM users WHERE id = :id");
    $stmt->bindParam(':id', $id);

    if ($stmt->execute()) {
        $success_message = "User deleted successfully.";
        header("Location: ".$_SERVER['PHP_SELF']);
        exit;
    } else {
        $error_message = "Error deleting user.";
    }
}

// Read product
$stmt = $conn->prepare("SELECT * FROM product");
$stmt->execute();
$product = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Read methods
$stmt = $conn->prepare("SELECT * FROM methods");
$stmt->execute();
$methods = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Read transportation
$stmt = $conn->prepare("SELECT * FROM transportation");
$stmt->execute();
$transportations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Read machine
$stmt = $conn->prepare("SELECT * FROM machine");
$stmt->execute();
$machine = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Read reports
$stmt = $conn->prepare("SELECT r.*, p.name AS product_name FROM reports r JOIN product p ON r.productId = p.id");
$stmt->execute();
$reports = $stmt->fetchAll(PDO::FETCH_ASSOC);

$report = null;
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $stmt = $conn->prepare("SELECT * FROM reports WHERE id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $report = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$report) {
        $error_message = "Error fetching report details.";
    }
}

// Read fonctionnemnt
$stmt = $conn->prepare("SELECT f.*, m.nom AS machine_name FROM fonctionnemnt f JOIN machine m ON f.machineId = m.id");
$stmt->execute();
$fonctionnemnt = $stmt->fetchAll(PDO::FETCH_ASSOC);






?>

<!-- HTML code to display users, forms, and messages -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    
    <style>
    /* General Styles */
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
.form-container input[type="password"],
.form-container select {
    width: 100%;
    padding: 10px;
    margin-bottom: 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
    transition: border-color 0.3s ease;
}

.form-container input[type="text"]:focus, 
.form-container input[type="password"]:focus,
.form-container select:focus {
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
    margin-right: 10px;
    transition: background-color 0.3s ease;
}

.action-links a.edit {
    background-color: #ccc;
    color: #333;
}

.action-links a.delete {
    background-color: #FF0000;
    color: #fff;
}

.action-links a:hover {
    background-color: #ddd;
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
.modal-body input[type="password"],
.modal-body select {
    width: 100%;
    padding: 10px;
    margin-bottom: 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
    transition: border-color 0.3s ease;
}

.modal-body input[type="text"]:focus,
.modal-body input[type="password"]:focus,
.modal-body select:focus {
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
    <h2>Admin Dashboard</h2>

    <!-- Success or error message -->
    <?php if (isset($success_message)): ?>
        <div class="message success"><?php echo $success_message; ?></div>
    <?php elseif (isset($error_message)): ?>
        <div class="message error"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <!-- Add User Form -->
    <div class="form-container">
        <h3>Add User</h3>
        <form action="" method="post">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <select class="dropdown-list" name="role" required>
                <option value="admin">Admin</option>
                <option value="gestionnaire">Gestionnaire</option>
                <option value="ingénieur">Ingénieur</option>
                <option value="operateur">Operateur</option>
                <option value="maintenance">Maintenance</option>
                <option value="technicien">Technicien</option>
                <option value="ingénieur">Ingénieur</option>
                <option value="logisticien">logisticien</option>
            </select>
            <input type="submit" name="add_user" value="Add User">
        </form>
    </div>

    <!-- Users Table -->
    <div class="form-container">
    <h3>Users</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Role</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo $user['id']; ?></td>
                    <td><?php echo $user['username']; ?></td>
                    <td><?php echo $user['role']; ?></td>
                    <td class="action-links">
    <a class="edit" href="#" data-id="<?php echo $user['id']; ?>" data-username="<?php echo $user['username']; ?>" data-role="<?php echo $user['role']; ?>" onclick="openEditUserModal(<?php echo $user['id']; ?>, '<?php echo $user['username']; ?>', '<?php echo $user['role']; ?>')">Edit</a>
    <a class="delete" href="?delete_user=<?php echo $user['id']; ?>" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
</td>


                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>
<!-- Products Table -->
<div class="form-container">
<h3>Products</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Description</th>
                <th>Status</th>
                
            </tr>
        </thead>
        <tbody>
            <?php foreach ($product as $productItem): ?>
                <tr>
                    <td><?php echo $productItem['id']; ?></td>
                    <td><?php echo $productItem['name']; ?></td>
                    <td><?php echo $productItem['description']; ?></td>
                    <td><?php echo $productItem['status']; ?></td>
                    
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>


<!-- Methods Table -->
<div class="form-container">
<h3>Methods</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Description</th>
                
            </tr>
        </thead>
        <tbody>
            <?php foreach ($methods as $method): ?>
                <tr>
                    <td><?php echo $method['id']; ?></td>
                    <td><?php echo $method['name']; ?></td>
                    <td><?php echo $method['description']; ?></td>
                    
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>
     <!-- Transportation Table -->
     <div class="form-container">
     <h3>Transportations</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Available</th>
                <th>Contact</th>
                <th>Capacity</th>
                
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
            
        </tr>
    <?php endforeach; ?>
</tbody>

    </table>
    </div>
     <!-- Machine Table -->
     <div class="form-container">
     <h3>Machines</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Description</th>
                <th>Status</th>
                
            </tr>
        </thead>
        <tbody>
            <?php foreach ($machine as $mach): ?>
                <tr>
                    <td><?php echo htmlspecialchars($mach['id']); ?></td>
                    <td><?php echo htmlspecialchars($mach['nom']); ?></td>
                    <td><?php echo htmlspecialchars($mach['description']); ?></td>
                    <td><?php echo htmlspecialchars($mach['status']); ?></td>
                    
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    <!-- Fonctionnemnt Table -->
    <div class="form-container">
    <h3>Fonctionnemnt</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Fonctionnement</th>
                <th>Machine</th>
                
            </tr>
        </thead>
        <tbody>
            <?php foreach ($fonctionnemnt as $fonc): ?>
                <tr>
                    <td><?php echo $fonc['id']; ?></td>
                    <td><?php echo $fonc['fonctionnement']; ?></td>
                    <td><?php echo $fonc['machine_name']; ?></td>
                    
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>

    <!-- Report Table -->
    <div class="form-container">
    <h3>Reports</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Content</th>
                <th>Product</th>
                
            </tr>
        </thead>
        <tbody>
            <?php foreach ($reports as $report): ?>
                <tr>
                    <td><?php echo $report['id']; ?></td>
                    <td><?php echo $report['title']; ?></td>
                    <td><?php echo $report['content']; ?></td>
                    <td><?php echo $report['product_name']; ?></td>
                   
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>









    <!-- Edit User Modal -->
<div id="editUserModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeEditUserModal()">&times;</span>
        <div class="modal-header">
            <h2>Edit User</h2>
        </div>
        <div class="modal-body">
            <form id="editUserForm" action="" method="post">
                <input type="hidden" id="edit_user_id" name="id">
                <input type="text" id="edit_username" name="username" placeholder="Username" required>
                <input type="password" id="edit_password" name="password" placeholder="Password" required>
                <select id="edit_role" class="dropdown-list" name="role" required>
                    <option value="admin">Admin</option>
                    <option value="gestionnaire">Gestionnaire</option>
                    <option value="ingénieur">Ingénieur</option>
                    <option value="operateur">Operateur</option>
                    <option value="maintenance">Maintenance</option>
                    <option value="technicien">Technicien</option>
                    <option value="ingénieur">Ingénieur</option>
                    <option value="logisticien">logisticien</option>
                </select>
                <div class="modal-footer">
                    <input type="submit" name="update_user" value="Update User">
                </div>
            </form>
        </div>
    </div>
</div>

</div>

<div style="text-align: center; margin-top: 10px; margin-bottom: 90px;">
    <button class="logout-button">Logout</button>
</div>


</div>
<script>
    
    // Function to open the edit user modal and populate it with user data
function openEditUserModal(userId, username, role) {
    document.getElementById("edit_user_id").value = userId;
    document.getElementById("edit_username").value = username;
    document.getElementById("edit_password").value = ""; // Clear password field for security reasons
    document.getElementById("edit_role").value = role;
    document.getElementById("editUserModal").style.display = "block";
}

// Function to close the edit user modal
function closeEditUserModal() {
    document.getElementById("editUserModal").style.display = "none";
}

// Close the modal when clicking outside of it
window.onclick = function(event) {
    var modal = document.getElementById('editUserModal');
    if (event.target == modal) {
        closeEditUserModal();
    }
}


 // JavaScript to  logout
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
