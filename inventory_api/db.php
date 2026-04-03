<?php
$host = "sql100.infinityfree.com";
$db_name = "if0_41572653_inventory";
$username = "if0_41572653";
$password = "Magyza123"; 

try {
    
    $conn = new PDO("mysql:host=" . $host . ";dbname=" . $db_name . ";charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $exception) {
    echo "Connection error: " . $exception->getMessage();
}
?>