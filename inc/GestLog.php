<?php 
// affichage variable post
// foreach($_POST as $key => $val) echo '$_POST["'.$key.'"]='.$val.'<br />';

if (isset($_SESSION['authentification']))
{
    echo '<meta http-equiv="refresh" content="30"; url="#" />';
    
    echo '<p class="pull-right"><span class="label label-danger">Espace Securise Niveau '.$_SESSION['privilege'].'</span></p>';
    echo '<h1>Gestion des Fichiers Log</h1>';
    echo '<div class="clearfix"></div>';
    
    // verification sur la session authentification 
    if (isset($_POST['OSSelect'])) {$_SESSION['opensim_select'] = trim($_POST['OSSelect']);}
	
    echo '<p>Simulateur selectionne ';
    echo '<strong class="label label-info">'.$_SESSION['opensim_select'].' '.INI_Conf_Moteur($_SESSION['opensim_select'], "version").'</strong>';
    echo '</p>';

	//*****************************************************
	// Si NIV 1 - Verification Moteur Autorise ************
	if($_SESSION['osAutorise'] != '')
	{
        $osAutorise = explode(";", $_SESSION['osAutorise']);
        // echo count($osAutorise);
        // echo $_SESSION['osAutorise'];
        for ($i = 0; $i < count($osAutorise); $i++)
		{
            if (INI_Conf_Moteur($_SESSION['opensim_select'], "osAutorise") == $osAutorise[$i])
            {
                $moteursOK = "OK";
            }
        }
    }
	else {$moteursOK = "NOK";}
	
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

    /* CONSTRUCTION de la commande pour ENVOI sur la console via  SSH */
	if (isset($_POST['cmd']))
	{
        // *** Affichage mode debug ***
        // echo '# '.$_POST['cmd'].' #<br />';
		
        if (isset($_POST['versionLog']))
		{ 
			$cheminWIN = "";
			$cheminWIN = str_replace('/','\\', INI_Conf_Moteur($_SESSION['opensim_select'], "address"));	
			if ($_POST['versionLog'] == "32") {$cmd = 'DEL '.$cheminWIN."OpenSim.log";}
			if ($_POST['versionLog'] == "64") {$cmd = 'DEL '.$cheminWIN."OpenSim.32BitLaunch.log";}
            exec_command($cmd);
            //echo exec_command($cmd);
            // $ecrire = fopen($cheminWIN.'OpenSim.log', "w");
            // ftruncate($ecrire, 0);
            // echo "DEBUG: ".$ecrire;
		}  
	}

	//*************** Formulaire de choix du moteur a selectionne *****************
    // On se connecte a MySQL
	$db = mysql_connect($hostnameBDD, $userBDD, $passBDD);
	mysql_select_db($database, $db);
	$sql = 'SELECT * FROM moteurs';
	$req = mysql_query($sql) or die('Erreur SQL !<p>'.$sql.'</p>'.mysql_error());

    // echo '<h4>Selectionner un Simulateur</h4>';
	echo '<form class="form-group" method="post" action="">';
    echo '<div class="form-inline">';
    echo '<label for="OSSelect"></label>Select Simulator ';
    echo '<select class="form-control" name="OSSelect">';

    while($data = mysql_fetch_assoc($req))
    {
        // if ($data['osAutorise'] != '') {echo $data['osAutorise'];}
        // else {$osAutorise = explode(";", $data['osAutorise']); echo count($osAutorise);}
        $sel = "";
        if ($data['id_os'] == $_SESSION['opensim_select']) {$sel = "selected";}
        echo '<option value="'.$data['id_os'].'" '.$sel.'>'.$data['name'].' '.$data['version'].'</option>';
    }
    
    echo'</select>';
    echo' <button type="submit" class="btn btn-success"><i class="glyphicon glyphicon-ok"></i> Choisir</button>';
    echo '</div>';
    echo'</form>';
    mysql_close();	

    // Test du fichier log 32bit / 64bit
    $versionlog = "";
    $fichierLog32 = INI_Conf_Moteur($_SESSION['opensim_select'], "address").'OpenSim.log';
    
    if (file_exists($fichierLog32))
    {
        $logfile = $fichierLog32;
        $versionlog = "32";
        echo '<div class="alert alert-success alert-anim" role="alert">';
        echo "Fichier existant ".$fichierLog = INI_Conf_Moteur($_SESSION['opensim_select'],"address").'OpenSim.log';
        echo '<strong> OpenSim.log</strong>';
        echo '</div>';
    }

    else if ($_POST['cmd'])
    {
        echo '<div class="alert alert-danger alert-anim" role="alert">';
        echo "Le fichier <strong>OpenSim.log</strong> n'existe pas";
        echo '</div>';
    }

    $fichierLog64 = INI_Conf_Moteur($_SESSION['opensim_select'], "address").'OpenSim.32BitLaunch.log';
	
    if (file_exists($fichierLog64))
    {
        $logfile = $fichierLog32;
        $versionlog = "64";
        echo '<div class="alert alert-success alert-anim" role="alert">';
        echo "Fichier existant " .$fichierLog = INI_Conf_Moteur($_SESSION['opensim_select'], "address").'OpenSim.32BitLaunch.log';
        echo '<strong> OpenSim.32BitLaunch.log</strong>';
        echo '</div>';
    }

    else if ($_POST['cmd'])
    {
        echo '<div class="alert alert-danger alert-anim" role="alert">';
        echo "Le fichier <strong>OpenSim.32BitLaunch.log</strong> n'existe pas"; 
        echo '</div>';
    }
    
    else $logfile = "";
	
    $taille_fichier = filesize($fichierLog);

    if ($taille_fichier >= 1073741824) {$taille_fichier = round($taille_fichier / 1073741824 * 100) / 100 . " Go";}
    else if ($taille_fichier >= 1048576) {$taille_fichier = round($taille_fichier / 1048576 * 100) / 100 . " Mo";}
    else if ($taille_fichier >= 1024) {$taille_fichier = round($taille_fichier / 1024 * 100) / 100 . " Ko";}
    else {$taille_fichier = $taille_fichier . " o";}

    echo '<form class="form-group" method="post" action="">';
    echo '<input type="hidden" value="'.$versionlog.'" name="versionLog">';
    echo '<button type="submit" class="btn btn-danger" name="cmd" '.$btnN3.'><i class="glyphicon glyphicon-trash"></i> Effacer le fichier <strong>Log</strong></button>';
    echo '</form>';

    if ($versionlog) echo '<p class="pull-right"><span class="label label-info">System '.$versionlog.' Bits</span></p>';
    echo '<p>Taille du Fichier Log <span class="badge">'.$taille_fichier.'</span></p>';
    

	$fcontents = file($fichierLog);
	$i = sizeof($fcontents) - 25;
    $aff = "";

	while ($fcontents[$i] != "")
	{
	    $aff .= $fcontents[$i];
		$i++;
	}

	if (!$aff)
    {
        if (!$logfile) $aff = "Le fichier Log est innexistant ...";
        else $aff = "Le fichier Log ".$logfile." est vide ...";
    }
    echo '<pre>'.$aff.'</pre>';

	echo '</td>';
	echo '</tr>';
	echo '</table>';
}
else {header('Location: index.php');}
?>
