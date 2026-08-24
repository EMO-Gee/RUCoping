<?php
session_start();
require 'databaseConnection.php';

// ensure password column wide enough for hashes (bcrypt produces ~60 chars)
function ensurePasswordColumn(){
    global $conn;
    // attempt to modify; ignore errors if already correct
    @$conn->query("ALTER TABLE users MODIFY `password` VARCHAR(255) NOT NULL");
}
ensurePasswordColumn();

// Regenerate session ID to prevent fixation attacks
session_regenerate_id(true);

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $StudentNumber = trim($_POST['StudentNumber'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$StudentNumber || !$password) {
        $error = "Both fields are required.";
    } else {
        $sql = "SELECT StudentNumber, Name, Surname, `password`, role FROM users WHERE StudentNumber = ? LIMIT 1";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            die("<b>Prepare failed:</b> " . htmlspecialchars($conn->error));
        }

        $stmt->bind_param("s", $StudentNumber);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
            $user = $result->fetch_assoc();
            // support legacy plain-text passwords by checking either
            $isValid = false;
            if (password_verify($password, $user['password'])) {
                $isValid = true;
            } elseif ($password === $user['password']) {
                // old un-hashed value; valid login, rehash and update record
                $isValid = true;
                $newHash = password_hash($password, PASSWORD_BCRYPT);
                $upd = $conn->prepare("UPDATE users SET `password` = ? WHERE StudentNumber = ?");
                if ($upd) {
                    $upd->bind_param("ss", $newHash, $StudentNumber);
                    $upd->execute();
                    $upd->close();
                }
            }

            if ($isValid) {
                $_SESSION['StudentNumber'] = $user['StudentNumber'];
                $_SESSION['Name'] = $user['Name'];
                $_SESSION['Surname'] = $user['Surname'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['loggedin'] = true;

                if (strtolower($user['role']) === 'admin') {
                    header("Location: admin-dashboard.php");
                    exit();
                } else {
                    // redirect to the page they were trying to access
                    $redirectTo = $_SESSION['redirect_after_login'] ?? 'index.html';
                    unset($_SESSION['redirect_after_login']);
                    header("Location: " . $redirectTo);
                    exit();
                }
            } else {
                $error = "Incorrect password.";
            }
        } else {
            $error = "User not found.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - RuCOPING</title>
    <style>
        body {
            background-color: #F1E3F3;
            color: #484041;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        .login-box {
            text-align: center;
            background: white;
            padding: 24px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            width: 100%;
            max-width: 420px;
        }

        h3 {
            color: #792EB2;
            margin-top: 0;
        }

        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 8px;
            margin: 8px 0 14px 0;
            border: 1px solid rgba(72,64,65,0.15);
            border-radius: 4px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            background-color: #2B6CB0;
            color: white;
            border: none;
            padding: 10px 16px;
            border-radius: 6px;
            cursor: pointer;
        }

        a {
            color: #792EB2;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <h3>RU Loggin?</h3>
        <?php if ($error): ?>
            <p style="color:red;"><?=htmlspecialchars($error)?></p>
        <?php endif; ?>
        <form method="POST" action="">
            <img src="Resources/LoginImg.png" alt="Login Image" width="200" height="200" >
            </br>
            <label for="StudentNumber">Student Number:</label>
            <input type="text" id="StudentNumber" name="StudentNumber" required><br>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required><br>

            <input type="submit" value="Login">

            <p>Don't have an account? <a href="signup.php">Sign Up</a></p>
        </form>
    </div>
</body>
</html>