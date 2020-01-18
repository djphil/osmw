<?php 
$dossiers = [
    'default',
    'amelia',
    'cerulean',
    'cosmo',
    'cyborg',
    'darkly',
    'flatly',
    'freelancer',
    'journal',
    'lumen',
    'paper',
    'readable',
    'sandstone',
    'simplex',
    'slate',
    'spacelab',
    'superhero',
    'united',
    'yety'
];

function build_url($folder) {return 'css/' . htmlspecialchars($folder) . '/' .htmlspecialchars($folder). '.css';}

$new_style = (isset($_GET['style'])) ? $_GET['style'] : ''; 
$cookie_style = (isset($_COOKIE['style'])) ? $_COOKIE['style'] : '';  

if (in_array($new_style, $dossiers, true)) 
{
    setcookie('style', $new_style, time() + (365 * 24 * 3600), '/');
    $url = build_url($new_style); 
}

else if (in_array($cookie_style, $dossiers, true)) {$url = build_url($cookie_style);}
else {$url = build_url($dossiers[0]);} 
?>