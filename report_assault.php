<?php
session_start();
require 'databaseConnection.php';

$error = '';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // User not logged in, redirect to login page
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // gather information from the report assault html
    $studentNr = $_SESSION['StudentNumber'];
    $email = $_SESSION['email'];
    $incidentDate = $_POST["date"];
    $location = $_POST["location"];
    $description = $_POST["description"];
    $confirmation = $_POST["confirm"];
    
    if (!$incidentDate || !$description){

        $error = "Please fill in all required fields.";
    }

    elseif(!$confirmation)
    {
        $error= "Please confirm report";
    }

    else
    {
        $sql = "INSERT INTO report (StudentNumber, email, `location`,incidentDescription , incidentDate)
        VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);

        if(!$stmt)
        {
            die($conn->error);
        }

        $stmt->bind_param("sssss", $studentNr, $email,$location, $description,
                $incidentDate
            );

            if ($stmt->execute()){
                header("Location: report_assault.php?registered=1");
                exit();

            }
            else{

                $error = "Could not submit form: ".$stmt->error;
            }

            $stmt->close();
        }
}
        

    // create an entry in the database

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Report Sexual Assault - RuCOPING</title>
    <link rel="stylesheet" href="index.css">
    <link rel="stylesheet" href="Global_Hreader.css">
    <link rel="stylesheet" href="global_footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<style>

    /* ===== RESET ===== */
    * {
        box-sizing: border-box;
    }

    /*header formatting*/
   /* header{
    background-color:#792eb2;
    position:fixed;
    top:0;
    left:0;
    width:100%;
    padding: 10px 0;
    transition: all 0.4s ease; 
    box-shadow: 0 2px 10px rgba(0,0,0,0.12);
    -webkit-backdrop-filter: blur(6px);
    backdrop-filter: blur(6px);
    z-index: 2000;
    text-decoration:none;
    color:#ffffff;
    margin:0;
    }

    .header-container{
    width:97%;
    margin:0 auto;
    display:flex;
    justify-content: space-between;
    align-items: center;
    }
    nav{
        display:flex;
        align-items: center;
        justify-content: space-between;
    }

    nav a{
        text-decoration:none;
        color:#ffffff;
        margin:0 15px;
        font-weight: 500;
    }*/

    /* ensure page content is not hidden behind the fixed header */
    body {
        background-color: #F1E3F3;
        color: #484041;
        font-family: Arial, sans-serif;
        margin: 0;
        padding-top: 80px; /* slightly larger than header height */
    }
    

    /* ===== CONTENT WRAPPER ===== */
    .container {
        max-width: 700px;
        margin: 20px auto 60px auto; /* reduced top margin because body padding handles spacing */
        padding: 0 5%;
        position: relative; /* ensure content stacks above ribbon */
        z-index: 2;
    }

    .ribbon {
    position: fixed;
    top: 0%; /* adjust as needed to position the ribbon higher or lower on the page */
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 0; /* make sure the ribbon is at the back*/
    pointer-events: none; /* allow clicks to pass through the ribbon */
}

    
    /* Slight move right */
    @media (min-width: 992px) {
        .container {
            margin-left: 12%;
        }
    }

    h1 {
        color: #792EB2;
    }

    label {
        font-weight: 600;
        display: block;
        margin-top: 15px;
    }

    input,
    select,
    textarea {
        width: 100%;
        padding: 10px;
        border: 1px solid rgba(72,64,65,0.15);
        border-radius: 6px;
        margin-top: 6px;
        font-size: 14px;
    }

    /* ===== CHECKBOX FIX ===== */
    .checkbox-group {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        margin-top: 20px;
    }

    .checkbox-group input[type="checkbox"] {
        width: auto;
        margin-top: 3px;
        transform: scale(1.2);
    }

    /* ===== BUTTON ===== */
    button {
        background-color: #2B6CB0;
        color: white;
        padding: 12px 18px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        margin-top: 20px;
        font-size: 15px;
    }

    button:hover {
        opacity: 0.95;
    }

    /* ===== RESPONSIVE ===== */

    /* Tablet */
    @media (max-width: 768px) {

        .container {
            margin: 30px auto;
            padding: 0 6%;
        }

        h1 {
            font-size: 1.6rem;
        }
    }

    /* Mobile */
    @media (max-width: 480px) {

        header {
            height: 64px;
        }

        .logo {
            font-size: 1.2rem;
        }

        .container {
            margin: 20px auto;
            padding: 0 6%;
        }

        h1 {
            font-size: 1.4rem;
        }

        button {
            width: 100%;
        }

        .checkbox-group {
            flex-direction: row;
            align-items: flex-start;
        }
    }

    </style>
<body>

<!-- navigation bar-->    
<header>

    <div class="header-container">

        <div class="logo-text">
            <a  href="index.html" class="logo"><i class="fa-solid fa-spa"></i>RuCOPING</a>
        </div>
        
       
        <div class="header-right">
            <!--visible navBar -->
            <a href="index.html">Home</a>
            <a href="aboutme.html">About Us</a>
            <a href="Login.php">Login</a>                <input type="checkbox" id="menu-toggle" class="menu-toggle">
               
            <!--Hamburger menu checkbox-->
            <input type="checkbox" id="menu-toggle" class="menu-toggle">

            <!--hamburger icon-->
            <label class="hamburger" for="menu-toggle">
                <span></span>
                <span></span>
                <span></span> <!--hamburger lines-->
            </label> 

            <!-- Hamburger Links -->
            <nav class = "menu-nav">
                <a href="admin-dashboard.html">Admin LogIn</a> 
                <a href ="appointment.html">Book Appointment with Counsellor</a>
                <a href="submit-review.html">Share your Story</a>
                <a href = "report-assault.html">Report Assault</a>

                <label class="switch">
                    Dark Mode
                    <input type="checkbox" id="dark-mode-toggle">
                    <span class="slider round"></span>
                </label>
            </nav>
        </div>       
        
    </div>       
</header>

<canvas class="ribbon"></canvas>

<div class="container">

<h1>Confidential Sexual Assault Report</h1>

<p>
If you are in immediate danger, please call emergency services.
This form is confidential.
</p>

<form action="report_assault.php" method="POST">

    <!-- <label for = "StudentNr">Enter your Student Nr:</label>
    <input type="text" id="StudentNr" name="StudentNr" placeholder="G00Z0000"> -->

    <label for="date">Date of Incident:</label>
    <input type="date" id="date" name="date">

    <label for="location">Location of Incident (Optional):</label>
    <input type="text" id="location" name="location">

    <label for="description">Please describe what happened:</label>
    <textarea id="description" name="description" rows="8" required></textarea>

    <div class="checkbox-group">
        <input type="checkbox" id="confirm" name="confirm" required>
        <label for="confirm">
            I confirm that the information provided is true to the best of my knowledge.
        </label>
    </div>

    <button type="submit">Submit Report</button>

</form>

</div>

<!--FOOTER-->
<footer>
<div class = "footer-container">
<!--DEVELOPERS-->
    <div class = "footer-section">
        <h6>DEVELOPERS</h6>
        <p><i style ="color:gray">
            Caitlin Elliott: g24e2316@campus.ru.ac.za
            <br>Rowan Fortune: g23f9661@campus.ru.ac.za
            <br>Ulelethu Rigala: g24r8054@campus.ru.ac.za
            <br>Kganyiso Sediti: g23s3219@campus.ru.ac.za
            <br>Othandwayo Myona: g22m3157@campus.ru.ac.za
            <br>RuCOPING(c) Copyright</i>
        </p>
    </div>

<!--CONTACT US-->
    <div class = "footer-section">
        <h6>CONTACT US</h6>

        <p><strong>Health Care Centre</strong><br>
            Email: <a href="mailto:healthcarecentre@ru.ac.za">healthcarecentre@ru.ac.za</a><br>
            Phone: <a href="tel:+27466038523">046 603 8523</a>
        </p>

        <p><strong>Counselling Centre</strong><br>
            Email: <a href="mailto:counsellingcentre@ru.ac.za">counsellingcentre@ru.ac.za</a><br>
            Phone: <a href="tel:+27466037070">046 603 7070</a>
        </p>

    </div>

<!--SOCIALS-->   
    <div class="footer-section socials">
        <h6>SOCIAL MEDIA</h6>
        <div class="social-icons">
            <a href="https://www.instagram.com/rhodes_university/"><i class="fa-brands fa-instagram"></i></a>
            <a href="https://www.youtube.com/playlist?list=PLV9WJDAawyhbQb_DSKe3rjc9j_dOykynB"><i class="fa-brands fa-youtube"></i></a>
            <a href="https://www.facebook.com/groups/706769159431821/"><i class="fa-brands fa-facebook"></i></a>
        </div>
    </div>

</div>
</footer>

<script src="Articles/articles.js">
const anonymous = document.getElementById('anonymous');
const studentNrDiv = document.getElementById('StudentNrDiv')
anonymous.addEventListener('change', () => {
    if (anonymous.value === 'no'){
        studentNrDiv.style.display = 'block';
    } else {
        studentNrDiv.style.display = 'none';
    }
    });
</script>

</body>
</html>
