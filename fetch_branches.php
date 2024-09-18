<?php
require './config/db_connection.php';

if (isset($_POST['areazone_id'])) {
    $areazone_id = $_POST['areazone_id'];

    try {
        // เตรียมคำสั่ง SQL โดยใช้ PDO
        $query = "SELECT * FROM branch WHERE areazone_id = :areazone_id";
        $stmt = $conn->prepare($query);
        
        // Bind ค่าที่ต้องการส่งเข้าไปใน SQL
        $stmt->bindParam(':areazone_id', $areazone_id, PDO::PARAM_INT);
        
        // Execute คำสั่ง
        $stmt->execute();

        // ตรวจสอบว่ามีผลลัพธ์หรือไม่
        if ($stmt->rowCount() > 0) {
            echo '<option value="">-- เลือกสาขา --</option>';
            
            // วนลูปเพื่อดึงข้อมูล
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo '<option value="' . $row['branch_id'] . '">' . $row['branch_name'] . '</option>';
            }
        } else {
            echo '<option value="">ไม่มีสาขา</option>';
        }
    } catch (PDOException $e) {
        // Handle exception หากเกิดข้อผิดพลาด
        echo '<option value="">เกิดข้อผิดพลาด: ' . $e->getMessage() . '</option>';
    }
}
?>

