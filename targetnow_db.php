<?php 

session_start();
require './config/db_connection.php';
if(isset($_POST['save_target'])){
    $year = $_POST['year'];
    $areazone = $_POST['areazone'];
    $branch = $_POST['branch'];
    $annual_target = $_POST['annual_target'];
    $target_1 = $_POST['target_1'];
    $target_2 = $_POST['target_2'];
    $target_3 = $_POST['target_3'];
    $target_4 = $_POST['target_4'];
    $target_5 = $_POST['target_5'];
    $target_6 = $_POST['target_6'];
    $target_7 = $_POST['target_7'];
    $target_8 = $_POST['target_8'];
    $target_9 = $_POST['target_9'];
    $target_10 = $_POST['target_10'];
    $target_11 = $_POST['target_11'];
    $target_12 = $_POST['target_12'];
    $total_target = $_POST['total-target'];

    if(empty($year)){
        $_SESSION['error'] = 'กรุณาเลือกปี';
        header('location: target_now.php');
    }else if(empty($areazone)){
        $_SESSION['error'] = 'กรุณาareazone';
        header('location: target_now.php');
    }else if(empty($branch)){
        $_SESSION['error'] = 'กรุณาbranch';
        header('location: target_now.php');
    }else if(empty($annual_target)){
        $_SESSION['error'] = 'กรุณากรอกเป้าหมาย';
        header('location: target_now.php');
    }else{
        try{

            $check_user = $conn->prepare("SELECT * FROM users WHERE username_id = :username_id");
            $check_user->bindParam(":username_id",$username_id);
            $check_user->execute();
            $row = $check_user->fetch(PDO::FETCH_ASSOC);


        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
}

?>