<?php
require "databaseConnection.php";

// Total users
$sqlUsers = "SELECT COUNT(*) AS total_users FROM users";
$resultUsers = $conn->query($sqlUsers);
$rowUsers = $resultUsers->fetch_assoc();
$totalUsers = $rowUsers["total_users"];

// Total reports
$sqlReports = "SELECT COUNT(*) AS total_reports FROM reports";
$resultReports = $conn->query($sqlReports);
$rowReports = $resultReports->fetch_assoc();
$totalReports = $rowReports["total_reports"];

// Appointments this week
$sqlAppointments = "SELECT COUNT(*) AS total_appointments 
                    FROM appointment 
                    WHERE WEEK(date) = WEEK(CURDATE())";
$resultAppointments = $conn->query($sqlAppointments);
$rowAppointments = $resultAppointments->fetch_assoc();
$totalAppointments = $rowAppointments["total_appointments"];

/* Reports per month (last 6 months) */
$sql = "
SELECT DATE_FORMAT(incidentDate,'%b %Y') AS month, COUNT(*) AS total
FROM reports
WHERE incidentDate >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH)
GROUP BY YEAR(incidentDate), MONTH(incidentDate)
ORDER BY incidentDate
";

$result = $conn->query($sql);

$reportsData = [];
$reportsLabels = [];

while($row = $result->fetch_assoc()){
    $reportsLabels[] = $row["month"];
    $reportsData[] = $row["total"];
}

/* Testimonials status */
$sql2 = "
SELECT approved, COUNT(*) as total
FROM testimonials
GROUP BY approved
";

$result2 = $conn->query($sql2);

$approved = 0;
$pending = 0;
$rejected = 0; // still unused since rejected testimonials are deleted

while($row = $result2->fetch_assoc()){
    // MySQL returns NULL as a PHP null value
    if ($row["approved"] === null) {
        $pending = $row["total"];
    } else if ($row["approved"] == "yes") {
        $approved = $row["total"];
    } elseif ($row["approved"] === "no") {
        // handle legacy or future cases, though we currently delete rejects
        $rejected = $row["total"];
    }
}

/* Appointments trend */
$sql3 = "
SELECT date, councillor ";
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Statistics - Admin</title>
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
    <h1>System Statistics</h1>

    <div class="stats-cards">
        <div class="card">
            <p>Total Registered Users</p>
            <div class="stat-number"><?php echo $totalUsers; ?></div>
        </div>

        <div class="card">
            <p>New Sexual Assault Reports</p>
            <div class="stat-number"><?php echo $totalReports; ?></div>
        </div>

        <div class="card">
            <p>Appointments This Week</p>
            <div class="stat-number"><?php echo $totalAppointments; ?></div>
        </div>
    </div>

</div>

<section class="charts" style="max-width:960px;margin:18px auto;padding:0 18px;">
    <div class="card">
        <h3>Reports by Month</h3>
        <canvas id="reportsByMonth" width="800" height="320"></canvas>
    </div>

    <div class="card">
        <h3>Appointments (past 8 weeks)</h3>
        <canvas id="appointmentsTrend" width="800" height="320"></canvas>
    </div>

    <div class="card">
        <h3>Testimonials Status</h3>
        <canvas id="testimonialsPie" width="400" height="240"></canvas>
    </div>
</section>

<!-- Chart library and admin stats script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

const reportsLabels = <?php echo json_encode($reportsLabels); ?>;
const reportsData = <?php echo json_encode($reportsData); ?>;

const testimonialsApproved = <?php echo $approved; ?>;
const testimonialsPending = <?php echo $pending; ?>;
const testimonialsRejected = <?php echo $rejected; ?>;

</script>
<script>
    
(function(){

    function ready(fn){
        if (document.readyState !== 'loading') fn();
        else document.addEventListener('DOMContentLoaded', fn);
    }

    ready(function(){

        if (typeof Chart === 'undefined') {
            console.warn('Chart.js not loaded — charts will not render.');
            return;
        }

        /* -------------------------
           REPORTS BY MONTH
        ------------------------- */

        const reportsCtx = document.getElementById('reportsByMonth');

        if (reportsCtx){

            const labels = reportsLabels;     // from PHP
            const data = reportsData;         // from PHP

            new Chart(reportsCtx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Reports',
                        data: data,
                        backgroundColor: 'rgba(183,78,213,0.85)'
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1 }
                        }
                    }
                }
            });

        }

        /* -------------------------
           APPOINTMENTS TREND
        ------------------------- */

        const apptCtx = document.getElementById('appointmentsTrend');

        if (apptCtx){

            const labels = [];
            const now = new Date();

            for (let i = 7; i >= 0; i--) {
                const w = new Date(now);
                w.setDate(now.getDate() - i * 7);
                labels.push(w.toLocaleDateString(undefined, { month: 'short', day: 'numeric' }));
            }

            const apptData = [10,12,8,9,11,12,13,12];

            new Chart(apptCtx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Appointments',
                        data: apptData,
                        borderColor: '#2B6CB0',
                        backgroundColor: 'rgba(43,108,176,0.12)',
                        fill: true,
                        tension: 0.3,
                        pointRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 2 }
                        }
                    }
                }
            });

        }

        /* -------------------------
           TESTIMONIAL STATUS
        ------------------------- */

        const pieCtx = document.getElementById('testimonialsPie');

        if (pieCtx){

            const approved = testimonialsApproved;
            const pending = testimonialsPending;
            const rejected = testimonialsRejected;

            new Chart(pieCtx, {
                type: 'bar',
                data: {
                    labels: ['Approved','Pending','Rejected'],
                    datasets: [{
                        label: 'Testimonials',
                        data: [approved, pending, rejected],
                        backgroundColor: ['#4CAF50','#FFC107','#B22222']
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: { stepSize: 1 }
                        }
                    }
                }
            });

        }

    });

})();

</script>

<script src="admin-testimonials.js"></script>
<script src="admin-stats.js"></script>

 <script src = "darkmode.js"></script>

</body>
</html>
