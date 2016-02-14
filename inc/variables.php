<?php
include_once './inc/config.php';

//******************************************************
// *******   CONSTANTES 
//******************************************************
$Couleur_Feux_V = "images/Feux_Vert.jpg";
$Couleur_Feux_O = "images/Feux_Orange.jpg";
$Couleur_Feux_R = "images/Feux_Rouge.jpg";

//******************************************************
// PARAMETRAGE DES COMMANDES DISPONIBLE POUR OSMANAGERWEB 
//******************************************************
// commande de base 
$pre_cmd                = "cd ".INI_Conf_Moteur($_SESSION['opensim_select'], "address").";./ScreenSend ".$_SESSION['opensim_select']." ";
$cmd_OS_force_update    = $pre_cmd."force update"; 
$cmd_OS_stop            = $pre_cmd."shutdown"; 
$cmd_OS_restart         = $pre_cmd."restart"; 
$cmd_OS_save_iar        = $pre_cmd."save iar ";
$cmd_OS_region_root     = $pre_cmd."change region root"; 
$cmd_SYS_start          = "cd ".INI_Conf_Moteur($_SESSION['opensim_select'],"address").";./RunOpensim.sh"; 
$cmd_SYS_etat_OS        = "ps -e |grep mono";
$cmd_SYS_etat_OS2       = "screen -list";
$cmd_SYS_Version_mono   = "mono -V";
$cmd_SYS_Delete_log32   = "cd ".INI_Conf_Moteur($_SESSION['opensim_select'],"address").";chmod 777 OpenSim.log;rm OpenSim.log";
$cmd_SYS_Delete_log64   = "cd ".INI_Conf_Moteur($_SESSION['opensim_select'],"address").";chmod 777 OpenSim.32BitLaunch.log;rm OpenSim.32BitLaunch.log";
$cmd_SYS_Delete_file    = "cd ".INI_Conf_Moteur($_SESSION['opensim_select'],"address").";chmod 777 ";
$cmd_SYS_Delete_Xlog    = "cd ".INI_Conf_Moteur($_SESSION['opensim_select'],"address").";chmod 777 XEngine.log;rm XEngine.log";
//******************************************************

$MENU_LATTERALE = '<center><div id="menu"><ul>
		<li><a href="./" title="Page d\'accueil"><span>Accueil</span></a></li>
		<li><a href="?a=1" title="Gestion des sims (Messages, Start, Stop)"><span>Sims</span></a></li>
		<li><a href="?a=2" title="Gestion des sauvegardes (OAR, XML2)"><span>Backup</span></a></li>
		<li><a href="?a=3" title="Gestion du Terrain (RAW, JPG)"><span>Terrain</span></a></li>
		<li><a href="?a=7" title="Gestion et Visualisation du Log"><span>Log</span></a></li>
		<li><a href="?a=10" title="Gestion des fichiers de sauvegardes (OAR, IAR, RAW, ...)"><span>Fichiers</span></a></li>
		<li><a href="?a=9" title="Vous avez un probleme, envoyer un mail au gestionnaire du serveur"><span>Contact</span></a></li>
		<li><a href="?a=11" title="Affichage des sims presentes sur le moteur"><span>Carte</span></a></li>
		<li><a href="?a=14" title="Qui a participe au projet OSMW"><span>A Propos</span></a></li>
		<li><a href="?a=13" title="Une question, ici peut etre la reponse !"><span>Aide</span></a></li>
	</ul></div></center>';

$PIED_DE_PAGE = '<hr><center>'.INI_Conf($_SESSION['opensim_select'], "VersionOSMW").'</center>';
?>
