<?php
session_start();
require_once __DIR__ . '/databaseConnection.php';

// Only admins can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: Login.php');
    exit;
}

// Fetch pending testimonials (approved IS NULL)
$pendingTestimonials = [];
$res = $conn->query("SELECT t.*, u.Name AS userName, u.Surname AS userSurname
                     FROM testimonial t
                     LEFT JOIN users u ON t.email = u.email
                     WHERE t.approved IS NULL
                     ORDER BY t.testimonialID DESC");
while ($row = $res->fetch_assoc()) {
    $pendingTestimonials[] = $row;
}
$res->free();

// Fetch the single most recent report (descending date) for dashboard display
// we no longer need the array of all reports, just the latest entry
$reports = [];
$res2 = $conn->query("SELECT * FROM report ORDER BY incidentDate DESC LIMIT 1");
if ($res2) {
    while ($row = $res2->fetch_assoc()) {
        $reports[] = $row;
    }
    $res2->free();
}

// Helper function to get display name
function getDisplayName($t) {
    if (!empty($t['anonymous']) && $t['anonymous'] === 'yes') {
        return 'anonymous';
    }
    if (!empty($t['userName']) || !empty($t['userSurname'])) {
        return trim(($t['userName'] ?? '') . ' ' . ($t['userSurname'] ?? ''));
    }
    if (!empty($t['Name']) || !empty($t['Surname'])) {
        return trim(($t['Name'] ?? '') . ' ' . ($t['Surname'] ?? ''));
    }
    return $t['StudentNumber'] ?: $t['email'] ?: 'Student';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - RuCOPING</title>
    <link rel="stylesheet" href="admin.css">

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            /* match main stylesheet */
            background-color: #F1E3F3;
            display: flex;
        }

        /* Sidebar */
        .sidebar {
            width: 220px;
            background-color: #792EB2;
            color: white;
            height: 100vh;
            padding-top: 20px;
            position: fixed;
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 30px;
        }

        .sidebar a {
            display: block;
            color: white;
            text-decoration: none;
            padding: 12px 20px;
        }

        .sidebar a:hover {
            background-color: #2B6CB0;
        }

        /* Main Content */
        .main-content {
            margin-left: 220px;
            padding: 20px;
            width: 100%;
        }

        header {
            margin-bottom: 20px;
        }

        header h1 {
            color: #484041;
            margin: 0;
        }

        .dashboard-section {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }

        .dashboard-section h2 {
            color: #792EB2;
            margin-top: 0;
        }

        .card {
            border: 1px solid #D3D3D3;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 6px;
        }

        .btn {
            padding: 6px 12px;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            margin-right: 8px;
            font-size: 14px;
        }

        .approve {
            background-color: #4CAF50;
            color: white;
        }

        .reject {
            background-color: #B22222;
            color: white;
        }

        .respond {
            background-color: #2B6CB0;
            color: white;
        }

        textarea {
            width: 100%;
            margin-top: 10px;
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #CCC;
        }

        .appointment-item {
            padding: 10px;
            border-bottom: 1px solid #EEE;
        }

        .appointment-item:last-child {
            border-bottom: none;
        }

        .date {
            font-weight: bold;
            color: #2B6CB0;
        }
    </style>
</head>

<body>

<!-- Sidebar -->
<div class="sidebar">
    <h2>RUCoping Admin Panel</h2>
    <a href="admin-dashboard.php">Dashboard</a>
    <a href="admin-testimonials.php">Pending Testimonials</a>
    <a href="admin-reports.php">Sexual Assault Reports</a>
    <a href="admin-appointments.html">Appointments</a>
    <a href="admin-stats.php">Statistics</a>
    <a href="logout.php">Logout</a>
</div>

<!-- Main Content -->
<div class="main-content">

<header>
    <h1>Admin Dashboard</h1>
</header>

<div class="dashboard-section">
    <h2>Pending Testimonials</h2>

    <?php if (empty($pendingTestimonials)): ?>
    <p>No pending testimonials at the moment.</p>
<?php else: ?>
    <?php foreach ($pendingTestimonials as $t): ?>
        <div class="card">
            <p><strong><?= htmlspecialchars(getDisplayName($t)) ?></strong> - <?= htmlspecialchars($t['studyYear'] ?? '') ?></p>
            <p>"<?= nl2br(htmlspecialchars($t['testimonial'] ?? '')) ?>"</p>
            <form method="post" action="admin-testimonials.php">
                <input type="hidden" name="id" value="<?= htmlspecialchars($t['testimonialID']) ?>">
                <button class="btn approve" type="submit" name="action" value="approve">Approve</button>
                <button class="btn reject" type="submit" name="action" value="reject">Reject</button>
            </form>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
</div>

<div class="dashboard-section">
    <h2>New Sexual Assault Reports</h2>

    <?php if (empty($reports)): ?>
        <p>No new reports at the moment.</p>
    <?php else: ?>
        <?php foreach ($reports as $r): ?>
            <div class="card">
                <p><strong>Report ID:</strong> <?= htmlspecialchars($r['reportID']) ?></p>
                <p><strong>Date:</strong> <?= htmlspecialchars(date("d M Y", strtotime($r['incidentDate']))) ?></p>
                <p><strong>Student Number:</strong> <?= htmlspecialchars($r['StudentNumber'] ?: 'Anonymous') ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($r['email'] ?: 'N/A') ?></p>
                <p><strong>Location:</strong> <?= htmlspecialchars($r['location'] ?: 'Not specified') ?></p>
                <p><strong>Description:</strong> <?= nl2br(htmlspecialchars($r['incidentDescription'] ?? '')) ?></p>

                <form action="send_response.php" method="POST">
                    <input type="hidden" name="reportID" value="<?= htmlspecialchars($r['reportID']) ?>">
                    <h4>Send Response</h4>
                    <textarea name="response" rows="3" placeholder="Type response..."></textarea><br><br>
                    <button type="submit" class="btn respond">Send Response</button>
                </form>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<div class="dashboard-section">
    <h2>Upcoming Appointments</h2>

    <div class="appointment-item">
        <span class="date">16 May 2026 - 09:00</span>
        <p>Student ID: 204578 | Counselling Session</p>
    </div>

    <div class="appointment-item">
        <span class="date">16 May 2026 - 13:30</span>
        <p>Student ID: 208945 | Follow-up</p>
    </div>

    <div class="appointment-item">
        <span class="date">17 May 2026 - 10:15</span>
        <p>Student ID: 203456 | Trauma Support</p>
    </div>
</div>

</div>

    <script src="admin-testimonials.js"></script>
    <!--Rowan-->
 <script src = "darkmode.js"></script>
</body>
</html>