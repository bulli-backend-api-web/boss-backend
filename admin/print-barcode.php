<?php

include("config/database.php");
require 'vendor/autoload.php';

error_reporting(E_ALL);
ini_set('display_errors',1);

use Picqer\Barcode\BarcodeGeneratorPNG;

$barcode_no = my_simple_crypt($_GET['id'], 'decrypt_1');

$generator = new BarcodeGeneratorPNG();

$barcode = base64_encode(
    $generator->getBarcode(
        $barcode_no,
        $generator::TYPE_CODE_128,
        1,
        45
    )
);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <title>Print Barcode</title>

    <style>
@page{
    size: 60mm 35mm;
    margin: 0;
}

html, body{
    width: 60mm;
    height: 35mm;
    margin: 0;
    padding: 0;
    background: #fff;
    font-family: Arial, sans-serif;
}

.barcode-wrapper{
    width: 60mm;
    height: 35mm;
    display: flex;
    align-items: center;
    justify-content: center;
}

.barcode-card{
    width: 56mm;
    height: 31mm;
    text-align: center;
    background: #fff;
    padding: 2mm;
    box-sizing: border-box;
}

.barcode-title{
    font-size: 10px;
    font-weight: bold;
    margin-bottom: 2mm;
}

.barcode-img{
    width: 50mm;
    height: 14mm;
    object-fit: contain;
}

.barcode-number{
    margin-top: 1.5mm;
    font-size: 10px;
    font-weight: 600;
    letter-spacing: 0.5px;
}

@media print{
    html, body{
        width: 60mm;
        height: 35mm;
        margin: 0 !important;
        padding: 0 !important;
    }

    .barcode-wrapper{
        page-break-after: avoid;
    }
}
</style>

</head>

<body>

<div class="barcode-wrapper">
    <div class="barcode-card">
        <img src="data:image/png;base64,<?= $barcode; ?>" class="barcode-img">
        <div class="barcode-number">
            <?= $barcode_no; ?>
        </div>
    </div>
</div>
<script>

window.onload = function(){
    window.print();
    window.onafterprint = function(){
        window.close();
    };
};

</script>

</body>
</html>