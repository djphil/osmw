<?php 
$new_style = (isset($_GET['style'])) ? $_GET['style'] : ''; 
$cookie_style = (isset($_COOKIE['style'])) ? $_COOKIE['style'] : '';  
if (in_array($new_style, $themes, true)) 
{
    setcookie('style', $new_style, time() + (365 * 24 * 3600), '/');
    $url = build_url($new_style); 
}
else if (in_array($cookie_style, $themes, true)) {$url = build_url($cookie_style);}
else {$url = build_url($themes[0]);} 
?> 