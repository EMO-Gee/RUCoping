<?php
session_start();

// Restrict access to admin users
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: Login.php');
    exit;
}

require_once __DIR__ . '/databaseConnection.php';

// Handle approve/reject actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? '';
    $action = $_POST['action'] ?? '';
    if ($id && $action) {
        if ($action === 'approve') {
            $stmt = $conn->prepare("UPDATE testimonial SET approved = 1 WHERE testimonialID = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();
        } elseif ($action === 'reject') {
            $stmt = $conn->prepare("DELETE FROM testimonial WHERE testimonialID = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();
        }
    }
    header('Location: admin-testimonials.php');
    exit;
}

// Helper function to get display name

function getDisplayName($t) {
    // If the user chose anonymous
    if (!empty($t['anonymous']) && $t['anonymous'] === 'yes') {
        return 'anonymous';
    }

    // If user is logged in and StudentNumber matches, use session name
    if (!empty($_SESSION['StudentNumber']) && $_SESSION['StudentNumber'] == ($t['StudentNumber'] ?? '')) {
        return trim(($_SESSION['Name'] ?? '') . ' ' . ($_SESSION['Surname'] ?? ''));
    }

    // Otherwise, use name from users table if available
    if (!empty($t['userName']) || !empty($t['userSurname'])) {
        return trim(($t['userName'] ?? '') . ' ' . ($t['userSurname'] ?? ''));
    }

    // Fallback: Name/Surname from testimonial table
    if (!empty($t['Name']) || !empty($t['Surname'])) {
        return trim(($t['Name'] ?? '') . ' ' . ($t['Surname'] ?? ''));
    }

    // Last fallback: StudentNumber or email
    return $t['StudentNumber'] ?: $t['email'] ?: 'Student';
}

// Fetch pending testimonials (approved IS NULL)
$pending = [];
$res = $conn->query("SELECT t.*, u.Name AS userName, u.Surname AS userSurname
                     FROM testimonial t
                     LEFT JOIN users u ON t.email = u.email
                     WHERE t.approved IS NULL
                     ORDER BY t.testimonialID DESC");
while ($row = $res->fetch_assoc()) {
    $pending[] = $row;
}
$res->free();

// Fetch approved testimonials (approved = 1)
$approved = [];
$res = $conn->query("SELECT t.*, u.Name AS userName, u.Surname AS userSurname
                     FROM testimonial t
                     LEFT JOIN users u ON t.email = u.email
                     WHERE t.approved = 1
                     ORDER BY t.testimonialID DESC");
while ($row = $res->fetch_assoc()) {
    $approved[] = $row;
}
$res->free();


/*
// Helper function to determine display name
function getDisplayName($t) {
    if ($t['anonymous']) return 'anonymous';
    if (!empty($t['userName']) || !empty($t['userSurname'])) {
        return trim(($t['userName'] ?? '') . ' ' . ($t['userSurname'] ?? ''));
    }
    if (!empty($t['Name']) || !empty($t['Surname'])) {
        return trim(($t['Name'] ?? '') . ' ' . ($t['Surname'] ?? ''));
    }
    return $t['StudentNumber'] ?: $t['email'] ?: 'Student';
} */
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Testimonials - Admin</title>
<link rel="stylesheet" href="admin.css">
</head>
<body>

<div class="sidebar">
    <h2>RUCoping Admin Panel</h2>
    <a href="admin-dashboard.php">Dashboard</a>
    <a href="admin-testimonials.php">Pending Testimonials</a>
    <a href="admin-reports.php">Sexual Assault Reports</a>
    <a href="admin-appointments.html">Appointments</a>
    <a href="admin-stats.html">Statistics</a>
    <a href="logout.php">Logout</a>
</div>

<div class="main">
    <h1>Pending Testimonials</h1>
    <?php if (empty($pending)): ?>
        <p>No pending testimonials at the moment.</p>
    <?php else: ?>
        <?php foreach ($pending as $t): ?>
            <div class="card" style="margin-bottom:20px;">
                <p><strong>Name:</strong> <?= htmlspecialchars(getDisplayName($t)) ?></p>
                <p><strong>Year:</strong> <?= htmlspecialchars($t['studyYear'] ?? '') ?></p>
                <p><?= nl2br(htmlspecialchars($t['testimonial'] ?? '')) ?></p>
                <form method="post" style="margin-top:8px;">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($t['testimonialID']) ?>">
                    <button class="btn approve" type="submit" name="action" value="approve">Approve</button>
                    <button class="btn reject" type="submit" name="action" value="reject">Reject</button>
                </form>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <h1 style="margin-top:40px;">Approved Testimonials</h1>
    <?php if (empty($approved)): ?>
        <p>No approved testimonials yet.</p>
    <?php else: ?>
        <?php foreach ($approved as $t): ?>
            <div class="card" style="margin-bottom:20px;">
                <p><strong>Name:</strong> <?= htmlspecialchars(getDisplayName($t)) ?></p>
                <p><strong>Year:</strong> <?= htmlspecialchars($t['studyYear'] ?? '') ?></p>
                <p><?= nl2br(htmlspecialchars($t['testimonial'] ?? '')) ?></p>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<script src="admin-testimonials.js"></script>
<script src="darkmode.js"></script>
</body>
</html>