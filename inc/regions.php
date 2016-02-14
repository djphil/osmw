<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <link rel="stylesheet" href="css/bootstrap.min.css" type="text/css" />
    <link rel="stylesheet" href="css/bootstrap-theme.min.css" type="text/css" />
    <link rel="stylesheet" href="css/styles.css" type="text/css" />
    <link rel="icon" href="img/favicon.ico">
</head>

<body>
<div class="container">
<?php 
include 'inc/config.php';
include 'inc/fonctions.php';
include 'inc/navbar.php';

//******************************************************
// *******   Noms des fichiers INI 
//******************************************************
$FichierINIRegions = "Regions.ini";         // ou RegionConfig.ini
$FichierINIOpensim = "OpenSimDefaults.ini"; // ou OpenSim.ini
$FichierLOGOpensim = "OpenSim.log";         // ou OpenSim.32BitLaunch.log pour les version 64 bits
$chemin = "C:/opensim/mygrid/bin/";

$db = mysql_connect($hostnameBDD, $userBDD, $passBDD);
mysql_select_db($database,$db);

$sql = 'SELECT * FROM moteurs';
$req = mysql_query($sql) or die('Erreur SQL !<p>'.$sql.'</p>'.mysql_error());
while($data = mysql_fetch_assoc($req))
{
	$opensim_select = $data['id_os']; 
	break;
}
mysql_close();
	
//******************************************************
//  Affichage page principale
//******************************************************
// *** Lecture Fichier Regions.ini ***
	//$filename2 = INI_Conf_Moteur($opensim_select,"address")."Regions/".$FichierINIRegions;	 
	 $filename2 = $chemin."Regions/".$FichierINIRegions;
	if (file_exists($filename2)) 
		{//echo "Le fichier $filename2 existe.<br>";
		$filename = $filename2 ;
		}else {//echo "Le fichier $filename2 n'existe pas.<br>";
		}
	$tableauIni = parse_ini_file($filename, true);
	if($tableauIni == FALSE){echo 'prb lecture ini '.$filename.'<br>';}
	
// *** Lecture Fichier OpenSimDefaults ***
		//$filename2 = INI_Conf_Moteur($opensim_select,"address").$FichierINIOpensim;		 
		$filename2 = $chemin.$FichierINIOpensim;
	if (file_exists($filename2)) 
		{//echo "Le fichier $filename2 existe.<br>";
		$filename = $filename2 ;
		}else {//echo "Le fichier $filename2 n'existe pas.<br>";
		}

	// **** Recuperation du port http du serveur ******		
		if (!$fp = fopen($filename,"r")) 
		{echo "Echec de l'ouverture du fichier ".$filename;}		
		$tabfich=file($filename); 
		for( $i = 1 ; $i < count($tabfich) ; $i++ )
		{
		$porthttp = strstr($tabfich[$i],"http_listener_port");
			if($porthttp)
			{
				$posEgal = strpos($porthttp,'=');
				$longueur = strlen($porthttp);
				$srvOS = substr($porthttp, $posEgal + 1);
			}
		}
		fclose($fp);
	// **********************************************************

	echo '<p>Nombre total de regions <span class="badge">'.count($tableauIni).'</span></p>';

    echo '<table class="table table-hover">';
    echo '<tr>';
    echo '<th>Name</th>';
    echo '<th>Image</th>';
    echo '<th>Teleport</th>';
    echo '<th>Online</th>';
    echo '</tr>';

	while (list($key, $val) = each($tableauIni))
	{
		$ImgMap = "http://".$hostnameSSH.":".trim($srvOS)."/index.php?method=regionImage".str_replace("-","",$tableauIni[$key]['RegionUUID']);
        if (Test_Url($ImgMap) <> '1') {$i = '<h4 class="glyphicon glyphicon-remove text-danger"></h4>';}
        else {$i = '<h4 class="glyphicon glyphicon-ok text-success"></h4>';}
		
		echo '<tr>';
        echo '<td>'.$key.'</td>';
        echo '<td><img style="height:32px;" class="img-thumbnail" alt="" src="'.$ImgMap.'"></td>';
        echo '<td><a class="btn btn-default" href="secondlife://'.$hypergrid.":".$key.'">Teleport</a></td>';
        echo '<td>'.$i.'</td>';
        echo '</tr>';
	}
	echo '</table>';
?>

</div>
