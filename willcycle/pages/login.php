<?php
session_start();
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, role FROM users WHERE username = :username AND password = :password");
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', $password);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];

        // Redirect the user to their respective role-based page
        switch ($user['role']) {
            case 'admin':
                header('Location: admin.php');
                break;
            case 'gestionnaire':
                header('Location: gestionnaire.php');
                break;
            case 'logisticien':
                header('Location: logisticien.php');
                break;
            case 'maintenance':
                header('Location: maintenance.php');
                break;
            case 'operateur':
                header('Location: operateur.php');
                break;
            case 'technicien':
                header('Location: technicien.php');
                break;
            case 'ingénieur':
                header('Location: ingenieur.php');
                break;
            default:
                $error = "Invalid role.";
                break;
        }
        exit;
    } else {
        $error = "Invalid username or password.";
    }
}

?>


<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
  <style>
        /* Header Styles */
        header {
            background-color: #343a40; /* Dark gray,  */
            color: #fff;
            padding: 20px;
            text-align: center;
        }

        header .logo {
            margin-bottom: 10px;
        }

        header h1 {
            margin: 0;
            font-size: 24px;
        }

        
    </style>
</head>
<body>
<?php include('../includes/header.php'); ?>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }

        .container {
            max-width: 400px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #333;
            text-align: center;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-bottom: 5px;
            color: #333;
        }

        input[type="text"], 
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            transition: border-color 0.3s ease;
        }

        input[type="text"]:focus, 
        input[type="password"]:focus {
            border-color: #4CAF50;
            outline: none;
        }

        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        p {
            color: #a94442;
            background-color: #f2dede;
            border: 1px solid #ebccd1;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Login</h1>
        <?php if (isset($error)) echo "<p>$error</p>"; ?>
        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
            <br>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <br>
            <input type="submit" value="Login">
        </form>
    </div>
    
    <?php include('../includes/footer.php'); ?>
    
</body>
</html>
