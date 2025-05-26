<?php
require_once "db_connect.php";

session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$error = '';
$user_id = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = trim($_POST['user_id']);
    $password = trim($_POST['password']);
    
    $conn = getDB();
    
    $sql = "SELECT id, employee_id, password, name FROM employees WHERE employee_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        
        error_log("Stored hash: " . $user['password']);
        error_log("Input password: " . $password);
        
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['employee_id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_db_id'] = $user['id'];
            
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Invalid employee ID or password";
            error_log("Password verification failed");
        }
    } else {
        $error = "Invalid employee ID or password";
    }
    
    $stmt->close();
    closeDB($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Attendance System - Login</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f8f7fc;
            color: #333;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-image: linear-gradient(135deg, #f8f7fc 0%, #c3cfe2 100%);
        }

        .login-container {
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 15px 30px rgba(106, 13, 173, 0.2);
            width: 100%;
            max-width: 400px;
        }

        .header {
            background-color: #6a0dad;
            color: white;
            padding: 20px;
            text-align: center;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }

        .header h1 {
            margin: 0;
            font-size: 1.8rem;
        }

        .header p {
            font-size: 14px;
            opacity: 0.9;
        }

        .login-form {
            padding: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #6a0dad;
            font-weight: 500;
            font-size: 14px;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 15px;
            transition: border 0.3s;
        }

        .form-group input:focus {
            border-color: #6a0dad;
            outline: none;
        }

        .login-btn {
            background-color: #6a0dad;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 5px;
            width: 100%;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .login-btn:hover {
            background-color: #5a0bb2;
        }

        .error-message {
            color: #e74c3c;
            font-size: 14px;
            margin-top: 5px;
        }

        .server-error {
            color: #e74c3c;
            font-size: 14px;
            margin-bottom: 15px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="header">
            <h1>Employee Attendance System</h1>
            <p>Sign in to access the dashboard</p>
        </div>
        
        <div class="login-form">
            <?php if (!empty($error)): ?>
                <div class="server-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <form id="loginForm" method="POST" action="Userlogin.php">               
                <div class="form-group">
                    <label for="user_id">Employee ID</label>
                    <input type="text" id="user_id" name="user_id" placeholder="Enter your employee ID" required
                           value="<?php echo htmlspecialchars($user_id); ?>">
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                </div>
                
                <button type="submit" class="login-btn">Sign In</button>
            </form>
        </div>
    </div>
</body>
</html>
