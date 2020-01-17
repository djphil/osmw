<?php 
function construire_url($dossier) 
{
	return 'css/' . htmlspecialchars($dossier) . '/' .htmlspecialchars($dossier). '.css'; 
}

$dossiers = array (
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
);

$new_style = (isset($_GET['style'])) ? $_GET['style'] : ''; 
$cookie_style = (isset($_COOKIE['style'])) ? $_COOKIE['style'] : '';  

if (in_array($new_style, $dossiers, true)) 
{
	setcookie('style', $new_style, time() + (365 * 24 * 3600), '/');
	$url = construire_url($new_style); 
}

else if (in_array($cookie_style, $dossiers, true))
{
	$url = construire_url($cookie_style);
}
else {$url = construire_url($dossiers[0]);} 
?> 
