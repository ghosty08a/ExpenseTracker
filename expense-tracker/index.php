<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

include 'db.php';

/* =========================
   DASHBOARD DATA
========================= */
$total = $conn->query("SELECT SUM(amount) as total FROM expenses")
    ->fetch_assoc()['total'] ?? 0;

$today = date('Y-m-d');
$todayTotal = $conn->query("SELECT SUM(amount) as total FROM expenses WHERE date='$today'")
    ->fetch_assoc()['total'] ?? 0;

$month = date('Y-m');
$monthTotal = $conn->query("SELECT SUM(amount) as total FROM expenses WHERE date LIKE '$month%'")
    ->fetch_assoc()['total'] ?? 0;

/* =========================
   CHART DATA
========================= */
$data = $conn->query("
    SELECT category, SUM(amount) as total 
    FROM expenses 
    GROUP BY category
");

$categories = [];
$amounts = [];

while ($row = $data->fetch_assoc()) {
    $categories[] = $row['category'];
    $amounts[] = $row['total'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - Expense Tracker</title>

    <!-- FORCE CSS RELOAD -->
    <link rel="stylesheet" href="style.css?v=2">

    <!-- CHART -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>

<div class="app">

    <!-- SIDEBAR -->
    <aside class="sidebar">
        <h2 class="logo">💰 Tracker</h2>

        <nav class="menu">
            <a href="index.php" class="active">🏠 Dashboard</a>
            <a href="reports.php">📊 Reports</a>
            <a href="settings.php">⚙️ Settings</a>
            <a href="logout.php" class="logout">🚪 Logout</a>
        </nav>
    </aside>

    <!-- MAIN -->
    <main class="main">

        <!-- HEADER -->
        <header class="topbar">
            <div>
                <h2>Dashboard</h2>
                <small style="color:#cbd5f5;">
                    Welcome back, <?= $_SESSION['user'] ?>
                </small>
            </div>
        </header>

        <!-- OVERVIEW -->
        <section>
            <h3 class="section-title">📊 Spending Overview</h3>

            <div class="cards">
                <div class="card">
                    <h3>Total</h3>
                    <p>₱ <?= number_format($total, 2) ?></p>
                </div>

                <div class="card">
                    <h3>Today</h3>
                    <p>₱ <?= number_format($todayTotal, 2) ?></p>
                </div>

                <div class="card">
                    <h3>This Month</h3>
                    <p>₱ <?= number_format($monthTotal, 2) ?></p>
                </div>
            </div>
        </section>

        <!-- FORM -->
        <section class="form-box">
            <h3>Add Expense</h3>

            <form method="POST" action="add.php">
                <input type="number" step="0.01" name="amount" placeholder="Amount" required>
                <input type="text" name="category" placeholder="Category">
                <input type="text" name="description" placeholder="Description">
                <input type="date" name="date" required>
                <button type="submit">Add Expense</button>
            </form>
        </section>

        <!-- CHART -->
        <section class="chart-box">
            <h3>Expenses by Category</h3>
            <canvas id="expenseChart"></canvas>
        </section>

        <!-- TABLE -->
        <section class="table-box">
            <h3>Recent Expenses</h3>

            <table>
                <thead>
                    <tr>
                        <th>Amount</th>
                        <th>Category</th>
                        <th>Description</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                <?php
                $result = $conn->query("SELECT * FROM expenses ORDER BY date DESC");

                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                        <td>₱ {$row['amount']}</td>
                        <td>{$row['category']}</td>
                        <td>{$row['description']}</td>
                        <td>{$row['date']}</td>
                        <td><a class='delete-btn' href='delete.php?id={$row['id']}'>Delete</a></td>
                    </tr>";
                }
                ?>
                </tbody>
            </table>
        </section>

    </main>
</div>

<!-- CHART SCRIPT -->
<script>
const ctx = document.getElementById('expenseChart');

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?= json_encode($categories) ?>,
        datasets: [{
            label: 'Expenses',
            data: <?= json_encode($amounts) ?>,
            backgroundColor: '#6366f1',
            borderRadius: 12
        }]
    },
    options: {
        animation: {
            duration: 1200,
            easing: 'easeOutQuart'
        },
        scales: {
            x: {
                ticks: { color: "#cbd5f5" },
                grid: { color: "rgba(255,255,255,0.05)" }
            },
            y: {
                ticks: { color: "#cbd5f5" },
                grid: { color: "rgba(255,255,255,0.05)" }
            }
        },
        plugins: {
            legend: {
                labels: { color: "#cbd5f5" }
            }
        }
    }
});
</script>

</body>
</html>