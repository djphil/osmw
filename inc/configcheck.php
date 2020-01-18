<?php if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {die('Access denied ...');} ?>
<?php
$config = 'inc/config.php';
if (!file_exists($config) || filesize($config ) <= 0)
{
    echo "<br /><center><b>Alert!</b> Missing file <b>config.php</b>, please run <b>install.php</b> first...</center>";
    exit();
}
?>