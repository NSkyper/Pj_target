<?php
require './config/db_connection.php';

// รับค่าจาก POST
$year = isset($_POST['year']) ? $_POST['year'] : null;
$branch_name = isset($_POST['branch_name']) ? $_POST['branch_name'] : null;
$month = isset($_POST['month']) ? $_POST['month'] : null;
$target_amount = isset($_POST['target_amount']) ? $_POST['target_amount'] : null;

// ตรวจสอบว่าพารามิเตอร์ทั้งหมดมีค่าหรือไม่
if (!$year || !$branch_name || !$month || !$target_amount) {
    die("Missing required parameters.");
}

try {
    // สร้าง PDO instance
    $pdo = new PDO($dsn, $username, $password, $options);

    // ค้นหา branch_id จาก branch_name
    $branch_sql = "SELECT branch_id FROM branch WHERE branch_name = :branch_name";
    $stmt = $pdo->prepare($branch_sql);
    $stmt->bindParam(':branch_name', $branch_name);
    $stmt->execute();
    $branch = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$branch) {
        die('Branch not found.');
    }

    $branch_id = $branch['branch_id'];

    // อัปเดตเป้าหมายตามปี, branch_id และเดือน
    $update_sql = "UPDATE target SET target_amt = :target_amount WHERE target_year = :year AND branch_id = :branch_id AND target_month = :month";
    $stmt = $pdo->prepare($update_sql);
    $stmt->bindParam(':target_amount', $target_amount, PDO::PARAM_STR);
    $stmt->bindParam(':year', $year, PDO::PARAM_INT);
    $stmt->bindParam(':branch_id', $branch_id, PDO::PARAM_INT);
    $stmt->bindParam(':month', $month, PDO::PARAM_INT);

    if ($stmt->execute()) {
        // อัปเดตสำเร็จ เปลี่ยนเส้นทางไปหน้าหลัก
        header("Location: test.php");
        exit();  // ต้องใช้ exit() เพื่อหยุดการทำงานหลังจากเปลี่ยนเส้นทาง
    } else {
        echo "Error updating target.";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
