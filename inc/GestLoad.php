<?php
include 'inc/variables.php';
include 'inc/functions.php';

if (isset($_SESSION['authentification'])){ // v&eacute;rification sur la session authentification 
if($_POST['OSSelect']){$_SESSION['opensim_select'] = trim($_POST['OSSelect']);}
	echo '<hr>';
	$ligne1 = '<b>Gestion des fichiers sauvegardes du Serveur.</b>';
	$ligne2 = '*** <u>Moteur OpenSim selectionne: </u>'.$_SESSION['opensim_select'].' - '.INI_Conf_Moteur($_SESSION['opensim_select'],"version").' ***';
	echo '<div class="block" id="clean-gray"><button><center>'.$ligne1.'<br>'.$ligne2.'</center></button></div>';
	echo '<hr>';
	//*****************************************************
	// Si NIV 1 - Verification Moteur Autorisé ************

	if($_SESSION['osAutorise'] != '')
	{
        $osAutorise = explode(";", $_SESSION['osAutorise']);

        for($i=0;$i < count($osAutorise);$i++)
        {
            if(INI_Conf_Moteur($_SESSION['opensim_select'],"osAutorise") == $osAutorise[$i]){$moteursOK="OK";}
        } 
	}

    /* ************************************ */
	$btnN1 = "disabled";
    $btnN2 = "disabled";
    $btnN3 = "disabled";
	if ($_SESSION['privilege'] == 4) {$btnN1 = ""; $btnN2 = ""; $btnN3 = "";} // Niv 4
	if ($_SESSION['privilege'] == 3) {$btnN1 = ""; $btnN2 = ""; $btnN3 = "";} // Niv 3
	if ($_SESSION['privilege'] == 2) {$btnN1 = ""; $btnN2 = "";}              // Niv 2
	if ($moteursOK == "OK")
    {
        if ($_SESSION['privilege'] == 1)
        {
            $btnN1 = "";
            $btnN2 = "";
            $btnN3 = "";
        }
    } // Niv 1 + SECURITE MOTEUR
    /* ************************************ */
        
//******************************************************
// Actions des Boutons
//******************************************************

if($_POST['cmd'] == "Charger")	// Actions supprimer fichier
{ 
	echo $_POST['name_sim'];
	echo $_POST['name_file'];
}

//*************** Formulaire de choix du moteur a selectionné *****************
// on se connecte à MySQL
$db = mysql_connect($hostnameBDD, $userBDD, $passBDD);
mysql_select_db($database,$db);
$sql = 'SELECT * FROM moteurs';
$req = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());
echo '<center><form method=post action="">
	<select name="OSSelect">';
while($data = mysql_fetch_assoc($req))
	{$sel="";
	 if($data['id_os'] == $_SESSION['opensim_select']){$sel="selected";}
		echo '<option value="'.$data['id_os'].'" '.$sel.'>'.$data['name'].' - '.$data['version'].'</option>';
	}
mysql_close();	
echo'</select><input type="submit" value="choisir" ></form></center><hr>';


//**************************************************************************
	
//**************************************************************************
//<!-- liste des fichiers -->
echo '<table border="1" cellspacing="0" cellpadding="10" bordercolor="gray"><tr valign="top">';
/* repertoire initial à lister */
if(!$dir) {  $dir = INI_Conf_Moteur($_SESSION['opensim_select'],"address");} 
list_file(rawurldecode($dir)); 
echo '</td></tr></table><hr>';
//**************************************************************************

}else{header('Location: index.php');}