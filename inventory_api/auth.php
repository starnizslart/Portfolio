<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

include_once 'db.php';
$data = json_decode(file_get_contents("php://input"));

if(isset($data->action)) {
    // ---- สมัครสมาชิก ----
    if($data->action == 'register') {
        if(!empty($data->fullname) && !empty($data->username) && !empty($data->password)) {
            $check = $conn->prepare("SELECT id FROM users WHERE username = :user");
            $check->execute(['user' => $data->username]);
            if($check->rowCount() > 0) {
                http_response_code(400);
                echo json_encode(["message" => "Username นี้มีในระบบแล้ว!"]);
                exit();
            }

            // บันทึกรหัสผ่านเป็นตัวอักษรธรรมดาตามที่ขอ (Plain text)
            $query = "INSERT INTO users SET fullname=:name, username=:user, password=:pass";
            $stmt = $conn->prepare($query);
            if($stmt->execute(['name' => $data->fullname, 'user' => $data->username, 'pass' => $data->password])) {
                http_response_code(201);
                echo json_encode(["message" => "สมัครสมาชิกสำเร็จ! กรุณาเข้าสู่ระบบ"]);
            } else {
                http_response_code(503);
                echo json_encode(["message" => "เกิดข้อผิดพลาดในการบันทึกข้อมูล"]);
            }
        }
    } 
    // ---- เข้าสู่ระบบ ----
    else if($data->action == 'login') {
        if(!empty($data->username) && !empty($data->password)) {
            $query = "SELECT id, fullname, username, password, role FROM users WHERE username = :user";
            $stmt = $conn->prepare($query);
            $stmt->execute(['user' => $data->username]);

            if($stmt->rowCount() > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                // เช็ครหัสผ่านแบบตรงๆ (ไม่เข้ารหัส)
                if($data->password === $row['password']) {
                    http_response_code(200);
                    echo json_encode([
                        "message" => "เข้าสู่ระบบสำเร็จ",
                        "user" => ["id" => $row['id'], "fullname" => $row['fullname'], "role" => $row['role']]
                    ]);
                } else {
                    http_response_code(401);
                    echo json_encode(["message" => "รหัสผ่านไม่ถูกต้อง!"]);
                }
            } else {
                http_response_code(404);
                echo json_encode(["message" => "ไม่พบผู้ใช้งานนี้!"]);
            }
        }
    }
}
?>