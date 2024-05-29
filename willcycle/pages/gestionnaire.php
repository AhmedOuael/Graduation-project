<?php
session_start();

// Check if the product is an gestionnaire
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'gestionnaire') {
    header('Location: login.php');
    exit;
}

require_once '../includes/db.php';

// Read product
$stmt = $conn->prepare("SELECT * FROM product");
$stmt->execute();
$product = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Add a new product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $status = $_POST['status'];
    $addedBy = $_SESSION['user_id']; 
    

    $stmt = $conn->prepare("INSERT INTO product (name, description, status,addedBy) VALUES (:name, :description, :status, :addedBy)");
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':addedBy', $addedBy);
   

    if ($stmt->execute()) {
        $success_message = "product added successfully.";
        header("Location: ".$_SERVER['PHP_SELF']);
        exit;
    } else {
        $error_message = "Error adding product.";
    }
}

// Update a product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_product'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $status = $_POST['status'];
    

    $stmt = $conn->prepare("UPDATE product SET name = :name, description = :description, status = :status WHERE id = :id");
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':status', $status);
   
    $stmt->bindParam(':id', $id);

    if ($stmt->execute()) {
        $success_message = "product updated successfully.";
        header("Location: ".$_SERVER['PHP_SELF']);
        exit;
    } else {
        $error_message = "Error updating product.";
    }
}

// Delete a product
if (isset($_GET['delete_product'])) {
    $id = $_GET['delete_product'];

    $stmt = $conn->prepare("DELETE FROM product WHERE id = :id");
    $stmt->bindParam(':id', $id);

    if ($stmt->execute()) {
        $success_message = "product deleted successfully.";
        header("Location: ".$_SERVER['PHP_SELF']);
        exit;
    } else {
        $error_message = "Error deleting product.";
    }
}
?>

<!-- HTML code to display product, forms, and messages -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Management</title>
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
        .form-container textarea,
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
        .form-container textarea:focus,
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
            background-color: 	#FF0000;
            color: #fff;
        }

        .action-links a:hover {
            background-color: #ddd;
        }

        /* CSS for the modal */
.modal {
    display: none;
    position: fixed;
    z-index: 1;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgb(0,0,0);
    background-color: rgba(0,0,0,0.4);
}

.modal-content {
    background-color: #fefefe;
    margin: 10% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
    border-radius: 8px;
    box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
}

/* Close button */
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

/* Modal form input fields */
.modal-content input[type="text"],
.modal-content textarea,
.modal-content select {
    width: 100%;
    padding: 10px;
    margin-bottom: 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
    transition: border-color 0.3s ease;
}

.modal-content input[type="text"]:focus,
.modal-content textarea:focus,
.modal-content select:focus {
    border-color: #4CAF50;
    outline: none;
}

/* Modal form submit button */
.modal-content input[type="submit"] {
    background-color: #4CAF50;
    color: white;
    padding: 12px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.modal-content input[type="submit"]:hover {
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
    <h2>Product Management</h2>

    <div class="container">
    <h2>Product Management</h2>

    <!-- Success or error message -->
    <?php if (isset($success_message)): ?>
        <div class="message success"><?php echo $success_message; ?></div>
    <?php elseif (isset($error_message)): ?>
        <div class="message error"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <!-- Add Product Form -->
    <div class="form-container">
        <h3>Add Product</h3>
        <form action="" method="post">
            <input type="text" name="name" placeholder="Name" required>
            <textarea name="description" placeholder="Description" rows="4" required></textarea>
            <select name="status" required>
                <option value="Order Placement">Order Placement</option>
                <option value="Order Confirmation">Order Confirmation</option>
                <option value="Material Receipt">Material Receipt</option>
                <option value="Quality Control and Sorting">Quality Control and Sorting</option>
                <option value="Processing and Recycling">Processing and Recycling</option>
                <option value="Quality Assurance">Quality Assurance</option>
                <option value="Packaging and Storage">Packaging and Storage</option>
                <option value="Order Fulfillment">Order Fulfillment</option>
                <option value="Shipping and Delivery">Shipping and Delivery</option>
                <option value="Order Completion and Invoice">Order Completion and Invoice</option>
            </select>
            <input type="submit" name="add_product" value="Add Product">
        </form>
    </div>

    <!-- Products Table -->
    <h3>Products</h3>
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
            <?php foreach ($product as $productItem): ?>
                <tr>
                    <td><?php echo $productItem['id']; ?></td>
                    <td><?php echo $productItem['name']; ?></td>
                    <td><?php echo $productItem['description']; ?></td>
                    <td><?php echo $productItem['status']; ?></td>
                    <td><?php echo $productItem['addedBy']; ?></td>
                    <td><?php echo $productItem['addedAt']; ?></td>
                    <td class="action-links">
                        <a class="edit" href="#" data-id="<?php echo $productItem['id']; ?>" data-name="<?php echo $productItem['name']; ?>" data-description="<?php echo $productItem['description']; ?>" data-status="<?php echo $productItem['status']; ?>">Edit</a>
                        <a class="delete" href="?delete_product=<?php echo $productItem['id']; ?>" onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Modal for editing product -->
<div id="editProductModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Edit Product</h2>
        <form id="editProductForm" action="" method="post">
            <input type="hidden" name="id" id="editProductId">
            <input type="text" name="name" id="editProductName" required>
            <textarea name="description" id="editProductDescription" rows="4" required></textarea>
            <select name="status" id="editProductStatus" required>
                <option value="Order Placement">Order Placement</option>
                <option value="Order Confirmation">Order Confirmation</option>
                <option value="Material Receipt">Material Receipt</option>
                <option value="Quality Control and Sorting">Quality Control and Sorting</option>
                <option value="Processing and Recycling">Processing and Recycling</option>
                <option value="Quality Assurance">Quality Assurance</option>
                <option value="Packaging and Storage">Packaging and Storage</option>
                <option value="Order Fulfillment">Order Fulfillment</option>
                <option value="Shipping and Delivery">Shipping and Delivery</option>
                <option value="Order Completion and Invoice">Order Completion and Invoice</option>
            </select>
            <input type="submit" name="update_product" value="Update Product">
        </form>
    </div>
</div>
<div style="text-align: center; margin-top: 10px; margin-bottom: 90px;">
    <button class="logout-button">Logout</button>
</div>
<script>
    // Get the modal
    var modal = document.getElementById('editProductModal');

    // Get the <span> element that closes the modal
    var span = document.getElementsByClassName("close")[0];

    // When the user clicks on <span> (x), close the modal
    span.onclick = function() {
        modal.style.display = "none";
    }

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }

    // When the user clicks on edit, populate the modal fields
    var editButtons = document.getElementsByClassName("edit");
    for (var i = 0; i < editButtons.length; i++) {
        editButtons[i].onclick = function() {
            var productId = this.getAttribute("data-id");
            var productName = this.getAttribute("data-name");
            var productDescription = this.getAttribute("data-description");
            var productStatus = this.getAttribute("data-status");

            document.getElementById("editProductId").value = productId;
            document.getElementById("editProductName").value = productName;
            document.getElementById("editProductDescription").value = productDescription;
            document.getElementById("editProductStatus").value = productStatus;

            modal.style.display = "block";
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
</div>
</body>
</html>