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



// ดึงข้อมูลปี
$yearsQuery = "SELECT DISTINCT target_year FROM target ORDER BY target_year DESC";
$yearsResult = $conn->query($yearsQuery);
$years = $yearsResult->fetchAll(PDO::FETCH_COLUMN);

// ดึงข้อมูลสาขา
$branchesQuery = "SELECT DISTINCT branch_name FROM branch ORDER BY branch_name";
$branchesResult = $conn->query($branchesQuery);
$branches = $branchesResult->fetchAll(PDO::FETCH_COLUMN);

// รับค่าฟิลเตอร์ปีและสาขา
$year_filter = isset($_POST['year_filter']) ? $_POST['year_filter'] : '';
$branch_filter = isset($_POST['branch_filter']) ? $_POST['branch_filter'] : '';

// Query สำหรับดึงข้อมูลยอดขาย
$sql = "SELECT t.target_year, b.branch_name, t.target_month, t.target_amt AS target_amount
        FROM target t
        JOIN branch b ON t.branch_id = b.branch_id
        ORDER BY t.target_year, t.target_month, b.branch_name";
$result = $conn->query($sql);
$sales_data = $result->fetchAll(PDO::FETCH_ASSOC);
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
            height: 100vh;
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
            <a href="index.php" class="menu-item">หน้าแรก</a>
            <a href="Add information.php" class="menu-item">เพิ่มข้อมูล</a>
            <a href="setting.php" class="menu-item">การตั้งค่าสิทธิ์</a>
            <a href="setting2.php" class="menu-item">ตั้งค่า2</a>
            <a href="averaging.php" class="menu-item">การเฉลี่ยยอด</a>
        </div>
    </nav>
    <div class="container">
        <div class="row justify-content-center align-item-center">
            <div class="card mt-5 w-75 text-center">
                <div class="card-body py-5 pt-5">
                    <h3>ตั้งค่าเป้ายอดขาย</h3>
                    <h5>เลือกวิธีการตั้งค่าเป้าหมาย</h5>
                    <div class="row justify-content-center align-item-center col-sm-12">
                        <button class="btn btn-primary mt-2 w-75" onclick="window.location.href='target_now.php'">กรอกเป้าหมายยอดขาย</button>
                        <button class="btn btn-warning mt-2 w-75">ใช้เป้าหมายจากปีที่แล้ว</button>
                        <button class="btn btn-danger mt-2 w-75">คำนวณจากยอดขายจริงของปีที่แล้ว</button>
                    </div>
                </div>
            </div>
            <button class="btn btn-success w-50 mt-2" onclick="toggleTable()">แสดง/ซ่อน ตารางยอดขาย</button>
            <div id="filterContainer">
                <label for="yearFilter">ปี:</label>
                <select id="yearFilter" name="year_filter" onchange="filterTable()">
                    <option value="">ทั้งหมด</option>
                    <?php foreach ($years as $year): ?>
                        <option value="<?php echo htmlspecialchars($year); ?>" <?php echo $year == $year_filter ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($year); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="branchFilter">สาขา:</label>
                <select id="branchFilter" name="branch_filter" onchange="filterTable()">
                    <option value="">ทั้งหมด</option>
                    <?php foreach ($branches as $branch): ?>
                        <option value="<?php echo htmlspecialchars($branch); ?>" <?php echo $branch == $branch_filter ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($branch); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="monthFilter">เดือน:</label>
                <select id="monthFilter" onchange="filterTable()">
                    <option value="">ทั้งหมด</option>
                    <?php
                    $months = [
                        "มกราคม",
                        "กุมภาพันธ์",
                        "มีนาคม",
                        "เมษายน",
                        "พฤษภาคม",
                        "มิถุนายน",
                        "กรกฎาคม",
                        "สิงหาคม",
                        "กันยายน",
                        "ตุลาคม",
                        "พฤศจิกายน",
                        "ธันวาคม"
                    ];
                    foreach ($months as $index => $month):
                    ?>
                        <option value="<?php echo $index + 1; ?>"><?php echo $month; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <table id="salesTable" border="1" class="table table">
                <thead>
                    <tr>
                        <th>ปี</th>
                        <th>สาขา</th>
                        <th>เดือน</th>
                        <th>เป้ายอดขาย</th>
                        <th>แก้ไข</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sales_data as $row): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['target_year']); ?></td>
                            <td><?php echo htmlspecialchars($row['branch_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['target_month']); ?></td>
                            <td><?php echo htmlspecialchars($row['target_amount']); ?></td>
                            <td><a href="update.php?year=<?php echo urlencode($row['target_year']); ?>&branch_name=<?php echo urlencode($row['branch_name']); ?>&month=<?php echo urlencode($row['target_month']); ?>">แก้ไข</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>


    <script>
        function filterTable() {
            var yearFilter = document.getElementById('yearFilter').value;
            var branchFilter = document.getElementById('branchFilter').value;
            var monthFilter = document.getElementById('monthFilter').value;

            var table = document.getElementById('salesTable');
            var rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

            for (var i = 0; i < rows.length; i++) {
                var cells = rows[i].getElementsByTagName('td');
                var year = cells[0].textContent || cells[0].innerText;
                var branch = cells[1].textContent || cells[1].innerText;
                var month = cells[2].textContent || cells[2].innerText;

                var showRow = true;

                if (yearFilter && year !== yearFilter) {
                    showRow = false;
                }
                if (branchFilter && branch !== branchFilter) {
                    showRow = false;
                }
                if (monthFilter && month !== monthFilter) {
                    showRow = false;
                }

                rows[i].style.display = showRow ? '' : 'none';
            }
        }

        function toggleTable() {
            var table = document.getElementById('salesTable');
            // เช็คสถานะการแสดงผลปัจจุบันและเปลี่ยนแปลง
            if (table.style.display === 'none' || table.style.display === '') {
                table.style.display = 'table';
            } else {
                table.style.display = 'none';
            }
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