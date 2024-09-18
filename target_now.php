<?php
session_start();
require_once './config/db_connection.php';

// ตรวจสอบว่าผู้ใช้ได้เข้าสู่ระบบหรือไม่
if (!isset($_SESSION['user_login'])) {
    $_SESSION['error'] = 'กรุณาเข้าสู่ระบบ!';
    header('location: signin.php');
    exit();
}

$username = $_SESSION['user_login'];

try {
    // ใช้ prepared statement เพื่อป้องกัน SQL Injection
    $stmt = $conn->prepare("SELECT * FROM users WHERE username_id = :username_id");
    $stmt->bindParam(':username_id', $username, PDO::PARAM_STR);
    $stmt->execute();

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        $_SESSION['error'] = 'ข้อมูลผู้ใช้ไม่ถูกต้อง!';
        header('location: signin.php');
        exit();
    }
} catch (PDOException $e) {
    $_SESSION['error'] = 'เกิดข้อผิดพลาดในการดึงข้อมูลผู้ใช้: ' . $e->getMessage();
    header('location: signin.php');
    exit();
}
?>
<!doctype html>
<html lang="en">

<head>
    <title>Title</title>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <!-- Bootstrap CSS v5.2.1 -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN"
        crossorigin="anonymous" />
    <style>
        body {
            font-family: 'Kanit', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            background-color: #f0f0f0;
        }

        .sidebar {
            width: 250px;
            background-color: #0066cc;
            color: white;
            padding: 20px;
            height: 150vh;
        }

        .profile-pic {
            width: 120px;
            height: 120px;
            background-color: white;
            border-radius: 15px;
            margin: 0 auto 20px;
        }

        .user-info {
            text-align: center;
            margin-bottom: 30px;
        }

        .menu-item {
            display: block;
            padding: 12px 15px;
            margin-bottom: 10px;
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .menu-item:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }
    </style>
</head>

<body>
    <nav>
        <div class="sidebar">
            <div class="profile-pic"></div>
            <div class="user-info">
                <span>Welcome, <?php echo $row['firstname'] . ' ' . $row['lastname'] ?></span>
                <p>ตำแหน่ง</p>
            </div>
            <a href="target.php" class="menu-item">หน้าแรก</a>
            <a href="Add information.php" class="menu-item">เพิ่มข้อมูล</a>
            <a href="setting.php" class="menu-item">การตั้งค่าสิทธิ์</a>
            <a href="setting2.php" class="menu-item">ตั้งค่า2</a>
            <a href="averaging.php" class="menu-item">การเฉลี่ยยอด</a>
        </div>
    </nav>
    <div class="container">
        <div class="row justify-content-center align-item-center">
            <div class="card mt-5 w-100 text-center">
                <div class="card-body">
                    <h3>ตั้งค่าเป้ายอดขาย</h3>
                    <h5>กรอกข้อมูล</h5>
                    <div class="container">
                        <form action="targetnow_db.php" method="post">
                            <div class="form-group row mt-3 col-sm-12">
                                <label for="" class="col-sm-1 col-form-label">ปี</label>
                                <div class="col-sm-2">
                                    <select id="year" name="year" class="form-select w-50">
                                        <option value="2024">2024</option>
                                        <option value="2025">2025</option>
                                        <option value="2026">2026</option>
                                    </select>
                                </div>
                                <label for="" class="col-sm-2 col-form-label">AreaZones</label>
                                <div class="col-sm-3">
                                    <select id="areazone" name="areazone" class="form-select w-50">
                                        <option value="AreaZone 1">AreaZone 1</option>
                                    </select>
                                </div>
                                <label for="" class="col-sm-1 col-form-label">สาขา</label>
                                <div class="col-sm-3">
                                    <select id="branch" name="branch" class="form-select w-50">
                                        <option value="Pinklao">Pinklao</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-floating mt-3">
                                <input type="number" id="annual_target" class="form-control w-50" name="annual_target" step="0.01" value="10,000" placeholder="เป้าหมาย" oninput="updateMonthlyTargets()">
                                <label for="floatingInput">เป้าหมายยอดขายรายปี</label>
                            </div>
                            <h3>เป้าหมายยอดขายรายเดือน:</h3>

                            <table class="table table-striped">
                                <tr>
                                    <th>เดือน</th>
                                    <th>เป้าหมายยอดขาย</th>
                                </tr>
                                <tr>
                                    <td>มกราคม</td>
                                    <td><input type="number" class="form-control" id="target_1" name="target_1" step="0.01" oninput="updateTotalTarget()" disabled></td>
                                </tr>
                                <tr>
                                    <td>กุมภาพันธ์</td>
                                    <td><input type="number" class="form-control" id="target_2" name="target_2" step="0.01" oninput="updateTotalTarget()" disabled></td>
                                </tr>
                                <tr>
                                    <td>มีนาคม</td>
                                    <td><input type="number" class="form-control" id="target_3" name="target_3" step="0.01" oninput="updateTotalTarget()" disabled></td>
                                </tr>
                                <tr>
                                    <td>เมษายน</td>
                                    <td><input type="number" class="form-control" id="target_4" name="target_4" step="0.01" oninput="updateTotalTarget()" disabled></td>
                                </tr>
                                <tr>
                                    <td>พฤษภาคม</td>
                                    <td><input type="number" class="form-control" id="target_5" name="target_5" step="0.01" oninput="updateTotalTarget()" disabled></td>
                                </tr>
                                <tr>
                                    <td>มิถุนายน</td>
                                    <td><input type="number" class="form-control" id="target_6" name="target_6" step="0.01" oninput="updateTotalTarget()" disabled></td>
                                </tr>
                                <tr>
                                    <td>กรกฎาคม</td>
                                    <td><input type="number" class="form-control" id="target_7" name="target_7" step="0.01" oninput="updateTotalTarget()" disabled></td>
                                </tr>
                                <tr>
                                    <td>สิงหาคม</td>
                                    <td><input type="number" class="form-control" id="target_8" name="target_8" step="0.01" oninput="updateTotalTarget()" disabled></td>
                                </tr>
                                <tr>
                                    <td>กันยายน</td>
                                    <td><input type="number" class="form-control" id="target_9" name="target_9" step="0.01" oninput="updateTotalTarget()" disabled></td>
                                </tr>
                                <tr>
                                    <td>ตุลาคม</td>
                                    <td><input type="number" class="form-control" id="target_10" name="target_10" step="0.01" oninput="updateTotalTarget()" disabled></td>
                                </tr>
                                <tr>
                                    <td>พฤศจิกายน</td>
                                    <td><input type="number" class="form-control" id="target_11" name="target_11" step="0.01" oninput="updateTotalTarget()" disabled></td>
                                </tr>
                                <tr>
                                    <td>ธันวาคม</td>
                                    <td><input type="number" class="form-control" id="target_12" name="target_12" step="0.01" oninput="updateTotalTarget()" disabled></td>
                                </tr>
                            </table>
                            <div class="container">
                                <p>ยอดรวมเป้าหมายยอดขายรายปี: <span id="total-target" name="total-target">0</span></p>
                            </div>
                            <button class="btn btn-danger w-100">ยกเลิก</button>
                            <button type="submit" name="save_target" class="btn btn-success w-100 mt-2">บันทึก</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <script>
        function updateMonthlyTargets() {
            const annualTarget = parseFloat(document.getElementById('annual_target').value) || 0;
            const monthlyTargets = annualTarget / 12;
            const remainder = annualTarget % 12;

            for (let month = 1; month <= 12; month++) {
                const input = document.getElementById(`target_${month}`);
                if (input) {
                    const value = Math.floor((monthlyTargets + (month <= remainder ? 0.01 : 0)) * 100) / 100;
                    input.value = value.toFixed(2);
                    input.disabled = false;
                }
            }
            updateTotalTarget();
        }

        function updateTotalTarget() {
            let totalSum = 0;
            for (let month = 1; month <= 12; month++) {
                const input = document.getElementById(`target_${month}`);
                if (input) {
                    totalSum += parseFloat(input.value) || 0;
                }
            }
            document.getElementById('total-target').textContent = totalSum.toFixed(2);
        }
    </script>


    <script
        src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
        crossorigin="anonymous">
    </script>

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"
        integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+"
        crossorigin="anonymous"></script>

</body>

</html>