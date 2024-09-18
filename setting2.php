<?php
session_start();
require './config/db_connection.php';

$log_target = 'SELECT * FROM log';
$stmt = $conn->prepare($log_target);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <?php
    echo "<table border='1'>
    <tr>
        <th>Type</th>
        <th>Status</th>
        <th>Year</th>
        <th>Month</th>
        <th>Branch Name</th>
        <th>Amount</th>
        <th>Old Amount</th>
        <th>Created By</th>
        <th>Update Date</th>
    </tr>";

    foreach ($results as $row) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['type']) . "</td>";
        echo "<td>" . htmlspecialchars($row['status']) . "</td>";
        echo "<td>" . htmlspecialchars($row['year_log']) . "</td>";
        echo "<td>" . htmlspecialchars($row['month_log']) . "</td>";
        echo "<td>" . htmlspecialchars($row['branch_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['amt']) . "</td>";
        echo "<td>" . htmlspecialchars($row['old_amt']) . "</td>";
        echo "<td>" . htmlspecialchars($row['create_by']) . "</td>";
        echo "<td>" . htmlspecialchars($row['update_date']) . "</td>";
        echo "</tr>";
    }

    echo "</table>";
    ?>
</body>

</html>