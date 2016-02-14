<?php 
if (isset($_SESSION['authentification']) && $_SESSION['privilege'] >= 3)
{
    // Vérification sur la session authentification 
	if (isset($_POST['OSSelect'])){$_SESSION['opensim_select'] = trim($_POST['OSSelect']);}
	echo '<HR>';
	$ligne1 = '<B>Gestion des sauvegardes pour les moteurs Opensim.</B>';
	$ligne2 = '<br>>>> Destination: <b>'.INI_Conf_Moteur($_SESSION['opensim_select'],"address").'</b> <<<';
	echo '<div class="block" id="clean-gray"><button><CENTER>'.$ligne1.'<br>'.$ligne2.'</CENTER></button></div>';
	//echo '<hr>';
	//******************************************************
	$btnN1 = "disabled"; $btnN2 = "disabled"; $btnN3 = "disabled";
	if( $_SESSION['privilege']==4){$btnN1="";$btnN2="";$btnN3="";}		//  Niv 4	
	if( $_SESSION['privilege']==3){$btnN1="";$btnN2="";$btnN3="";}		//  Niv 3
	if( $_SESSION['privilege']==2){$btnN1="";$btnN2="";}				//	Niv 2
	if( $_SESSION['privilege']==1){$btnN1="";}							//	Niv 1
	//******************************************************	
//*******************************************************************	
//*****************************************************************

//******************************************************
// Actions des Boutons
//******************************************************
if (isset($_POST['cmd']))
{
echo $_POST['cmd'].'<br />';

if($_POST['cmd'] == "Telecharger")	// Actions Telecharger fichier
{ 
//	echo $_POST['name_sim'];
//	echo $_POST['name_file'];
	$a = DownloadFile(INI_Conf_Moteur($_SESSION['opensim_select'],"address").$_POST['name_file']);
}
if($_POST['cmd'] == "Supprimer")	// Actions supprimer fichier
{ 
//	echo $_POST['name_sim'];
//	echo $_POST['name_file'];
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
//**************************************************************************

//******************************************************
// Debut Affichage page principale
//******************************************************

	echo'<hr>';
	//*************** Formulaire de choix du moteur a selectionné *****************
		// on se connecte à MySQL
	$db = mysql_connect($hostnameBDD, $userBDD, $passBDD);
	mysql_select_db($database,$db);
	$sql = 'SELECT * FROM moteurs';
	$req = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());
	echo '<CENTER><FORM METHOD=POST ACTION="">
		<select name="OSSelect">';
	while($data = mysql_fetch_assoc($req))
		{$sel="";
		 if($data['id_os'] == $_SESSION['opensim_select']){$sel="selected";}
			echo '<option value="'.$data['id_os'].'" '.$sel.'>'.$data['name'].' - '.$data['version'].'</option>';
		}
	mysql_close();
    // echo' <button type="submit" class="btn btn-success"><i class="glyphicon glyphicon-ok"></i> Choisir</button>';
	echo'</select><INPUT TYPE="submit" VALUE="Choisir" ></FORM></CENTER><hr>';
	//**************************************************************************
	
	
		//**************************************************************************	
	// *** Lecture Fichier Region.ini ***
	$filename2 = INI_Conf_Moteur($_SESSION['opensim_select'],"address")."Regions/".$FichierINIRegions;	 
	if (file_exists($filename2)) 
		{//echo "Le fichier $filename2 existe.<br>";
		$filename = $filename2 ;
		}else {//echo "Le fichier $filename2 n'existe pas.<br>";
		}
	$tableauIni = parse_ini_file($filename, true);
	if($tableauIni == FALSE){echo 'prb lecture ini $filename<br>';}
	
	// *** Lecture Fichier OpenSimDefaults ***
	$filename2 = INI_Conf_Moteur($_SESSION['opensim_select'],"address").$FichierINIOpensim;		
	if (file_exists($filename2)) 
		{//echo "Le fichier $filename2 existe.<br>";
		$filename = $filename2 ;
		}else {//echo "Le fichier $filename2 n'existe pas.<br>";
		}

// **** Recuperation du port http du serveur ******		
	if (!$fp = fopen($filename,"r")) 
	{echo "Echec de l'ouverture du fichier $filename";}		
	$tabfich=file($filename); 
	for( $i = 1 ; $i < count($tabfich) ; $i++ )
	{
	//echo $tabfich[$i]."</br>";
	$porthttp = strstr($tabfich[$i],"http_listener_port");
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
			
/* racine */
$cheminPhysique = INI_Conf_Moteur($_SESSION['opensim_select'],"address");
$Address = $hostnameSSH;		
		
		
/* infos à extraire */
function addScheme($entry,$base,$type) {
  $tab['name'] = $entry;
  $tab['type'] = filetype($base."/".$entry);
  $tab['date'] = filemtime($base."/".$entry);
  $tab['size'] = filesize($base."/".$entry);
  $tab['perms'] = fileperms($base."/".$entry);
  $tab['access'] = fileatime($base."/".$entry);
  $t = explode(".", $entry);
  $tab['ext'] = $t[count($t)-1];
  return $tab;
}


/* liste des dossiers */
function list_dir($base, $cur, $level=0) {
  global $PHP_SELF, $order, $asc;
  if ($dir = opendir($base)) {
    $tab = array();
    while($entry = readdir($dir)) {
      if(is_dir($base."/".$entry) && !in_array($entry, array(".",".."))) {
        $tab[] = addScheme($entry, $base, 'dir');
      }
    }
    /* tri */
    usort($tab,"cmp_name");
    foreach($tab as $elem) {
      $entry = $elem['name'];
      /* chemin relatif à la racine */
      $file = $base."/".$entry;
     /* marge gauche */
      for($i=1; $i<=(4*$level); $i++) {
        echo "&nbsp;";
      }
      /* l'entree est-elle le dossier courant */
      if($file == $cur) {
        echo "<img src='./images/hippo.gif' />&nbsp;$entry<br />\n";
      } else {
        echo "<img src='./images/hippo.gif' />&nbsp;<a href=\"$PHP_SELF?dir=". rawurlencode($file) ."&order=$order&asc=$asc\">$entry</a><br />\n";
      }
      /* l'entree est-elle dans la branche dont le dossier courant est la feuille */
      if(ereg($file."/",$cur."/")) {
        list_dir($file, $cur, $level+1);
      }
    }
    closedir($dir);
  }
}


/* liste des fichiers */
function list_file($cur) {
  global $PHP_SELF, $order, $asc, $order0;
  if ($dir = opendir($cur)) {
    /* tableaux */
    $tab_dir = array();
    $tab_file = array();
    /* extraction */
    while($file = readdir($dir)) {
      if(is_dir($cur."/".$file)) {
        if(!in_array($file, array(".",".."))) {
          $tab_dir[] = addScheme($file, $cur, 'dir');
        }
      } else {
          $tab_file[] = addScheme($file, $cur, 'file');
      }
    }
    /* tri */
   // usort($tab_dir,"cmp_".$order);
   // usort($tab_file,"cmp_".$order);
    /* affichage */
//*********************************************************************************************************
    echo "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\">";
    echo "<tr style=\"font-size:8pt;font-family:arial;\">
    <th>".(($order=='name')?(($asc=='a')?'/\\ ':'\\/ '):'')."Nom</th><td>&nbsp;</td>
    <th>".(($order=='size')?(($asc=='a')?'/\\ ':'\\/ '):'')."Taille</th><td>&nbsp;</td>
	<th>".(($order=='date')?(($asc=='a')?'/\\ ':'\\/ '):'')."Derniere modification</th><td>&nbsp;</td>
	</tr>";
//*********************************************************************************************************
    foreach($tab_file as $elem) 
	{
	if($_SESSION['privilege']==1){$cheminWeb ="#";}else{$cheminWeb = "pages/force-download.php?file=".INI_Conf_Moteur($_SESSION['opensim_select'],"address").$elem['name'];}

		if(assocExt($elem['ext']) <> 'inconnu')
		{
		  echo "<tr><td>";
		  echo '<FORM METHOD=POST ACTION=""><INPUT TYPE="submit" VALUE="Telecharger" NAME="cmd" '.$btnN3.'><INPUT TYPE="submit" VALUE="Supprimer" NAME="cmd" '.$btnN3.'><INPUT TYPE="hidden" VALUE="'.$_SESSION['opensim_select'].'" NAME="name_sim"><INPUT TYPE="hidden" VALUE="'.$elem['name'].'" NAME="name_file">&nbsp;&nbsp;&nbsp;'.$elem['name'].'&nbsp;&nbsp;&nbsp;</FORM>';
		  echo "</td><td>&nbsp;</td>
		  <td align=\"right\">".formatSize($elem['size'])."</td><td>&nbsp;</td>
		  <td>".date("d/m/Y H:i:s", $elem['date'])."</td><td>&nbsp;</td></tr>";
		}
    }
    echo "</table>";
    closedir($dir);
//*********************************************************************************************************	
  }
}
/* formatage de la taille */
function formatSize($s) {
  /* unites */
  $u = array('octets','Ko','Mo','Go','To');
  /* compteur de passages dans la boucle */
  $i = 0;
  /* nombre à afficher */
  $m = 0;
  /* division par 1024 */
  while($s >= 1) {
    $m = $s;
    $s /= 1024;
    $i++;
  }
  if(!$i) $i=1;
  $d = explode(".",$m);
  /* s'il y a des decimales */
  if($d[0] != $m) {
    $m = number_format($m, 2, ",", " ");
  }
  return $m." ".$u[$i-1];
}
/* formatage du type */
function assocType($type) {
  /* tableau de conversion */
  $t = array(
    'fifo' => "file",
    'char' => "fichier special en mode caractere",
    'dir' => "dossier",
    'block' => "fichier special en mode bloc",
    'link' => "lien symbolique",
    'file' => "fichier",
    'unknown' => "inconnu"
  );
  return $t[$type];
}
/* description de l'extention */
function assocExt($ext) {
  $e = array(
    '' => "inconnu",
	'gz' => "Sauvegarde OSMW"
  );
  if(in_array($ext, array_keys($e))) {
    return $e[$ext];
  } else {
    return $e[''];
  }
}
function cmp_name($a,$b) {
    global $asc;
    if ($a['name'] == $b['name']) return 0;
    if($asc == 'a') {
        return ($a['name'] < $b['name']) ? -1 : 1;
    } else {
        return ($a['name'] > $b['name']) ? -1 : 1;
    }
}
function cmp_size($a,$b) {
    global $asc;
    if ($a['size'] == $b['size']) return cmp_name($a,$b);
    if($asc == 'a') {
        return ($a['size'] < $b['size']) ? -1 : 1;
    } else {
        return ($a['size'] > $b['size']) ? -1 : 1;
    }
}
function cmp_date($a,$b) {
    global $asc;
    if ($a['date'] == $b['date']) return cmp_name($a,$b);
    if($asc == 'a') {
        return ($a['date'] < $b['date']) ? -1 : 1;
    } else {
        return ($a['date'] > $b['date']) ? -1 : 1;
    }
}
function cmp_access($a,$b) {
    global $asc;
    if ($a['access'] == $b['access']) return cmp_name($a,$b);
    if($asc == 'a') {
        return ($a['access'] < $b['access']) ? -1 : 1;
    } else {
        return ($a['access'] > $b['access']) ? -1 : 1;
    }
}
function cmp_perms($a,$b) {
    global $asc;
    if ($a['perms'] == $b['perms']) return cmp_name($a,$b);
    if($asc == 'a') {
        return ($a['perms'] < $b['perms']) ? -1 : 1;
    } else {
        return ($a['perms'] > $b['perms']) ? -1 : 1;
    }
}
function cmp_type($a,$b) {
    global $asc;
    if ($a['type'] == $b['type']) return cmp_name($a,$b);
    if($asc == 'a') {
        return ($a['type'] < $b['type']) ? -1 : 1;
    } else {
        return ($a['type'] > $b['type']) ? -1 : 1;
    }
}
function cmp_ext($a,$b) {
    global $asc;
    if ($a['ext'] == $b['ext']) return cmp_name($a,$b);
    if($asc == 'a') {
        return ($a['ext'] < $b['ext']) ? -1 : 1;
    } else {
        return ($a['ext'] > $b['ext']) ? -1 : 1;
    }
}



echo '<table class="table table-striped"><tr>';
//<!-- liste des fichiers -->
/* repertoire initial à lister */
$dir = "";
if(!$dir) {$dir = INI_Conf_Moteur($_SESSION['opensim_select'],"address");} 
list_file(rawurldecode($dir)); 
echo '</td></tr></table><HR>';



echo '<table><tr>';
//**************************************************************************	
$i=0;
	while (list($key, $val) = each($tableauIni))
	{
			if($i%3 <= 3){
			echo '<td>';
		$ImgMap = "http://".$hostnameSSH.":".trim($srvOS)."/index.php?method=regionImage".str_replace("-","",$tableauIni[$key]['RegionUUID']);
		echo '<center><b><u>*** '.$key.' ***</u></b>  <img src="'.$ImgMap.'" width=45 height=45 BORDER=1></center>';
		echo '<FORM METHOD=POST ACTION="">';
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
//	 <td><input type='checkbox' name='matrice[]' value='".$cheminPhysique.$elem['name']."'></td><td>&nbsp;</td></tr>";
		//******************************************************

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
		//******************************************************		

		//******************************************************
		echo '<input type="submit" value="Sauvegarde fichiers opensim" name="cmd" '.$btnN2.'>
		<input type="hidden" name="format_backup" value="OAR" >
		<input typE="hidden" value="'.$key.'" name="name_sim">';	
		//******************************************************	
		echo '</form></td>';

		if($i%3 == 2){echo '</tr><tr>';}
		$i++;
		}
	}
	//**************************************************************************	
echo '</tr></table>';
//***********************************************************************************************		
		
}else{header('Location: index.php');   }
?>