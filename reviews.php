<?php
require_once __DIR__ . '/databaseConnection.php';
// fetch approved testimonials
$testimonials = [];
if ($res = $conn->query("SELECT * FROM testimonials WHERE approved = 1 ORDER BY testimonialID DESC")) {
    while ($row = $res->fetch_assoc()) {
        $testimonials[] = $row;
    }
    $res->free();
}
?>
<!DOCTYPE html>
<html lang="en">
    
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Testimonials</title>
        <link rel="stylesheet" href="reviews.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    </head>

    <body>
        
        <!-- Header and Navigation Menu-->
        <header>
            <div class="header-container">
                <div class="logo-text">
                    <a href="index.html" class="logo">
                        <i class="fa-solid fa-spa"></i> RuCOPING
                    </a>
                </div>
            
                <div class="header-right">
                    <nav class="desktop-nav">
                        <a href="index.html">Home</a>
                        <a href="aboutme.html">About Us</a>
                        <a href="Login.html">Login</a>
                    </nav>

                    <input type="checkbox" id="menu-toggle" class="menu-toggle">
                    <label class="hamburger" for="menu-toggle">
                        <span></span>
                        <span></span>
                        <span></span>
                    </label>
            
                    <nav class="menu-nav">
                        <a href="admin-dashboard.html">Admin LogIn</a> 
                        <a href ="appointment.html">Book Appointment with Counsellor</a>
                        <a href="submit-review.html">Share your Story</a>

                        <label class="switch">
                            Dark Mode
                            <input type="checkbox" id="dark-mode-toggle">
                            <span class="slider round"></span>
                        </label>
                    </nav>
                </div>
            </div>
        </header>
        
        <h1>Student Testimonials</h1>
        <p>Real feedback from our students</p>
        
        
        <main class="reviews-container">
            <?php foreach ($testimonials as $t): ?>
                <?php $displayName = $t['anonymous'] ? 'Anonymous' : ($t['StudentNumber'] ?: $t['email'] ?: 'Student'); ?>
                <section class="review-card">
                    <?php if (!empty($t['picture'])): ?>
                        <img src="<?= htmlspecialchars($t['picture']) ?>" alt="Student" class="profile-image" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; float: left; margin-right: 20px;">
                    <?php endif; ?>
                    <p class="quote">
                        <?= nl2br(htmlspecialchars($t['testimonial'])) ?>
                    </p>
                    <p class="student-tag" style="color: #555555;">~ <?= htmlspecialchars($t['study year']) ?></p>
                </section>
            <?php endforeach; ?>

            <section class="review-actions" style="text-align: center; margin: 40px 0;">
                <a href="submit-review.html" class="btn-review">
                    <i class="fa-solid fa-pen-to-square"></i>Submit a Review</a>
            </section>

        </main>

        <!-- Footer -->
        <footer>
            <div class = "footer-container">

            <!--Developers-->
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

            <!--Contact Us-->
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

            <!--Socials-->   
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
        <script src="darkmode.js"></script>

    </body>
</html>