<?php
require './config/db_connection.php';

// รับค่าพารามิเตอร์จาก URL
$year = isset($_GET['year']) ? $_GET['year'] : null;
$branch_name = isset($_GET['branch_name']) ? $_GET['branch_name'] : null;
$month = isset($_GET['month']) ? $_GET['month'] : null;

// ตรวจสอบว่าพารามิเตอร์ทั้งหมดมีค่าหรือไม่
if (!$year || !$branch_name || !$month) {
    die("Missing required parameters.");
}

try {
    // สร้าง PDO instance
    $pdo = new PDO($dsn, $username, $password, $options);

    // ค้นหา branch_id จาก branch_name
    $branch_sql = "SELECT branch_id FROM branch WHERE branch_name = :branch_name";
    $stmt = $pdo->prepare($branch_sql);
    $stmt->bindParam(':branch_name', $branch_name, PDO::PARAM_STR);
    $stmt->execute();
    $branch = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$branch) {
        die('Branch not found.');
    }

    $branch_id = $branch['branch_id'];

    // ค้นหาข้อมูลเป้าหมายตามปี, branch_id และเดือน
    $target_sql = "SELECT target_amt FROM target WHERE target_year = :year AND branch_id = :branch_id AND target_month = :month";
    $stmt = $pdo->prepare($target_sql);
    $stmt->bindParam(':year', $year, PDO::PARAM_INT);
    $stmt->bindParam(':branch_id', $branch_id, PDO::PARAM_INT);
    $stmt->bindParam(':month', $month, PDO::PARAM_INT);
    $stmt->execute();
    $target = $stmt->fetch(PDO::FETCH_ASSOC);

    // ตรวจสอบว่าพบข้อมูลเป้าหมายหรือไม่
    if (!$target) {
        die('No target data found.');
    }

    $target_amount = $target['target_amt'];
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Target for Month</title>
</head>
<body>
    <h1>Update Target for <?php echo date('F', mktime(0, 0, 0, $month, 1)); ?></h1>
    
    <form action="update_process.php" method="POST">
        <input type="hidden" name="year" value="<?php echo htmlspecialchars($year); ?>">
        <input type="hidden" name="branch_name" value="<?php echo htmlspecialchars($branch_name); ?>">
        <input type="hidden" name="month" value="<?php echo htmlspecialchars($month); ?>">

        <label for="target_amount">Target Amount:</label>
        <input type="text" name="target_amount" id="target_amount" value="<?php echo htmlspecialchars($target_amount); ?>">

        <button type="submit">Update</button>
    </form>

</body>
</html>
