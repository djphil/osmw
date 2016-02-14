<?php
// Verification sur la session authentification 
if (isset($_SESSION['authentification']))
{   
    echo '<p class="pull-right"><span class="label label-danger">Espace Securise Niveau '.$_SESSION['privilege'].'</span></p>';
    echo '<h1>Gestion Inventaire</h1>';
    echo '<div class="clearfix"></div>';

    echo '<p>Simulateur selectionne ';
    echo '<strong class="label label-info">'.$_SESSION['opensim_select'].' '.INI_Conf_Moteur($_SESSION['opensim_select'], "version").'</strong>';
    echo '</p>';
    /* ************************************ */
    // Si NIV 1 - Verification Moteur Autorise ************
    if ($_SESSION['osAutorise'] != '')
    {
        $osAutorise = explode(";", $_SESSION['osAutorise']);
        // echo count($osAutorise);
        // echo $_SESSION['osAutorise'];
		
        for ($i = 0; $i < count($osAutorise); $i++)
        {
            if (INI_Conf_Moteur($_SESSION['opensim_select'], "osAutorise") == $osAutorise[$i]){$moteursOK = "OK";}
        }
    }

    /* ************************************ */
	$btnN1 = "disabled"; 
    $btnN2 = "disabled"; 
    $btnN3 = "disabled";
	if ($_SESSION['privilege'] == 4) {$btnN1 = ""; $btnN2 = ""; $btnN3 = "";}   // Niv 4	
	if ($_SESSION['privilege'] == 3) {$btnN1 = ""; $btnN2 = ""; $btnN3 = "";}   // Niv 3
	if ($_SESSION['privilege'] == 2) {$btnN1 = ""; $btnN2 = "";}                // Niv 2

    /*
    if($moteursOK == "OK")
    {
        if ($_SESSION['privilege'] == 1)
        {
            $btnN1 = ""; 
            $btnN2 = ""; 
            $btnN3 = "";
        }
    }   // Niv 1 + SECURITE MOTEUR
    */
    /* ************************************ */

    //******************************************************
    // CONSTRUCTION de la commande pour ENVOI sur la console via  SSH
    //******************************************************
    if (isset($_POST['cmd']))
	{
        // *** Lecture Fichier OpenSimDefaults ***
		$filename2 = INI_Conf_Moteur($_SESSION['opensim_select'], "address").$FichierINIOpensim;
        if (file_exists($filename2)) {$filename = $filename2 ;}
        else {;}

        // **** Recuperation du port http du serveur ******		
        if (!$fp = fopen($filename,"r")) 
		{
            echo "Echec d'ouverture du fichier ".$filename;
        }	

        $tabfich=file($filename); 

        for( $i = 1 ; $i < count($tabfich) ; $i++ )
		{
            $porthttp = strstr($tabfich[$i]," port = ");
            $access_password = strstr($tabfich[$i]," access_password = ");

            if ($porthttp)
			{
				$posEgal = strpos($porthttp, '=');
				$longueur = strlen($porthttp);
				$RemotePort = substr($porthttp, $posEgal + 1);
			}

            if ($access_password)
			{
				$posEgal = strpos($access_password,'=');
				$longueur = strlen($access_password);
				$access_password2 = trim(substr($access_password, $posEgal + 1));
				// $longueur2 = strlen($access_password2);
				// $Remote_access_password = substr($access_password2, 1,$longueur2-2 );			
			}
		}
		fclose($fp);
		// $myRemoteAdmin = new RemoteAdmin(trim($hostnameSSH), trim($RemotePort), trim($Remote_access_password));
        $myRemoteAdmin = new RemoteAdmin(trim($hostnameSSH), trim($RemotePort), trim($access_password2));

		if ($_POST['cmd'] == 'Recuperer')
		{
            if (!empty($_POST['first']) && !empty($_POST['last']) && !empty($_POST['pass']))
            {
                $fullname = $_POST['first']." ".$_POST['last'];
                $parameters = array('command' => 'save iar '.$fullname.' / '.$_POST['pass'].' BackupIAR_'.$_POST['first'].'_'.$_POST['last'].'_'.date(d_m_Y_h).'.iar');
                $myRemoteAdmin->SendCommand('admin_console_command', $parameters);

                echo "<div class='alert alert-success alert-anim'>";
                echo "<i class='glyphicon glyphicon-ok'></i>";
                echo " Demande effectuee avec succes <strong>".$fullname."</strong>, veuillez consulter l'extrait du fichier <strong>Log</strong> ci-dessous ...</div>";
                // echo "<a class='btn btn-default btn-primary' href='index.php?a=7'><i class='glyphicon glyphicon-eye-open'></i> Consulter le Ficher Log complet</a>";
            }
            
            else
            {
                echo "<div class='alert alert-danger alert-anim'>";
                echo "<i class='glyphicon glyphicon-remove'></i>";
                echo " <strong>Login</strong> ou <strong>Mot de passe</strong> invalide ...</div>";
            }
		}  
	}

	// Formulaire de choix du moteur a selectionne
    // On se connecte a MySQL
    $db = mysql_connect($hostnameBDD, $userBDD, $passBDD);
    mysql_select_db($database,$db);
    
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

    /* ************************************ */
	echo '<h4>Vos identifiants</h4>';
	echo '<form method="post" action="">';
    echo '<table class="table table-hover">';
    echo '<tr>';
    echo '<th>Firstname</th>';
    echo '<th>Lastname</th>';
    echo '<th>Password</th>';
    echo '<th>Action</th>';
    echo '</tr>';

	echo '<tr>';
	echo'<td><input class="form-control" type="text" name="first"></td>';
	echo'<td><input class="form-control" type="text" name="last"></td>';
	echo '<td><input class="form-control" type="password" name="pass"></td>';
	echo '<td>
			  <input type="hidden" value="" name="name_sim">
			  <button class="btn btn-success" type="submit" value="Recuperer" name="cmd" >
              <i class="glyphicon glyphicon-save"></i>  Save IAR</button>
		  </td>';
	echo '</tr>';
	echo '</table>';
	echo '</form>';
    /* ************************************ */

    /* Lecture Log Si Formulaire OK */
    if (!empty($_POST['first']) && !empty($_POST['last']) && !empty($_POST['pass']))
    {
        $aff = "";
        $logfile = "";
        $versionlog = "";

        $fichierLog32 = INI_Conf_Moteur($_SESSION['opensim_select'], "address").'OpenSim.log';
        if (file_exists($fichierLog32))
        {
            $logfile = $fichierLog32;
            $versionlog = "32";
        }

        $fichierLog64 = INI_Conf_Moteur($_SESSION['opensim_select'], "address").'OpenSim.32BitLaunch.log';
        if (file_exists($fichierLog64))
        {
            $logfile = $fichierLog32;
            $versionlog = "64";
        }

        $fcontents = file($logfile);
        $i = sizeof($fcontents) - 10;

        while ($fcontents[$i] != "")
        {
            // if (strstr($fcontents[$i], '[INVENTORY ARCHIVER]')
            // or strstr($fcontents[$i], '[ARCHIVER]') 
            // or strstr($fcontents[$i], '[RADMIN]'))
                $aff .= "<p>".$fcontents[$i]."</p>";
            $i++;
        }

        if (!empty($aff))
        {
            echo '<h4>Fichier Log '.$logfile.'</h4>';
            echo '<pre>'.$aff.'</pre>';
        }
    }
}
else {header('Location: index.php');}
?>
