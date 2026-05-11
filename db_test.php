<?php
header('Content-Type: text/plain; charset=UTF-8');
error_reporting(E_ALL);
ini_set('display_errors', 1);

/*
املئي القيم من ملف إعدادات الموقع:
DB_HOST
DB_NAME
DB_USER
DB_PASS
*/

$host = 'localhost';
$db   = 'buyform1_web';
$user = 'buyform1_web';
$pass = 'gE9MXNgeEUut';



$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("DB CONNECTION FAILED: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

echo "DB CONNECTED OK\n";

/*
إذا كنتِ تعرفين البريفكس اكتبيه هنا
مثال: site_products أو bf_products
*/
$prefix = 'maa';

$sql = "SELECT COUNT(*) AS total FROM {$prefix}_products";
$result = $conn->query($sql);

if (!$result) {
    die("QUERY FAILED: " . $conn->error);
}

$row = $result->fetch_assoc();

echo "PRODUCTS TABLE OK\n";
echo "TOTAL PRODUCTS: " . ($row['total'] ?? 0) . "\n";