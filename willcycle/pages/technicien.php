<?php
session_start();

// Check if the user is a technician
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'technicien') {
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



// Add a new report
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_report'])) {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $productId = $_POST['productId'];
    $createdBy = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO reports (title, content, productId, createdBy) VALUES (:title, :content, :productId, :createdBy)");
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':content', $content);
    $stmt->bindParam(':productId', $productId);
    $stmt->bindParam(':createdBy', $createdBy);
    $stmt->bindParam(':createdBy', $createdBy);

    if ($stmt->execute()) {
        $success_message = "Report added successfully.";
        header("Location: ".$_SERVER['PHP_SELF']);
        exit;
    } else {
        $error_message = "Error adding report.";
    }
}

// Update a report
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_report'])) {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $content = $_POST['content'];
    $productId = $_POST['productId'];

    $stmt = $conn->prepare("UPDATE reports SET title = :title, content = :content, productId = :productId WHERE id = :id");
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':content', $content);
    $stmt->bindParam(':productId', $productId);
    $stmt->bindParam(':id', $id);

    if ($stmt->execute()) {
        $success_message = "Report updated successfully.";
        header("Location: ".$_SERVER['PHP_SELF']);
        exit;
    } else {
        $error_message = "Error updating report.";
    }
}



// Delete a report
if (isset($_GET['delete_report'])) {
    $id = $_GET['delete_report'];

    $stmt = $conn->prepare("DELETE FROM reports WHERE id = :id");
    $stmt->bindParam(':id', $id);

    if ($stmt->execute()) {
        $success_message = "Report deleted successfully.";
        header("Location: ".$_SERVER['PHP_SELF']);
        exit;
    } else {
        $error_message = "Error deleting report.";
    }
}
?>

<!-- HTML code to display products, reports, forms, and messages -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Technician Dashboard</title>
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

        /* Style for dropdown list */
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
    <h2>Technician Dashboard</h2>

    <!-- Success or error message -->
    <?php if (isset($success_message)): ?>
        <div class="message success"><?php echo $success_message; ?></div>
    <?php elseif (isset($error_message)): ?>
        <div class="message error"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <!-- Add Report Form -->
    <div class="form-container">
    <h3>Add Report</h3>
        <form action="" method="post">
            <input type="text" name="title" placeholder="Title" required>
            <textarea name="content" placeholder="Content" rows="4" required></textarea>
            <select class="dropdown-list" name="productId" required>
                <option value="">Select Product</option>
                <?php foreach ($products as $product): ?>
                    <option value="<?php echo $product['id']; ?>"><?php echo $product['name']; ?></option>
                <?php endforeach; ?>
            </select>
            <input type="submit" name="add_report" value="Add Report">
        </form>
    </div>

    <!-- Report Table -->
    <h3>Reports</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Content</th>
                <th>Product</th>
                <th>createdBy</th>
                <th>createdAt</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($reports as $report): ?>
                <tr>
                    <td><?php echo $report['id']; ?></td>
                    <td><?php echo $report['title']; ?></td>
                    <td><?php echo $report['content']; ?></td>
                    <td><?php echo $report['product_name']; ?></td>
                    <td><?php echo $report['createdBy']; ?></td>
                    <td><?php echo $report['createdAt']; ?></td>
                    <td class="action-links">
                        <a class="edit" href="javascript:void(0);" onclick="openEditModal(<?php echo $report['id']; ?>, '<?php echo addslashes($report['title']); ?>', '<?php echo addslashes($report['content']); ?>', <?php echo $report['productId']; ?>)">Edit</a>
                        <a class="delete" href="?delete_report=<?php echo $report['id']; ?>" onclick="return confirm('Are you sure you want to delete this report?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Edit Report Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeEditModal()">&times;</span>
            <h2>Edit Report</h2>
            <form id="editForm" action="" method="post">
                <input type="hidden" id="edit_report_id" name="id">
                <input type="text" id="edit_title" name="title" placeholder="Title" required class="modal-input">
                <textarea id="edit_content" name="content" placeholder="Content" rows="4" required class="modal-input"></textarea>
                <select id="edit_productId" class="dropdown-list modal-input" name="productId" required>
                    <option value="">Select Product</option>
                    <?php foreach ($products as $product): ?>
                        <option value="<?php echo $product['id']; ?>"><?php echo $product['name']; ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="submit" name="update_report" value="Update Report" class="modal-submit">
            </form>
        </div>
    </div>
    <div style="text-align: center; margin-top: 10px; margin-bottom: 90px;">
    <button class="logout-button">Logout</button>
</div>

    <script>
        // Get the modal
        var modal = document.getElementById('editModal');

        // When the user clicks on the button, open the modal
        function openEditModal(reportId, title, content, productId) {
            document.getElementById("edit_report_id").value = reportId;
            document.getElementById("edit_title").value = title;
            document.getElementById("edit_content").value = content;
            document.getElementById("edit_productId").value = productId;
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
</div>
<?php include('../includes/footer.php'); ?>
</body>
</html>









