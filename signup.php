<?php
require 'databaseConnection.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $StudentNumber = trim($_POST['StudentNumber'] ?? '');
    $Name = trim($_POST['Name'] ?? '');
    $Surname = trim($_POST['Surname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirm = trim($_POST['confirm'] ?? '');

    if (!$StudentNumber || !$Name || !$Surname || !$email || !$password){

        $error = "All fields are required.";

    }
    elseif ($password !== $confirm){

        $error = "Passwords do not match.";

    }
    else{

        // Check if user exists
        $check = $conn->prepare(
        "SELECT StudentNumber 
        FROM users 
        WHERE StudentNumber = ? OR email = ?");

        $check->bind_param("ss",$StudentNumber,$email);
        $check->execute();

        $result = $check->get_result();

        if ($result->num_rows > 0){

            $error = "Student number or email already exists.";

        }
        else{

            $sql = "INSERT INTO users
            (StudentNumber, Name, Surname, email, password, role)
            VALUES (?,?,?,?,?,'user')";

            $stmt = $conn->prepare($sql);

            if(!$stmt){
                die($conn->error);
            }

            $stmt->bind_param("sssss", $StudentNumber, $Name, $Surname, $email,
                $password
            );

            if ($stmt->execute()){

                header("Location: login.php?registered=1");
                exit();

            }
            else{

                $error = "Registration failed: ".$stmt->error;

            }

            $stmt->close();
        }

        $check->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up - RuCOPING</title>
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

        input[type="text"], input[type="password"], input[type="email"] {
            width: 100%;
            padding: 8px;
            margin: 8px 0 14px 0;
            border: 1px solid rgba(72,64,65,0.15);
            border-radius: 4px;
            box-sizing: border-box;
        }

        button[type="submit"] {
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
        <h3>Create an Account</h3>
        <form action="signup.php" method="POST">

            <img src="Resources/LoginImg.png" width="200">

            <label>Student Number:</label>
            <input type="text" name="StudentNumber" required>

            <label>First Name:</label>
            <input type="text" name="Name" required>

            <label>Last Name:</label>
            <input type="text" name="Surname" required>

            <label>Email:</label>
            <input type="email" name="email" required>

            <label>Password:</label>
            <input type="password" name="password" minlength="6" required>

            <label>Confirm Password:</label>
            <input type="password" name="confirm" minlength="6" required>

            <button type="submit">Sign Up</button>

            <p>Already have an account?
            <a href="login.php">Login</a>
            </p>

        </form>
    </div>

</body>
</html>