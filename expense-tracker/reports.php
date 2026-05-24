<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
include 'db.php';

/* ================= FILTER ================= */
$from = $_GET['from'] ?? date('Y-m-01');
$to = $_GET['to'] ?? date('Y-m-d');

/* ================= MONTHLY ================= */
$monthly = $conn->query("
    SELECT DATE_FORMAT(date, '%Y-%m') as month, SUM(amount) as total
    FROM expenses
    WHERE date BETWEEN '$from' AND '$to'
    GROUP BY month
");

$months = [];
$totals = [];

while ($row = $monthly->fetch_assoc()) {
    $months[] = $row['month'];
    $totals[] = (float)$row['total'];
}

/* ================= CATEGORY ================= */
$cat = $conn->query("
    SELECT category, SUM(amount) as total
    FROM expenses
    WHERE date BETWEEN '$from' AND '$to'
    GROUP BY category
    ORDER BY total DESC
");

$categories = [];
$amounts = [];

while ($row = $cat->fetch_assoc()) {
    $categories[] = $row['category'] ?? "Other";
    $amounts[] = (float)$row['total'];
}

/* ================= FALLBACK ================= */
if (empty($categories)) {
    $categories = ["No Data"];
    $amounts = [0];
}

if (empty($months)) {
    $months = ["No Data"];
    $totals = [0];
}

/* ================= STATS ================= */
$topCategory = $categories[0] ?? "None";
$total = array_sum($amounts);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Advanced Reports</title>

    <!-- CSS -->
    <link rel="stylesheet" href="style.css">

    <!-- ✅ ADD CHART.JS HERE -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0"></script>
</head>

<body>

<div class="app">

<!-- SIDEBAR -->
<aside class="sidebar">
    <h2 class="logo">💰 Tracker</h2>
    <div class="menu">
        <a href="index.php">🏠 Dashboard</a>
        <a href="reports.php" class="active">📊 Reports</a>
        <a href="settings.php">⚙️ Settings</a>
        <a href="logout.php" class="logout">🚪 Logout</a>
    </div>
</aside>

<!-- MAIN -->
<main class="main">

<h2 class="page-title">📊 Advanced Reports</h2>
<p class="page-subtitle">Detailed insights of your expenses</p>

<!-- FILTER -->
<div class="form-box">
<form method="GET">
    <input type="date" name="from" value="<?= $from ?>">
    <input type="date" name="to" value="<?= $to ?>">
    <button type="submit">Filter</button>
</form>
</div>

<!-- CARDS -->
<div class="cards">
    <div class="card">
        <h3>Total Spending</h3>
        <p>₱ <?= number_format($total,2) ?></p>
    </div>
    <div class="card">
        <h3>Top Category</h3>
        <p><?= $topCategory ?></p>
    </div>
    <div class="card">
        <h3>Transactions</h3>
        <p><?= count($amounts) ?></p>
    </div>
</div>

<!-- CHARTS -->
<div class="chart-box">
    <h3>📈 Monthly Trend</h3>
    <canvas id="lineChart"></canvas>
</div>

<div class="chart-box">
    <h3>📊 Category Bar</h3>
    <canvas id="barChart"></canvas>
</div>

<div class="chart-box">
    <h3>🥧 Category Breakdown</h3>
    <canvas id="pieChart"></canvas>
</div>

</main>
</div>

<!-- ================= CHART SCRIPT ================= -->
<script>
document.addEventListener("DOMContentLoaded", function () {

const textColor = "#e2e8f0";
const gridColor = "rgba(255,255,255,0.05)";

// 🔥 GRADIENT (LINE)
const ctxLine = document.getElementById('lineChart').getContext('2d');
const gradientLine = ctxLine.createLinearGradient(0, 0, 0, 300);
gradientLine.addColorStop(0, "rgba(99,102,241,0.6)");
gradientLine.addColorStop(1, "rgba(99,102,241,0)");

new Chart(ctxLine, {
    type: 'line',
    data: {
        labels: <?= json_encode($months) ?>,
        datasets: [{
            label: 'Monthly Spending',
            data: <?= json_encode($totals) ?>,
            borderColor: '#6366f1',
            backgroundColor: gradientLine,
            fill: true,
            tension: 0.4,
            pointRadius: 4,
            pointHoverRadius: 6
        }]
    },
    options: {
        animation: {
            duration: 1500,
            easing: 'easeOutQuart'
        },
        plugins: {
            legend: { labels: { color: textColor } },
            tooltip: {
                backgroundColor: "#020617",
                titleColor: "#fff",
                bodyColor: "#cbd5f5",
                borderColor: "#6366f1",
                borderWidth: 1
            }
        },
        scales: {
            x: {
                ticks: { color: textColor },
                grid: { color: gridColor }
            },
            y: {
                ticks: { color: textColor },
                grid: { color: gridColor }
            }
        }
    }
});

// 🔥 BAR (ROUNDED + SHADOW LOOK)
new Chart(document.getElementById('barChart'), {
    type: 'bar',
    data: {
        labels: <?= json_encode($categories) ?>,
        datasets: [{
            data: <?= json_encode($amounts) ?>,
            backgroundColor: '#22c55e',
            borderRadius: 14,
            hoverBackgroundColor: '#16a34a'
        }]
    },
    options: {
        animation: { duration: 1200 },
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: "#020617",
                bodyColor: "#fff"
            }
        },
        scales: {
            x: { ticks: { color: textColor }, grid: { display: false } },
            y: { ticks: { color: textColor }, grid: { color: gridColor } }
        }
    }
});

// 🔥 PIE (MODERN DOUGHNUT)
new Chart(document.getElementById('pieChart'), {
    type: 'doughnut',
    data: {
        labels: <?= json_encode($categories) ?>,
        datasets: [{
            data: <?= json_encode($amounts) ?>,
            backgroundColor: [
                '#6366f1','#22c55e','#f59e0b','#ef4444','#0ea5e9'
            ],
            borderWidth: 0
        }]
    },
    options: {
        cutout: "70%",
        animation: { animateScale: true },
        plugins: {
            legend: {
                position: "bottom",
                labels: { color: textColor, padding: 20 }
            }
        }
    }
});

});
</script>

</body>
</html>