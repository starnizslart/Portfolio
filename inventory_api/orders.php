<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET");

include_once 'db.php';
$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'POST') {
    $data = json_decode(file_get_contents("php://input"));
    if(!empty($data->user_id) && !empty($data->product_id) && !empty($data->product_name) && !empty($data->price)) {
        try {
            $checkStock = $conn->prepare("SELECT stock FROM products WHERE id = :pid");
            $checkStock->execute(['pid' => $data->product_id]);
            $prod = $checkStock->fetch(PDO::FETCH_ASSOC);

            if($prod && $prod['stock'] > 0) {
                $conn->beginTransaction();
                
                $query = "INSERT INTO orders (user_id, product_id, product_name, price) VALUES (:uid, :pid, :pname, :price)";
                $stmt = $conn->prepare($query);
                $stmt->execute(['uid' => $data->user_id, 'pid' => $data->product_id, 'pname' => $data->product_name, 'price' => $data->price]);
                
                $updateStock = $conn->prepare("UPDATE products SET stock = stock - 1 WHERE id = :pid");
                $updateStock->execute(['pid' => $data->product_id]);
                
                $conn->commit();
                http_response_code(201);
                echo json_encode(["message" => "สั่งซื้อสำเร็จ! ตัดสต็อกเรียบร้อย"]);
            } else {
                http_response_code(400);
                echo json_encode(["message" => "ขออภัย สินค้าหมดสต็อก!"]);
            }
        } catch (PDOException $e) {
            $conn->rollBack();
            http_response_code(503);
            echo json_encode(["message" => "ข้อผิดพลาดระบบ: " . $e->getMessage()]);
        }
    }
} else if ($method == 'GET') {
    // ถ้ามีการส่ง user_id มา จะดึงเฉพาะของ user นั้น ถ้าไม่มี(แอดมิน) ดึงทั้งหมด
    if(isset($_GET['user_id'])) {
        $stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = :uid ORDER BY id DESC");
        $stmt->execute(['uid' => $_GET['user_id']]);
    } else {
        $stmt = $conn->prepare("SELECT * FROM orders ORDER BY id DESC LIMIT 50"); // แอดมินดู 50 ล่าสุด
        $stmt->execute();
    }
    
    $orders_arr = array();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) { array_push($orders_arr, $row); }
    echo json_encode(["data" => $orders_arr]);
}
?>