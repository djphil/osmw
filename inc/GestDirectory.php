<?php
if (isset($_SESSION['authentification']))
{
    echo '<p class="pull-right"><span class="label label-danger">Espace Securise Niveau '.$_SESSION['privilege'].'</span></p>';
    echo '<h1>Fichiers Sauvegardes</h1>';
    echo '<div class="clearfix"></div>';
    
    // Verification sur la session authentification 
    if (isset($_POST['OSSelect'])) {$_SESSION['opensim_select'] = trim($_POST['OSSelect']);}

    echo '<p>Simulateur selectionne ';
    echo '<strong class="label label-info">'.$_SESSION['opensim_select'].' '.INI_Conf_Moteur($_SESSION['opensim_select'], "version").'</strong>';
    echo '</p>';

	//*****************************************************
	// Si NIV 1 - Verification Moteur Autorise ************
	if ($_SESSION['osAutorise'] != '')
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

	/* Actions des Boutons */
	if (isset($_POST['cmd']))
	{
        // Actions Telecharger fichier
		if ($_POST['cmd'] == "download")
		{ 
            echo INI_Conf_Moteur($_SESSION['opensim_select'], "address").$_POST['name_file']."<br />";
			$a = DownloadFile(INI_Conf_Moteur($_SESSION['opensim_select'], "address").$_POST['name_file']);
        }
		
        // Actions supprimer fichier
		if ($_POST['cmd'] == "delete")
		{
			$cheminWIN = str_replace('/','\\',INI_Conf_Moteur($_SESSION['opensim_select'],"address"));;		
			$cmd= 'DEL '.$cheminWIN.$_POST['name_file'];	 
			exec_command($cmd);
            
            echo '<div class="alert alert-success alert-anim" role="alert">';
            echo 'Fichier '.$cheminWIN.$_POST['name_file'].' supprime avec succes ...';
            echo '<strong> OpenSim.log</strong>';
            echo '</div>';
		}
	}

    //*************** Formulaire de choix du moteur a selectionne *****************
    // on se connecte a MySQL
    $db = mysql_connect($hostnameBDD, $userBDD, $passBDD);
    mysql_select_db($database,$db);
    $sql = 'SELECT * FROM moteurs';
    $req = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());
    // echo '<h4>Selectionner un Simulateur</h4>';
	echo '<form class="form-group" method="post" action="">';
    echo '<div class="form-inline">';
    echo '<label for="OSSelect"></label>Select Simulator ';
    echo '<select class="form-control" name="OSSelect">';

    while($data = mysql_fetch_assoc($req))
    {
        $sel = "";
        if ($data['id_os'] == $_SESSION['opensim_select']) {$sel = "selected";}
        echo '<option value="'.$data['id_os'].'" '.$sel.'>'.$data['name'].' '.$data['version'].'</option>';
    }

    echo'</select>';
    echo' <button type="submit" class="btn btn-success"><i class="glyphicon glyphicon-ok"></i> Choisir</button>';
    echo '</div>';
    echo'</form>';
    mysql_close();
    ?>
    
    <?php if(isset($_SESSION['flash'])): ?>
        <?php foreach($_SESSION['flash'] as $type => $message): ?>
            <div class="alert alert-<?php echo $type; ?> alert-anim">
                <?php echo $message; ?>
            </div>
        <?php endforeach; ?>
        <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>
    
    <?php
    // <!-- liste des fichiers -->
    /* Repertoire initial a lister */
    $dir = "";
    $dir = INI_Conf_Moteur($_SESSION['opensim_select'], "address");

    if ($dir) {list_file(rawurldecode($dir));}

    else
    {
        echo '<div class="alert alert-danger alert-anim" role="alert">';
        echo 'Le <strong>chemin</strong> est incorrecte ...';
        echo '</div>';
    }
}
else {header('Location: index.php');}
?>