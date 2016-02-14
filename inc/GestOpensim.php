<?php 
// Verification sur la session authentification 
if (isset($_SESSION['authentification']) && $_SESSION['privilege']>= 3)
{
    echo '<p class="pull-right"><span class="label label-danger">Espace Securise Niveau '.$_SESSION['privilege'].'</span></p>';
    echo '<h1>Gestion des fichiers</h1>';
    echo '<div class="clearfix"></div>';

    echo '<p>Simulateur selectionne ';
    echo '<strong class="label label-info">'.$_SESSION['opensim_select'].' '.INI_Conf_Moteur($_SESSION['opensim_select'], "version").'</strong>';
    echo '</p>';
	// ******************************************************
	$btnN1 = "disabled";
	$btnN2 = "disabled";
	$btnN3 = "disabled";
	if ($_SESSION['privilege'] == 4) {$btnN1 = ""; $btnN2 = ""; $btnN3 = "";} // Niv 4	
	if ($_SESSION['privilege'] == 3) {$btnN1 = ""; $btnN2 = ""; $btnN3 = "";} // Niv 3
	if ($_SESSION['privilege'] == 2) {$btnN1 = ""; $btnN2 = "";}			  // Niv 2
	if ($_SESSION['privilege'] == 1) {$btnN1 = "";}					          // Niv 1
	// ******************************************************	

    //******************************************************
    //  Affichage page principale
    //******************************************************
    // *** Test des Fichiers suivants ***	
	$filename1 = INI_Conf_Moteur($_SESSION['opensim_select'],"address")."OpenSim.ini";				
	$filename2 = INI_Conf_Moteur($_SESSION['opensim_select'],"address").$FichierINIOpensim;
	$filename3 = INI_Conf_Moteur($_SESSION['opensim_select'],"address")."config-include/FlotsamCache.ini";	
	$filename4 = INI_Conf_Moteur($_SESSION['opensim_select'],"address")."config-include/GridCommon.ini";
	$filename5 = INI_Conf_Moteur($_SESSION['opensim_select'],"address")."OpenSim.log";
	$filename6 = INI_Conf_Moteur($_SESSION['opensim_select'],"address")."OpenSim.32BitLaunch.log";
	$filename7 = INI_Conf_Moteur($_SESSION['opensim_select'],"address")."startuplogo.txt";
	$filename8 = INI_Conf_Moteur($_SESSION['opensim_select'],"address")."startup_commands.txt";
	$filename9 = INI_Conf_Moteur($_SESSION['opensim_select'],"address")."shutdown_commands.txt";
    //******************************************************

    $dispo = "";

    if (file_exists($filename1))
    {
        $dispo = $dispo.'<p><input class="btn btn-default btn-block" type="submit" name="affichage" value="OpenSim.ini"></p>';
    }

    else if (!$_POST['affichage']) 
    {
        echo '<div class="alert alert-danger alert-anim" role="alert">Fichier <strong>OpenSim.ini</strong> innexistant ...</div>';
    }

    if (file_exists($filename2))
    {
        $dispo = $dispo.'<p><input class="btn btn-default btn-block" type="submit" name="affichage" value="'.$FichierINIOpensim.'"></p>';
	}

	else if (!$_POST['affichage'])
	{
        // echo "Fichier OpenSimDefaults.ini innexistant ...";
        echo '<div class="alert alert-danger alert-anim" role="alert">Fichier <strong>".$FichierINIOpensim."</strong> innexistant ...</div>';
	}

	if (file_exists($filename3))
	{
        $dispo = $dispo.'<p><input class="btn btn-default btn-block" type="submit" name="affichage" value="FlotsamCache.ini"></p>';
	}

	else if (!$_POST['affichage'])
	{
        echo '<div class="alert alert-danger alert-anim" role="alert">Fichier <strong>FlotsamCache.ini</strong> innexistant ...</div>';
	}


	if (file_exists($filename4))
	{
        $dispo = $dispo.'<p><input class="btn btn-default btn-block" type="submit" name="affichage" value="GridCommon.ini"></p>';
	}

	else if (!$_POST['affichage'])
	{
        echo '<div class="alert alert-danger alert-anim" role="alert">Fichier <strong>GridCommon.ini</strong> innexistant ...</div>';
	}

	if (file_exists($filename5))
	{
        $dispo = $dispo.'<p><input class="btn btn-default btn-block" type="submit" name="affichage" value="OpenSim.log"></p>';
	}

	else if (!$_POST['affichage'])
	{
        echo '<div class="alert alert-danger alert-anim" role="alert">Fichier <strong>OpenSim.log</strong> innexistant ...</div>';
	}

	if (file_exists($filename6))
	{
        $dispo = $dispo.'<p><input class="btn btn-default btn-block" type="submit" name="affichage" value="OpenSim.32BitLaunch.log"></p>';
	}

	else if (!$_POST['affichage'])
	{
        echo '<div class="alert alert-danger alert-anim" role="alert">Fichier <strong>OpenSim.32BitLaunch.log</strong> innexistant ...</div>';
	}

	if (file_exists($filename7))
	{
        $dispo = $dispo.'<p><input class="btn btn-default btn-block" type="submit" name="affichage" value="startuplogo.txt"></p>';
	}

	else if (!$_POST['affichage'])
	{
        echo '<div class="alert alert-danger alert-anim" role="alert">Fichier <strong>startuplogo.txt</strong> innexistant ...</div>';
	}

	if (file_exists($filename8))
	{
        $dispo = $dispo.'<p><input class="btn btn-default btn-block" type="submit" name="affichage" value="startup_commands.txt"></p>';
	}

	else if (!$_POST['affichage'])
	{
        echo '<div class="alert alert-danger alert-anim" role="alert">Fichier <strong>startup_commands.txt</strong> innexistant ...</div>';
	}

	if (file_exists($filename9))
	{
        $dispo = $dispo.'<p><input class="btn btn-default btn-block" type="submit" name="affichage" value="shutdown_commands.txt"></p>';
	}

	else if (!$_POST['affichage'])
	{
        echo '<div class="alert alert-danger alert-anim" role="alert">Fichier <strong>shutdown_commands.txt</strong> innexistant ...</div>';
	}

    echo '<h4>Choisir le fichier a modifier</h4>';
    echo '<form class="form-group" method="post" action="">';
    echo $dispo;
    echo '</form>';
    
    if ($_POST['affichage'] == "OpenSim.ini"){$fichier = $filename1;}
	if ($_POST['affichage'] == $FichierINIOpensim){$fichier = $filename2;}
	if ($_POST['affichage'] == "FlotsamCache.ini"){$fichier = $filename3;}
	if ($_POST['affichage'] == "GridCommon.ini"){$fichier = $filename4;}
	if ($_POST['affichage'] == "OpenSim.log"){$fichier = $filename5;}
	if ($_POST['affichage'] == "OpenSim.32BitLaunch.log"){$fichier = $filename6;}
	if ($_POST['affichage'] == "startuplogo.txt"){$fichier = $filename7;}
	if ($_POST['affichage'] == "startup_commands.txt"){$fichier = $filename8;}
	if ($_POST['affichage'] == "shutdown_commands.txt"){$fichier = $filename9;}

    // Enregistre le fichier
    if (isset($_POST['button']))
    {
        unlink($fichier);
        $ouverture = fopen("$fichier", "a+");
        fwrite($ouverture, "$_POST[modif]");
        fclose($ouverture);
        echo '<div class="alert alert-success alert-anim" role="alert">';
        echo 'Modification du fichier <strong>'.$_POST['affichage'].'</strong> effectue avec succes ...</div>';
    }
        
    if (isset($_POST['affichage']))	// Affiche le fichier
    {  	
        echo '<div class="alert alert-warning" role="alert">';
        echo '<p>Modification du fichier <strong>'.$_POST['affichage'].'</strong> sur le Simulateur <strong>'.$_SESSION['opensim_select'].' '.INI_Conf_Moteur($_SESSION['opensim_select'],"version").'</strong>';
        echo '</div>';

        echo '<form class="form-group" method="post" action="">';
        echo '<input type="hidden" name="affichage" value="'.$_POST['affichage'].'">';
        echo '<input type="hidden" name="button" value="Modifier" '.$btnN3.'>';

        echo '<textarea class="form-control preformat" name="modif" rows="10">';
        echo file_get_contents($fichier); 
        echo '</textarea>';
        echo '<p></p>';

        echo '<button class="btn btn-success" type="submit" name="button" '.$btnN3.'>';
        echo '<i class="glyphicon glyphicon-ok"></i> Modifier le Fichier</button>';
        echo '</form>';
    }
}
else {header('Location: index.php');}
?>
