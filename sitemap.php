<?php
header('Content-Type: application/xml; charset=UTF-8');

$baseUrl = 'https://www.buyformula.net';

echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

/* الصفحات الأساسية */
$pages = [
    '/',
    '/ar',
    '/en',
];

foreach ($pages as $page) {
    echo "<url>";
    echo "<loc>$baseUrl$page</loc>";
    echo "<changefreq>daily</changefreq>";
    echo "<priority>1.0</priority>";
    echo "</url>";
}

/* المنتجات */
$conn = new mysqli("localhost","buyform1_web","gE9MXNgeEUut","buyform1_web");

$result = $conn->query("SELECT id,name_ar,name_en FROM maa_products WHERE status=1");

while($row = $result->fetch_assoc()){
    $id = $row['id'];

    echo "<url>";
    echo "<loc>$baseUrl/ar/products/View/$id</loc>";
    echo "<changefreq>weekly</changefreq>";
    echo "<priority>0.8</priority>";
    echo "</url>";

    echo "<url>";
    echo "<loc>$baseUrl/en/products/View/$id</loc>";
    echo "<changefreq>weekly</changefreq>";
    echo "<priority>0.8</priority>";
    echo "</url>";
}

echo "</urlset>";