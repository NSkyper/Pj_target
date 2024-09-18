<?php

session_start();
require './config/db_connection.php';

if (isset($_POST['save_target'])) {
    $username_id = $_POST['username_id'];
    $year = $_POST['year'];
    $areazone = $_POST['areazone'];
    $branch = $_POST['branch'];
    $annual_target = $_POST['annual_target'];
    $type = 'target';
    $status = 'i';
    $old_amt = '0';
    $update_date = date('Y-m-d H:i:s');

    if (empty($year)) {
        $_SESSION['error'] = 'กรุณาเลือกปี';
        header('location: target_now.php');
    } else if (empty($areazone)) {
        $_SESSION['error'] = 'กรุณาareazone';
        header('location: target_now.php');
    } else if (empty($branch)) {
        $_SESSION['error'] = 'กรุณาbranch';
        header('location: target_now.php');
    } else if (empty($annual_target)) {
        $_SESSION['error'] = 'กรุณากรอกเป้าหมาย';
        header('location: target_now.php');
    } else {
        try {
            $check_data = $conn->prepare('SELECT create_by FROM log WHERE create_by =:create_by');
            $check_data->bindParam(':create_by', $username_id);
            $check_data->execute();
            $row = $check_data->fetch(PDO::FETCH_ASSOC);
            if ($row['create_by'] == $username_id) {
                $_SESSION['warning'] = 'คุณได้ทำการกำหนด target ไว้แล้ว';
                header('location: target_now.php');
            } else if (!isset($_SESSION['warning'])){
                $stmt = $conn->prepare('INSERT INTO log (type, status, year_log, month_log, branch_name, amt, old_amt, create_by, update_date) 
                VALUES (:type, :status, :year_log, :month_log, :branch_name, :amt, :old_amt, :create_by, :update_date)');

                for ($i = 1; $i <= 12; $i++) {
                    $target_key = "target_" . $i;
                    $target_value = $_POST[$target_key];

                    $stmt->bindParam(':type', $type);
                    $stmt->bindParam(':status', $status);
                    $stmt->bindParam(':year_log', $year);
                    $stmt->bindParam(':month_log', $i);
                    $stmt->bindParam(':branch_name', $branch);
                    $stmt->bindParam(':amt', $target_value);
                    $stmt->bindParam(':old_amt', $old_amt);
                    $stmt->bindParam(':create_by', $username_id);
                    $stmt->bindParam(':update_date', $update_date);

                    $stmt->execute();
                }

                $_SESSION['success'] = 'บันทึกสำเร็จ';
                header('location: target_now.php');
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
}
