<?php
session_start();
require 'databaseConnection.php';

if($_SERVER['REQUEST_METHOD']=="POST"){

$StudentNumber = $_POST['StudentNumber'] ?? NULL;
$email = $_POST['email'] ?? NULL;
$studyYear = $_POST['studyYear'];
$testimonial = $_POST['testimonial'];
$anonymous = $_POST['anonymous'];

// new submissions start without any approval status (NULL)
$approved = NULL;  // the column should allow NULL and default to NULL in the database

// if not anonymous, pull the student's name and surname from the users table
$Name = NULL;
$Surname = NULL;
if ($anonymous === 'no' && $StudentNumber) {
    $qry = $conn->prepare("SELECT Name, Surname FROM users WHERE StudentNumber = ? LIMIT 1");
    if ($qry) {
        $qry->bind_param("s", $StudentNumber);
        $qry->execute();
        $res = $qry->get_result();
        if ($row = $res->fetch_assoc()) {
            $Name = $row['Name'];
            $Surname = $row['Surname'];
        }
        $qry->close();
    }
}

// image upload
$pictureName = NULL;

if(isset($_FILES['picture']) && $_FILES['picture']['error']==0){
    $folder = "uploads/";
    if(!file_exists($folder)){
        mkdir($folder,0777,true);
    }

    $pictureName = time()."_".$_FILES['picture']['name'];

    move_uploaded_file($_FILES['picture']['tmp_name'], $folder.$pictureName);

}

$haveNameCols = false;
if ($res = $conn->query("SHOW COLUMNS FROM testimonials LIKE 'Name'")) {
    if ($res->num_rows) {
        $haveNameCols = true;
    }
    $res->free();
}

if ($haveNameCols) {
    $sql = "INSERT INTO testimonial
        (StudentNumber,email,testimonial,anonymous,picture,studyYear,Name,Surname,approved)
        VALUES (?,?,?,?,?,?,?,?,?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssi", $StudentNumber, $email, $testimonial, $anonymous, $pictureName, $studyYear, $Name, $Surname, $approved);
} else {
    $sql="INSERT INTO testimonial
        (StudentNumber,email,testimonial,anonymous,picture,studyYear,approved)
        VALUES (?,?,?,?,?,?,?)";
    $stmt=$conn->prepare($sql);
    $stmt->bind_param("ssssssi", $StudentNumber, $email, $testimonial, $anonymous, $pictureName, $studyYear, $approved);
}

if($stmt->execute()){
    header("Location: submit-review.php?sent=1");
    exit();
} else{
    echo "Error: ".$conn->error;
}

$stmt->close();

}
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Submit Testimonial - RuCOPING</title>
<!--<link rel="stylesheet" href="Global_Hreader.css">-->
<link rel="stylesheet" href="index.css">
<link rel="stylesheet" href="Global_Hreader.css">
<link rel="stylesheet" href="global_footer.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">

<style>

/* ===== RESET ===== */
* {
    box-sizing: border-box;
}

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
    margin: 50px auto 80px auto;
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

/* Slight shift right on large screens */
@media (min-width: 992px) {
    .container {
        margin-left: 12%;
    }
}

h1 {
    color: #792EB2;
    margin-bottom: 10px;
}

p {
    margin-bottom: 20px;
}

/* ===== FORM ===== */
label {
    font-weight: 600;
    display: block;
    margin-top: 18px;
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

textarea {
    resize: vertical;
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
    margin-top: 25px;
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
</head>

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
                <a href ="appointment.html">Book Appointment with Counsellor</a>
                <a href="submit-review.php">Share your Story</a>

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

<h1>Share Your Testimonial</h1>
<p>Your feedback helps other students feel supported. Submissions are reviewed before publishing.</p>

<form action="submit-review.php" method="POST" enctype="multipart/form-data">

    <div id="StudentNrDiv" style="display: none;">
        <label>Your Student Number (optional):</label>
        <input type="text" name="StudentNumber">
    </div>

    <label>Your Email (optional):</label>
    <input type="email" name="email">

    <label>Year of Study:</label>
    <select name="studyYear" required>
        <option value="">-- Select Year --</option>
        <option value="1st year">1st Year</option>
        <option value="2nd year">2nd Year</option>
        <option value="3rd year">3rd Year</option>
        <option value="4th year">4th Year</option>
        <option value="Postgraduate">Postgraduate</option>
    </select>

    <label>Your Experience:</label>
    <textarea name="testimonial" rows="6" required></textarea>

    <label>Upload image:</label>
    <input type="file" name="picture">

    <label>Submit anonymously?</label>
    <select name="anonymous">
        <option value="yes">Yes</option>
        <option value="no">No</option>
    </select>

    <div class="checkbox-group">
    <input type="checkbox" name="consent" required>
    <label>I agree testimonial may be displayed</label>
    </div>

    <button type="submit">Submit Testimonial</button>

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

<script src="Articles/articles.js"></script>
<script src="darkmode.js"></script>

<script>
// capture form submissions and persist to localStorage
(function(){
    const form = document.querySelector('form');
    if(!form) return;
})();

    const pictureInput = document.getElementById("picture");

    if(pictureInput){
        pictureInput.addEventListener("change", function(){
            const file = this.files[0];

            if(file){
                const maxSize = 2 * 1024 * 1024; // 2MB

                if(file.size > maxSize){
                    alert("Image must be smaller than 2MB.");
                    this.value = "";
                }
            }
        });
    }

    const anonymous = document.getElementById('anonymous');
    const studentNrDiv = document.getElementById('StudentNrDiv');
    if (anonymous && studentNrDiv) {
        const updateVisibility = () => {
            studentNrDiv.style.display = anonymous.value === 'no' ? 'block' : 'none';
        };
        anonymous.addEventListener('change', updateVisibility);
        // make sure correct state on page load
        updateVisibility();
    }

// show thank‑you message if redirected after submission
(function(){
    const params = new URLSearchParams(window.location.search);
    if (params.has('sent')) {
        alert('Thank you! Your testimonial has been submitted and will appear once approved by an admin.');
    }
})();
</script>

</body>
</html>