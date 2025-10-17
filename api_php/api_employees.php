<?php 
include 'condb.php';

$action = $_POST['action'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action) {
    // เพิ่ม / แก้ไข / ลบ
    switch($action) {

        case 'add':
            $first_name = $_POST['first_name'];
            $last_name = $_POST['last_name'];
            $username = $_POST['username'];
            $password = $_POST['password'];

            // อัพโหลดไฟล์รูป
            $filename = null;
            if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === 0) {
                $targetDir = "uploads/"; // โฟลเดอร์ที่เก็บไฟล์
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0777, true); // สร้างโฟลเดอร์ถ้ายังไม่มี
                }
                $filename = time() . '_' . basename($_FILES['profile_picture']['name']); // ตั้งชื่อไฟล์
                $targetFile = $targetDir . $filename; // พาธไฟล์
                move_uploaded_file($_FILES['profile_picture']['tmp_name'], $targetFile); // อัพโหลดไฟล์
            }

            // คำสั่ง SQL เพื่อเพิ่มข้อมูลลูกจ้าง
            $sql = "INSERT INTO employees (first_name, last_name, username, password , profile_picture)
                    VALUES (:first_name, :last_name, :username, :password , :profile_picture)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':first_name', $first_name);
            $stmt->bindParam(':last_name', $last_name);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password', $password);
            $stmt->bindParam(':profile_picture', $filename); // เก็บชื่อไฟล์ภาพ

            if ($stmt->execute()) {
                echo json_encode(["message" => "เพิ่มข้อมูลลูกจ้างสำเร็จ"]);
            } else {
                echo json_encode(["error" => "เพิ่มข้อมูลลูกจ้างล้มเหลว"]);
            }
            break;

        case 'update':
            $employee_id = $_POST['employee_id'];
            $first_name = $_POST['first_name'];
            $last_name = $_POST['last_name'];
            $username = $_POST['username'];
            $password = $_POST['password'];

            // อัพโหลดไฟล์รูป
            $filename = null;
            if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === 0) {
                $targetDir = "uploads/"; // โฟลเดอร์ที่เก็บไฟล์
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0777, true); // สร้างโฟลเดอร์ถ้ายังไม่มี
                }
                $filename = time() . '_' . basename($_FILES['profile_picture']['name']); // ตั้งชื่อไฟล์
                $targetFile = $targetDir . $filename; // พาธไฟล์
                move_uploaded_file($_FILES['profile_picture']['tmp_name'], $targetFile); // อัพโหลดไฟล์
            }

            // คำสั่ง SQL เพื่ออัพเดตข้อมูลลูกจ้าง
            $sql = "UPDATE employees SET first_name = :first_name, last_name = :last_name, 
                    username = :username, password = :password, profile_picture = :profile_picture
                    WHERE employee_id = :employee_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':employee_id', $employee_id);
            $stmt->bindParam(':first_name', $first_name);
            $stmt->bindParam(':last_name', $last_name);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password', $password);
            $stmt->bindParam(':profile_picture', $filename);

            if ($stmt->execute()) {
                echo json_encode(["message" => "อัพเดตข้อมูลลูกจ้างสำเร็จ"]);
            } else {
                echo json_encode(["error" => "อัพเดตข้อมูลลูกจ้างล้มเหลว"]);
            }
            break;

        case 'delete':
            $employee_id = $_POST['employee_id'];

            // คำสั่ง SQL เพื่อลบข้อมูลลูกจ้าง
            $sql = "DELETE FROM employees WHERE employee_id = :employee_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':employee_id', $employee_id);

            if ($stmt->execute()) {
                echo json_encode(["message" => "ลบข้อมูลลูกจ้างสำเร็จ"]);
            } else {
                echo json_encode(["error" => "ลบข้อมูลลูกจ้างล้มเหลว"]);
            }
            break;

        default:
            echo json_encode(["error" => "Action ไม่ถูกต้อง"]);
            break;
    }

} else {
    // GET: ดึงข้อมูลลูกจ้าง
    $stmt = $conn->prepare("SELECT * FROM employees ORDER BY employee_id DESC");
    if ($stmt->execute()) {
        $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(["success" => true, "data" => $employees]);
    } else {
        echo json_encode(["success" => false, "data" => []]);
    }
}
?>
