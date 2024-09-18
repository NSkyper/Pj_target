<?php
require './config/db_connection.php';

try {
    // สร้าง PDO instance
    $pdo = new PDO($dsn, $username, $password, $options);

    // รับค่าพารามิเตอร์จาก URL
    $year = isset($_GET['year']) ? $_GET['year'] : null;
    $branch_name = isset($_GET['branch_name']) ? $_GET['branch_name'] : null;

    if (!$year || !$branch_name) {
        die("Missing required parameters.");
    }

    // รับ branch_id จาก branch_name
    $sql = "SELECT branch_id FROM branch WHERE branch_name = :branch_name";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':branch_name', $branch_name, PDO::PARAM_STR);
    $stmt->execute();
    $branch = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$branch) {
        die('Branch not found.');
    }

    $branch_id = $branch['branch_id'];

    // ลบข้อมูลจากตาราง target
    $sql = "DELETE FROM target WHERE target_year = :year AND branch_id = :branch_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':year', $year, PDO::PARAM_INT);
    $stmt->bindParam(':branch_id', $branch_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo "Record deleted successfully";
    } else {
        echo "Error: " . $stmt->errorInfo()[2];
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

header("Location: test.php"); // เปลี่ยนเส้นทางกลับไปที่หน้าหลัก
exit;
?>
