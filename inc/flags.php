<?php if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {die('Access denied ...');} ?>
<?php
echo '<span class="label label-primary">'.$actual_language.'</span> ';
$page = isset($_GET['a']) ? $_GET['a'] : 1;
foreach ($languages as $k => $v)
{
    if ($k != $language_code)
    {
        echo '<a href="?a='.$page.'&amp;lang='.$k.'">';
        echo '<img src="img/flags/flag-'.$k.'.png" alt="'.$v.'" title="'.$v.'" /></a> ';
    }
}
?>