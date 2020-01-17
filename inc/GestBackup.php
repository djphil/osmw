<h1>Gestion des sauvegardes</h1>
<p>Gestion des sauvegardes pour les moteurs Opensim.</p>
<?php 
if (isset($_SESSION['authentification']) && $_SESSION['privilege'] >= 3)
{
	if (isset($_POST['OSSelect'])){$_SESSION['opensim_select'] = trim($_POST['OSSelect']);}
	echo 'Destination: <b>'.INI_Conf_Moteur($_SESSION['opensim_select'],"address").'</b>';

	$btnN1 = "disabled"; $btnN2 = "disabled"; $btnN3 = "disabled";
	if( $_SESSION['privilege']==4){$btnN1="";$btnN2="";$btnN3="";}		//  Niv 4	
	if( $_SESSION['privilege']==3){$btnN1="";$btnN2="";$btnN3="";}		//  Niv 3
	if( $_SESSION['privilege']==2){$btnN1="";$btnN2="";}				//	Niv 2
	if( $_SESSION['privilege']==1){$btnN1="";}							//	Niv 1
    // if ($moteursOK == true) {if( $_SESSION['privilege'] == 1){$btnN1 = ""; $btnN2 = ""; $btnN3 = "";}}

    if (isset($_POST['cmd']))
    {
        echo $_POST['cmd'].'<br />';

        if($_POST['cmd'] == "Telecharger")
        {
            $a = DownloadFile(INI_Conf_Moteur($_SESSION['opensim_select'],"address").$_POST['name_file']);
        }

        if($_POST['cmd'] == "Supprimer")
        {
            $commande = $cmd_SYS_Delete_file.$_POST['name_file'].";rm ".$_POST['name_file'];
        }		
        if($_POST['cmd'] == 'Sauvegarde fichiers opensim')		// Actions sauvegarde conf 
        {
            echo $_POST['name_sim'].'<br>';
            extract($_POST);
            
            $fp = fopen ("files/liste_fichiersOS.txt", "w+");
            fputs("\n");
            echo "trouvé : ".count($matrice).'<br>';
            for ($i = 0; $i < count($_POST["matrice"]); $i++)
            {
                echo $_POST["matrice"][$i].'<br />';
                fputs($fp,INI_Conf_Moteur($_SESSION['opensim_select'],"address").$_POST["matrice"][$i]."\n");
            }
            fclose ($fp);
            echo $commande = 'cd '.$_SERVER['DOCUMENT_ROOT'].$cheminWeb.'files;while read line; do tar -P -c -T - -f '.INI_Conf_Moteur($_SESSION['opensim_select'],"address").$_POST['name_sim'].'_archive_conf_OS.tar.gz; done < liste_fichiersOS.txt;rm liste_fichiersOS.txt';
        }	

    }

	echo'<hr>';

    $sql = 'SELECT * FROM moteurs';
    $req = mysqli_query($db, $sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysqli_error($db));
    echo '<center><form method=post action=""><select name="OSSelect">';
    while($data = mysqli_fetch_assoc($req))
    {
        $sel="";
        if ($data['id_os'] == $_SESSION['opensim_select']){$sel="selected";}
        echo '<option value="'.$data['id_os'].'" '.$sel.'>'.$data['name'].' - '.$data['version'].'</option>';
    }
    mysqli_close($db);
    echo'</select><INPUT TYPE="submit" VALUE="Choisir" ></FORM></CENTER><hr>';

	$filename2 = INI_Conf_Moteur($_SESSION['opensim_select'],"address")."Regions/".$FichierINIRegions;	 
    if (file_exists($filename2)) {$filename = $filename2 ;}
    else {;}
	$tableauIni = parse_ini_file($filename, true);
	if($tableauIni == FALSE){echo 'prb lecture ini $filename<br>';}

	$filename2 = INI_Conf_Moteur($_SESSION['opensim_select'],"address").$FichierINIOpensim;		
	if (file_exists($filename2)) {$filename = $filename2 ;}
    else {;}
	
	if (!$fp = fopen($filename,"r")) {echo "Echec de l'ouverture du fichier $filename";}		

    $tabfich = file($filename);
    $n = count($tabfich);
    $srvOS = 9000;
	
    for( $i = 1 ; $i < $n ; $i++ )
	{
        if (strpos($tabfich[$i], ";") === false)
        {
            $porthttp = strstr($tabfich[$i], "http_listener_port");

            if($porthttp)
            {
                $posEgal = strpos($porthttp,'=');
                $longueur = strlen($porthttp);
                $srvOS = substr($porthttp, $posEgal + 1);
            }
        }

	}
	fclose($fp);
  
    /* racine */
    $cheminPhysique = INI_Conf_Moteur($_SESSION['opensim_select'], "address");
    $Address = $hostnameSSH;		

    echo '<table class="table table-striped"><tr>';

    $dir = "";
    if(!$dir) {$dir = INI_Conf_Moteur($_SESSION['opensim_select'], "address");} 
    list_file(rawurldecode($dir)); 
    echo '</td></tr></table>';

    echo '<table><tr>';
    $i = 0;

    foreach($tableauIni AS $key => $val)
	{
        if ($i % 3 <= 3)
        {
            echo '<td>';
            $ImgMap = "http://".$hostnameSSH.":".trim($srvOS)."/index.php?method=regionImage".str_replace("-","",$tableauIni[$key]['RegionUUID']);
            echo '<center><b><u>*** '.$key.' ***</u></b>  <img src="'.$ImgMap.'" width=45 height=45 BORDER=1></center>';
            echo '<FORM METHOD=POST ACTION="">';

            $filename1 = INI_Conf_Moteur($_SESSION['opensim_select'],"address")."OpenSim.ini";				
            $filename2 = INI_Conf_Moteur($_SESSION['opensim_select'],"address").$FichierINIOpensim;
            $filename3 = INI_Conf_Moteur($_SESSION['opensim_select'],"address")."config-include/FlotsamCache.ini";	
            $filename4 = INI_Conf_Moteur($_SESSION['opensim_select'],"address")."config-include/GridCommon.ini";
            $filename5 = INI_Conf_Moteur($_SESSION['opensim_select'],"address")."OpenSim.log";
            $filename6 = INI_Conf_Moteur($_SESSION['opensim_select'],"address")."OpenSim.32BitLaunch.log";
            $filename7 = INI_Conf_Moteur($_SESSION['opensim_select'],"address")."startuplogo.txt";
            $filename8 = INI_Conf_Moteur($_SESSION['opensim_select'],"address")."startup_commands.txt";
            $filename9 = INI_Conf_Moteur($_SESSION['opensim_select'],"address")."shutdown_commands.txt";

            // <td><input type='checkbox' name='matrice[]' value='".$cheminPhysique.$elem['name']."'></td><td>&nbsp;</td></tr>";

            if (file_exists($filename1))
                {echo '<input type="checkbox" name="matrice[]" value="OpenSim.ini" checked > Le fichier OpenSim.ini existe. <br>';}
                else {echo "<B>Le fichier OpenSim.ini n'existe pas.</b><br>";}
            if (file_exists($filename2))
                {echo '<input type="checkbox" name="matrice[]" value="'.$FichierINIOpensim.'" checked > Le fichier OpenSimDefaults.ini existe. <br>';}
                else {echo "<B>Le fichier OpenSimDefaults.ini n'existe pas.</b><br>";}
            if (file_exists($filename3))
                {echo '<input type="checkbox" name="matrice[]" value="FlotsamCache.ini" checked > Le fichier FlotsamCache.ini existe. <br>';}
                else {echo "<B>Le fichier FlotsamCache.ini n'existe pas.</B><br>";}
            if (file_exists($filename4))
                {echo '<input type="checkbox" name="matrice[]" value="GridCommon.ini" checked > Le fichier GridCommon.ini existe. <br>';}
                else {echo "<B>Le fichier GridCommon.ini n'existe pas.</B><br>";}
            if (file_exists($filename5))
                {echo '<input type="checkbox" name="matrice[]" value="OpenSim.log" > Le fichier OpenSim.log existe. <br>';}
            //	else {echo "<B>Le fichier OpenSim.log n'existe pas.</B><br>";}
            if (file_exists($filename6))
                {echo '<input type="checkbox" name="matrice[]" value="OpenSim.32BitLaunch.log" > Le fichier OpenSim.32BitLaunch.log existe. <br>';}
            //	else {echo "<B>Le fichier OpenSim.32BitLaunch.log n'existe pas.</B><br>";}
            if (file_exists($filename7))
                {echo '<input type="checkbox" name="matrice[]" value="startuplogo.txt" checked > Le fichier startuplogo.txt existe. <br>';}
            //	else {echo "<B>Le fichier startuplogo.txt n'existe pas.</B><br>";}
            if (file_exists($filename8))
                {echo '<input type="checkbox" name="matrice[]" value="startup_commands.txt" checked > Le fichier startup_commands.txt existe. <br>';}
            //	else {echo "<B>Le fichier startup_commands.txt n'existe pas.</B><br>";}
            if (file_exists($filename9))
                {echo '<input type="checkbox" name="matrice[]" value="shutdown_commands.txt" checked > Le fichier shutdown_commands.txt existe. <br>';}
            //	else {echo "<B>Le fichier shutdown_commands.txt n'existe pas.</B><br>";}		

            echo '<input type="submit" value="Sauvegarde fichiers opensim" name="cmd" '.$btnN2.'>
            <input type="hidden" name="format_backup" value="OAR" >
            <input typE="hidden" value="'.$key.'" name="name_sim">';	
            echo '</form></td>';
            if($i%3 == 2){echo '</tr><tr>';}
            $i++;
		}
	}
    echo '</tr></table>';
}
else {header('Location: index.php');}
?>
