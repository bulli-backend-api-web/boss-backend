<?php
//$tempid='1207162213567192951';

$tempid = '1707173468987984321';

$entityid='1701172795466205391';

$otp=urlencode($otp);
$sms_mobile=$sms_mobile;

$admin_mobile_number = '9712566025';



$fullname=urlencode($fullname);
$panel_name=urlencode($panel_name);
//$sms_msg="Dear%20".$fullname."%20Your%20OTP%20for%20login%20panel%20".$panel_name."%20to%20Vastranand%20is%20".$otp.".%20Valid%20for%2010%20minutes.%20Please%20do%20not%20share%20this%20OTP.%20Regards%2C%20VASTRANAND";

$sms_msg = "Dear%20". $fullname."%20Your%20OTP%20for%20login%20panel%20.".$otp."%20to%20Vastranand%20Valid%20for%2010%20minutes.%20Please%20do%20not%20share%20this%20OTP.%20Regards,%20VASTRANAND";


$handle=fopen("https://mobicomm.dove-sms.com//submitsms.jsp?user=vastra&key=d956d232beXX&mobile=$sms_mobile&message=$sms_msg&senderid=VSTRAD&accusage=1&entityid=1701172795466205391&tempid=1707173468987984321","r");

$handle=fopen("https://mobicomm.dove-sms.com//submitsms.jsp?user=vastra&key=d956d232beXX&mobile=$admin_mobile_number&message=$sms_msg&senderid=VSTRAD&accusage=1&entityid=1701172795466205391&tempid=1707173468987984321","r");  
?>