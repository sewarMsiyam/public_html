<?php
session_start(); // ضروري لتفعيل الجلسة

// إذا تم إرسال العملة من النموذج أو AJAX
if (isset($_POST['currency'])) {
    $_SESSION['currency'] = $_POST['currency'];
    // في حال كان الطلب AJAX فقط نرسل تأكيد
    echo $_SESSION['currency'];
    exit;
}

// عملة افتراضية
$selectedCurrency = isset($_SESSION['currency']) ? $_SESSION['currency'] : 'USD $';
?>
