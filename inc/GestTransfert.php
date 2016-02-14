<?php 
include './inc/variables.php';

if (isset($_SESSION['authentification']) && $_SESSION['privilege']>=3)
{
    // v&eacute;rification sur la session authentification 
	if($_POST['OSSelect']){$_SESSION['opensim_select'] = trim($_POST['OSSelect']);}
    
    echo '<p class="pull-right"><span class="label label-danger">Espace Securise Niveau '.$_SESSION['privilege'].'</span></p>';
    echo '<h1>Transfert des sauvegardes</h1>';
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

    if ($_POST['cmd'])
    {
        // echo $_POST['cmd'].'<br>';
        
        if ($_POST['cmd'] == 'Transferer')
        {
            extract($_POST);

            for ($i = 0; $i < count($_POST["matrice"]); $i++)
            {
                $ftp_server         = $_POST["ftpserver"];
                $login              = $_POST["ftplogin"];
                $password           = $_POST["ftppass"];
                $destination_file   = $_POST["ftppath"].'/'.$_POST["matrice"][$i];
                $source_file        = INI_Conf_Moteur($_SESSION['opensim_select'], "address").$_POST["matrice"][$i];		
                $connect            = ftp_connect($ftp_server);

                if (ftp_login($connect, $login, $password))
                {
                    echo '<p class="alert alert-danger alert-anim">Connecté en tant que <strong>'.$login.'</strong> sur <strong>'.$ftp_server.'</strong> ...</p>';
                    break;
                } 
                
                else
                {
                    echo '<p class="alert alert-danger alert-anim">Connexion impossible en tant que <strong>'.$login.'</strong> ...</p>';
                    break;
                }
                
                $upload = ftp_put($connect, "$destination_file", "$source_file", FTP_ASCII);
                
                if (!$upload)
                {
                    echo '<p class="alert alert-danger alert-anim">Le transfert Ftp a echoue ...</p>';
                    break;
                }
                
                else
                {
                    echo '<p class="alert alert-danger alert-anim">Téléchargement de <strong>'.$_POST['matrice'][$i].'</strong> vers <strong>'.$destination_file.'</strong></p>';
                }
            }
        }	
    }

    /*
    // ****************************
    // Envoi de la commande par ssh
    // ****************************
    if ($_GET['g']) {$commande = "cd ".INI_Conf_Moteur($_SESSION['opensim_select'], "address").";rm ".$_GET['g'];}
    if ($commande <> '')
    {
        if (!function_exists("ssh2_connect")) die(" function ssh2_connect doesn't exist");
        // log in at server1.example.com on port 22
        if(!($con = ssh2_connect($hostnameSSH, 22)))
        {
            echo " fail: unable to establish connection\n";
        }
        else 
        {// try to authenticate with username root, password secretpassword
            if(!ssh2_auth_password($con,$usernameSSH,$passwordSSH)) {
                echo "fail: unable to authenticate\n";
            } else {
            //echo " ok: logged in...\n";
                if (!($stream = ssh2_exec($con, $commande )))
                {
                    echo " fail: unable to execute command\n";
                }
                else
                {
                    // collect returning data from command
                    stream_set_blocking($stream, true);	$data = "";
                    while ($buf = fread($stream,4096)) 
                    {
                        $data .= $buf."\n";
                    }
                    //echo $data;					
                    fclose($stream);
                }
            }
        }
    }
    */

    //******************************************************
    // Debut Affichage page principale
    //******************************************************

	// Formulaire de choix du moteur a selectionne
    // On se connecte a MySQL
    $db = mysql_connect($hostnameBDD, $userBDD, $passBDD);
    mysql_select_db($database,$db);
    
    $sql = 'SELECT * FROM moteurs';
    $req = mysql_query($sql) or die('Erreur SQL !<p>'.$sql.'</p>'.mysql_error());

    echo '<form class="form-group" method="post" action="">';
    echo '<div class="form-inline">';
    echo '<label for="OSSelect"></label>Select Simulator ';
    echo '<select class="form-control" name="OSSelect">';

    while($data = mysql_fetch_assoc($req))
    {
        if ($data['osAutorise'] != '') {echo $data['osAutorise'];}
        else {$osAutorise = explode(";", $data['osAutorise']); echo count($osAutorise);}

        $sel = "";

        if ($data['id_os'] == $_SESSION['opensim_select']) {$sel = "selected";}
        echo '<option value="'.$data['id_os'].'" '.$sel.'>'.$data['name'].' '.$data['version'].'</option>';
    }
    
    echo'</select>';
    echo' <button type="submit" class="btn btn-success"><i class="glyphicon glyphicon-ok"></i> Choisir</button>';
    echo '</div>';
    echo'</form>';
    mysql_close();

    // **********************************	
	// *** Lecture Fichier Region.ini ***
	$filename2 = INI_Conf_Moteur($_SESSION['opensim_select'], "address")."Regions/".$FichierINIRegions;	 
	if (file_exists($filename2))
    {
        // echo "Le fichier $filename2 existe.<br>";
		$filename = $filename2;
    }
    else
    {
        // echo "Le fichier $filename2 n'existe pas.<br>";
    }
    
	$tableauIni = parse_ini_file($filename, true);
	if($tableauIni == FALSE){echo 'prb lecture ini $filename<br>';}
	
	// *** Lecture Fichier OpenSimDefaults ***
	$filename2 = INI_Conf_Moteur($_SESSION['opensim_select'],"address").$FichierINIOpensim;		
	if (file_exists($filename2))
    {
        // echo "Le fichier $filename2 existe.<br>";
		$filename = $filename2 ;
    }
    else
    {
        // echo "Le fichier $filename2 n'existe pas.<br>";
    }

    // **** Recuperation du port http du serveur ******		
	if (!$fp = fopen($filename,"r")) {echo "Echec de l'ouverture du fichier $filename";}		
	
    $tabfich = file($filename); 
	
    for( $i = 1 ; $i < count($tabfich) ; $i++ )
	{
        $porthttp = strstr($tabfich[$i], "http_listener_port");
		if($porthttp)
		{
			$posEgal = strpos($porthttp,'=');
			$longueur = strlen($porthttp);
			$srvOS = substr($porthttp, $posEgal + 1);
		}
	}
	fclose($fp);

    //******************************************************
    //  Contenu Affichage page principale
    //******************************************************	
    /* Racine */
    $cheminPhysique = INI_Conf_Moteur($_SESSION['opensim_select'],"address");
    $Address = $hostnameSSH;		

    echo '<form class="form-group" method="post" action="">';
    echo '<table>';
    echo '<tr>';
    
    // Liste des fichiers
    /* repertoire initial à lister */
    if (!$dir) {$dir = INI_Conf_Moteur($_SESSION['opensim_select'], "address");} 
    list_file(rawurldecode($dir)); 
    echo '</tr>';
    echo '</table>';

    echo '<h2>Archivage des fichiers</h2>';
    echo '<h3>Transfert vers un serveur ftp externe, archivage des fichiers</h3>';

    if ($_POST['cmd'] == 'Transferer')
    {
        echo '<p>Archivage du Simulateur <strong class="label label-info">'.$_POST['name_sim'].'</strong></p>';
        echo '<p>Nombre de fichier archive(s) <span class="badge">'.count($matrice).'</span></p>';
    }

    echo '<p>Destinations des Sauvegardes ';
    echo '<strong class="label label-info">'.INI_Conf_Moteur($_SESSION['opensim_select'], "address").'</strong>';
    echo '</p>';

    echo '<h3>Liste des fichiers transferables disponnibles</h3>';
    echo gen_matrice(rawurldecode($dir));

    echo '<div class="form-group">';
    echo '<label for="ftpserver"></label>Serveur FTP';
    echo '<input class="form-control" type="text" name="ftpserver" value="">';
    echo '</div>';

    echo '<div class="form-group">';
    echo '<label for="ftplogin"></label>Login';
    echo '<input class="form-control" type="text" name="ftplogin" value="">';
    echo '</div>';

    echo '<div class="form-group">';
    echo '<label for="ftppass"></label>Password';
    echo '<input class="form-control" type="password" name="ftppass" value="">';
    echo '</div>';

    echo '<div class="form-group">';
    echo '<label for="ftppath"></label>Chemin';
    echo '<input class="form-control" type="text" name="ftppath" value="">';
    echo '</div>';

    echo '<input type="hidden" value="'.$_SESSION['opensim_select'].'" name="name_sim">';
    echo '<button class="btn btn-success" type="submit" value="Transferer" name="cmd">';
    echo '<i class="glyphicon glyphicon-transfer"></i> Transferer';
    echo '</button>';
    echo '</form>';

    mysql_close();			
}
else {header('Location: index.php');}
?>
