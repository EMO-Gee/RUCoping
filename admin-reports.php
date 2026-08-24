<?php
session_start();
require_once __DIR__ . '/databaseConnection.php';

// Only allow admins
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: Login.php');
    exit;
}

// Fetch reports
$reports = [];
$res = $conn->query("SELECT * FROM report ORDER BY incidentDate DESC");
while ($row = $res->fetch_assoc()) {
    $reports[] = $row;
}
$res->free();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Sexual Assault Reports - Admin</title>
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
        <h1>New Sexual Assault Reports</h1>

<?php if (empty($reports)): ?>
        <p>No reports at the moment.</p>
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
                        <h4>Respond to User</h4>
                        <textarea name="response" rows="4" placeholder="Type response..."></textarea><br>
                        <button type="submit" class="btn respond">Send Response</button>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>



    <script src="admin-testimonials.js"></script>
    <script src = "darkmode.js"></script>

</body>
</html>