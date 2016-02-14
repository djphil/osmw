<?php 
include 'inc/variables.php';

if (isset($_SESSION['authentification']) && $_SESSION['privilege']>=3){ // v&eacute;rification sur la session authentification 
	if($_POST['OSSelect']){$_SESSION['opensim_select'] = trim($_POST['OSSelect']);}
	echo '<HR>';
	$ligne1 = '<B>Page Administration de tous les Moteurs pour ce serveur.</B>';
	$ligne2 = '<center>********** VEUILLEZ CONSULTER LE LOG REGULIEREMENT ********</center>';
	$ligne3 = '*** <u>Moteur OpenSim selectionne: </u>'.$_SESSION['opensim_select'].' - '.INI_Conf_Moteur($_SESSION['opensim_select'],"version").' ***';
	echo '<div class="block" id="clean-gray"><button><CENTER>'.$ligne1.'<br>'.$ligne2.'<br>'.$ligne3.'</CENTER></button></div>';
	echo '<hr>';
	
	//******************************************************
	$btnN1 = "disabled"; $btnN2 = "disabled"; $btnN3 = "disabled";
	if( $_SESSION['privilege']==4){$btnN1="";$btnN2="";$btnN3="";}		//  Niv 4	
	if( $_SESSION['privilege']==3){$btnN1="";$btnN2="";$btnN3="";}		//  Niv 3
	if( $_SESSION['privilege']==2){$btnN1="";$btnN2="";}				//	Niv 2
	if( $_SESSION['privilege']==1){$btnN1="";}							//	Niv 1
	//******************************************************	

		
// *******************************************************************		
// *******************************************************************			

// *******************************************************************	
// ****************  AFFICHAGE PAGE **********************************
// *******************************************************************			
	echo '<center><div class="block" id="pale-blue"><a href="#"><button>*** TESTS / MARCHE / ARRET ***</button></a></div></center><br>';
	
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
	echo'</select><INPUT TYPE="submit" VALUE="Choisir" ></FORM></CENTER><hr>';
	//**************************************************************************
	//*************** ACTION pour tous les moteurs *****************
		echo '<table width=100%><tr>
	<td align=center><FORM METHOD=POST ACTION=""><INPUT TYPE="submit" VALUE="Tests Systeme" NAME="cmd" '.$btnN3.'><INPUT TYPE="hidden" VALUE="'.$key.'" NAME="name_sim"></FORM></td>
	<td align=center><FORM METHOD=POST ACTION=""><INPUT TYPE="submit" VALUE="TOUS DEMARRER" NAME="cmd" '.$btnN3.'><INPUT TYPE="hidden" VALUE="'.$key.'" NAME="name_sim"></FORM></td>
	<td align=center><FORM METHOD=POST ACTION=""><INPUT TYPE="submit" VALUE="TOUS ARRETER" NAME="cmd" '.$btnN3.'><INPUT TYPE="hidden" VALUE="'.$key.'" NAME="name_sim"></FORM></td>
	</tr>
	<tr><td colspan=3 align=center>Commande en console opensim pour le moteur sélectionné: <br>	
	<FORM METHOD=POST ACTION="">
	   <INPUT TYPE="text" VALUE="" NAME="cmd_script" '.$btnN3.'>
		<INPUT TYPE="submit" VALUE="Executer" NAME="cmd" '.$btnN3.'>
		<INPUT TYPE="hidden" VALUE="'.$key.'" NAME="name_sim">
	</FORM></td></tr>
	</table><hr>';

	//*************** Liste de tous les moteurs *****************
	// *** Lecture BDD config  ***
		// on se connecte à MySQL
/*
		$db = mysql_connect($hostnameBDD, $userBDD, $passBDD);
	mysql_select_db($database,$db);
	$sql = 'SELECT * FROM moteurs';
	$req = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());	
	while($data = mysql_fetch_assoc($req))
	{
	echo '<table width="100%" BORDER=0><tr>
	<td align=center><b><u>'.$data['name'].'</u></b></td>
	<td align=center><FORM METHOD=POST ACTION=""><INPUT TYPE="submit" VALUE="Tests Systeme" NAME="cmd" '.$btnN3.'><INPUT TYPE="hidden" VALUE="'.$key.'" NAME="name_sim"></FORM></td>
	<td align=center><FORM METHOD=POST ACTION=""><INPUT TYPE="submit" VALUE="TOUS DEMARRER" NAME="cmd" '.$btnN3.'><INPUT TYPE="hidden" VALUE="'.$key.'" NAME="name_sim"></FORM></td>
	<td align=center><FORM METHOD=POST ACTION=""><INPUT TYPE="submit" VALUE="TOUS ARRETER" NAME="cmd" '.$btnN3.'><INPUT TYPE="hidden" VALUE="'.$key.'" NAME="name_sim"></FORM></td>
	</tr></table><hr>';
	}
*/
	//**************************************************************************

	//******  Lecture du INI **********
		 $key = $_SESSION['opensim_select'];
		 $commande = $cmd_SYS_etat_OS2;
		 $commande1 = $cmd_SYS_Version_mono;
		
	//*****************************************************************
// CONSTRUCTION de la commande pour ENVOI sur la console via  SSH
//*****************************************************************
	if($_POST['cmd'])
	{
	// *** Affichage mode debug ***
	//echo $_POST['cmd'].'<br>';

	// *************** ACTION BOUTON TESTS SYSTEME ***********************
	// *******************************************************************
		if($_POST['cmd'] == 'Executer')
		{ 
			if (!function_exists("ssh2_connect")) die(" function ssh2_connect doesn't exist");
			// log in at server1.example.com on port 22
			if(!($con = ssh2_connect($hostnameSSH, 22))){
				echo " fail: unable to establish connection\n";
			} else 
			{// try to authenticate with username root, password secretpassword
				if(!ssh2_auth_password($con,$usernameSSH,$passwordSSH)) {
					echo "fail: unable to authenticate\n";
				} else {
							// allright, we're in!
							// execute a command
							if (!($stream = ssh2_exec($con, $commande ))) {
								echo "fail: unable to execute command\n";
							} else {
								// collect returning data from command
								stream_set_blocking($stream, true);
								$data = "";
								while ($buf = fread($stream,4096)) {
									echo $data .= $buf.'<br>';
								}
								fclose($stream);
							}
						}
			}
		}		
		
		if($_POST['cmd'] == 'Tests Systeme') 	// Serie de tests 
		{
		// Commande pour test Serveur lancer 
		echo 'Nombre de serveurs : '.NbOpensim().'<br>';
		// on se connecte à MySQL
		$db = mysql_connect($hostnameBDD, $userBDD, $passBDD);
		mysql_select_db($database,$db);
		$sql = 'SELECT * FROM moteurs';
		$req = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());	
		while($data = mysql_fetch_assoc($req))
		{

			echo '<hr><b>Serveur Name:'.$data["name"].' - Version:'.$data["version"].'</b><br>';
			echo "********************************<br>";
			
			// *** Lecture Fichier Regions.ini ***
				$tableauIni = parse_ini_file($data["address"]."Regions/".$FichierINIRegions, true);
				if($tableauIni == FALSE){echo 'prb lecture ini '.$data["address"]."Regions/".$FichierINIRegions.'<br>';}else {echo 'Fichier PRESENT Regions.ini OK<br>';}
				echo "********************************<br>";
				
				// *** Fichier PRESENT Fichier OpenSimDefaults.ini ***
				$tableauIniOS = parse_ini_file(INI_Conf_Moteur($key,"address").$FichierINIOpensim, true);
				if($tableauIni == FALSE){echo 'prb lecture ini '.$data["address"].$FichierINIOpensim.'<br>';}else {echo 'Fichier PRESENT OpenSimDefaults.ini OK<br>';}
				echo "********************************<br>";
				
				// *** Fichier PRESENT Fichier RunOpensim.sh ***
				$tableauIniOS = parse_ini_file(INI_Conf_Moteur($key,"address")."RunOpensim.sh", true);
				if($tableauIni == FALSE){echo 'prb lecture RunOpensim.sh '.$data["address"]."RunOpensim.sh".'<br>';}else {echo 'Fichier PRESENT RunOpensim.sh OK<br>';}
				echo "********************************<br>";
				
				// *** Fichier PRESENT Fichier ScreenSend ***
				$tableauIniOS = parse_ini_file(INI_Conf_Moteur($key,"address")."ScreenSend", true);
				if($tableauIni == FALSE){echo 'prb lecture ScreenSend '.$data["address"]."ScreenSend".'<br>';}else {echo 'Fichier PRESENT ScreenSend OK<br>';}
				echo "********************************<br>";
				
				// Test de connection par serveur *********************************
					if (!function_exists("ssh2_connect")) die("function ssh2_connect doesn't exist");
					// log in at server1.example.com on port 22
					if(!($con = ssh2_connect($hostnameSSH, 22))){
						echo "fail: unable to establish connection\n";
					} else 
					{// try to authenticate with username root, password secretpassword
						if(!ssh2_auth_password($con, $usernameSSH, $passwordSSH )) {
							echo "fail: unable to authenticate\n";
						} else {
							// allright, we're in!
							echo "Connection au serveur SSH OK<br>";
							echo "********************************<br>";
							echo " *******  =>> Liste des Moteurs en cours (TOUS) <<==  ****<br>";
							// execute a command
							$data = $data1 = "";
							if (!($stream = ssh2_exec($con, $commande ))) {
								echo "fail: unable to execute command\n";
							} else {
								// collect returning data from command
								stream_set_blocking($stream, true);
								while ($buf = fread($stream,4096)) {
									 $data .= $buf.'<br>';
								}
								fclose($stream);
							}
							if (!($stream = ssh2_exec($con, $commande1 ))) {
								echo "fail: unable to execute command\n";
							} else {
								// collect returning data from command
								stream_set_blocking($stream, true);
								while ($buf = fread($stream,4096)) {
									 $data1 .= $buf.'<br>';
								}
								fclose($stream);
							}
						}
					}
					//********************* Extraction Info Retour ************
					echo $data;
					echo "********************************<br>";
					echo $data1;
					echo "<hr><br>";
		} 
	}	
	// *************** ACTION BOUTON TOUS DEMARRER ***********************
	// *******************************************************************	
		if($_POST['cmd'] == 'TOUS DEMARRER')
		{
		// Commande pour test Serveur lancer 
		echo 'Nombre de serveurs : '.NbOpensim().'<br>';
		// on se connecte à MySQL
		$db = mysql_connect($hostnameBDD, $userBDD, $passBDD);
		mysql_select_db($database,$db);
		$sql = 'SELECT * FROM moteurs';
		$req = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());	
		while($data = mysql_fetch_assoc($req))
		{
			echo '<hr>Serveur Name:'.$data["name"].' - Version:'.$data["version"].'<br>';
			echo $commande = "cd ".$data["address"].";./RunOpensim.sh"; 

			// Test de connection par serveur *********************************
					if (!function_exists("ssh2_connect")) die("function ssh2_connect doesn't exist");
					// log in at server1.example.com on port 22
					if(!($con = ssh2_connect($hostnameSSH, 22))){
						echo "fail: unable to establish connection\n";
					} else 
					{// try to authenticate with username root, password secretpassword
						if(!ssh2_auth_password($con, $usernameSSH, $passwordSSH )) {
							echo "fail: unable to authenticate\n";
						} else {
							// allright, we're in!
							//echo "Connection au serveur SSH OK<br>";
							//echo "********************************<br>";
							//echo " *******  =>> Liste des Moteurs <<==  ****<br>";
							//echo "********************************<br>";
							// execute a command
							if (!($stream = ssh2_exec($con, $commande ))) {
								echo "fail: unable to execute command\n";
							} else {
								// collect returning data from command
								stream_set_blocking($stream, true);
								$data = "";
								while ($buf = fread($stream,4096)) {
									echo $data .= $buf.'<br>';
								}
								fclose($stream);
							}
						}
					}
					//echo '<br>******************************** <br>Fin Config Serveur Name:'.INI_Conf_Moteur($key,"name").'<br>********************************<br>';
					//echo "<hr><br>";
			}
		}
	
	// *************** ACTION BOUTON TOUS ARRETER ***********************
	// *******************************************************************	
		if($_POST['cmd'] == 'TOUS ARRETER')
		{
		echo 'Nombre de serveurs : '.NbOpensim().'<br>';
		// on se connecte à MySQL
		$db = mysql_connect($hostnameBDD, $userBDD, $passBDD);
		mysql_select_db($database,$db);
		$sql = 'SELECT * FROM moteurs';
		$req = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());	
		while($data = mysql_fetch_assoc($req))
		{
			echo '<hr>Serveur Name:'.$data["name"].' - Version:'.$data["version"].'<br>';
			echo $commande = "cd ".$data["address"].";./ScreenSend ".$data["name"]." shutdown";

			// Test de connection par serveur *********************************
					if (!function_exists("ssh2_connect")) die("function ssh2_connect doesn't exist");
					// log in at server1.example.com on port 22
					if(!($con = ssh2_connect($hostnameSSH, 22))){
						echo "fail: unable to establish connection\n";
					} else 
					{// try to authenticate with username root, password secretpassword
						if(!ssh2_auth_password($con, $usernameSSH, $passwordSSH )) {
							echo "fail: unable to authenticate\n";
						} else {
							// allright, we're in!
							//echo "Connection au serveur SSH OK<br>";
							//echo "********************************<br>";
							//echo " *******  =>> Liste des Moteurs <<==  ****<br>";
							//echo "********************************<br>";
							// execute a command
							if (!($stream = ssh2_exec($con, $commande ))) {
								echo "fail: unable to execute command\n";
							} else {
								// collect returning data from command
								stream_set_blocking($stream, true);
								$data = "";
								while ($buf = fread($stream,4096)) {
									echo $data .= $buf.'<br>';
								}
								fclose($stream);
							}
						}
					}
					//echo '<br>******************************** <br>Fin Config Serveur Name:'.INI_Conf_Moteur($key,"name").'<br>********************************<br>';
					//echo "<hr><br>";
			}
		}
	
//****
	echo "<center><b>Pour chaque action, merci de consulter le LOG avant de relancer une commande.</b></center><br>";
	}
	
// *******************************************************************	
mysql_close();	

}else{header('Location: index.php');   }
?>