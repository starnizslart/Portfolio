<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once 'db.php';

$query = "SELECT * FROM products ORDER BY id DESC";
$stmt = $conn->prepare($query);
$stmt->execute();

$num = $stmt->rowCount();

if($num > 0) {
    $products_arr = array();
    $products_arr["data"] = array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $product_item = array(
            "id" => $row['id'],
            "name" => $row['name'],
            "category" => $row['category'],
            "price" => $row['price'],
            "stock" => $row['stock'],
            "created_at" => $row['created_at'],
            "image" => $row['image'] 
        );
        array_push($products_arr["data"], $product_item);
    }
    
    http_response_code(200);
    echo json_encode($products_arr);
} else {
    http_response_code(404);
    echo json_encode(array("message" => "ไม่พบข้อมูลสินค้า."));
}
?>