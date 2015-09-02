<?php
header('content-type: application/x-javascript'); 

$until = $_SESSION['remaining'];

?>
$('#countdown').countdown('destroy');
$('#countdown').countdown({
onTick: expire_warning,
until: +<?= $until; ?>, 
compact: true, 
format:"MS",
alwaysExpire:true,
expiryUrl:"index.php?action=logout"});