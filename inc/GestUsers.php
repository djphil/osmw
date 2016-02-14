<?php
if (isset($_SESSION['authentification']) && $_SESSION['privilege'] >= 3)
{
    echo '<p class="pull-right"><span class="label label-danger">Espace Securise Niveau '.$_SESSION['privilege'].'</span></p>';
    echo '<h1>Gestion des Utilisateurs</h1>';
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

	$db = mysql_connect($hostnameBDD, $userBDD, $passBDD);
	mysql_select_db($database,$db);

	if (isset($_POST['cmd']))
	{
		$clesprivilege = "";
		// ******************************************************
		// ****************** ACTION BOUTON *********************
		// ******************************************************

		if ($_POST['cmd'] == 'Reset')
		{
            echo '<h3>Modifier le Mot de passa</h3>';
			echo '<form method="post" action="">';
			echo '<table class="table table-hover">';
			echo '<input type="hidden" name="oldFirstName" value="'.$_POST['NewFirstName'].'" >';
			echo '<input type="hidden" name="oldLastName" value="'.$_POST['NewLastName'].'" >';

            echo '<tr>';
            echo '<th>Prenom</th>';
            echo '<th>Nom</th>';
            echo '<th>Nouveau Mot de Passe</th>';
            echo '<th>Confirmer le nouveau Mot de Passe</th>';
            echo '<th>Action</th>';
            echo '</tr>';

			echo '<tr>';
            echo '<td>'.$_POST['NewFirstName'].'</td>';
            echo '<td>'.$_POST['NewLastName'].'</td>';
			echo '<td><input class="form-control" type="password" name="NewPass1" value="" '.$btnN3.'></td>';
			echo '<td><input class="form-control" type="password" name="NewPass2" value="" '.$btnN3.'></td>';
			echo '<td><input class="btn btn-success" type="submit" value="Change" name="cmd" '.$btnN3.'></td>';
			echo '</tr>';
			echo '</table>';
			echo '</form>';
		}

		// ******************************************************
		if ($_POST['cmd'] == 'Ajouter')
		{
            echo '<button class="btn btn-danger pull-right" type="submit" value="Annuler" onclick=location.href="index.php?a=15" '.$btnN3.'>';
            echo '<i class="glyphicon glyphicon-remove"></i> Annuler</button>';

            echo '<h3>Ajouter un Utilisateur</h3>';

            echo '<form method="post" action="">';
			echo '<table class="table table-hover">';

            echo '<tr>';
            echo '<th>Simulateurs</th>';
            echo '<th>Privilege</th>';
            echo '<th>Firstname</th>';
            echo '<th>Lastname</th>';
            echo '<th>Password</th>';
            echo '<th>Action</th>';
            echo '</tr>';

			echo '<tr>';
            echo '<td>';
            $sql = 'SELECT * FROM moteurs';
			$req = mysql_query($sql) or die('Erreur SQL !<p>'.$sql.'</p>'.mysql_error());
			while($data = mysql_fetch_assoc($req))
			{
                echo '<div class="checkbox">';
                echo '<label><input type="checkbox" value="'.$data['osAutorise'].'" name="'.$data['id_os'].'">'.$data['id_os'].'</label>';
                echo '</div>';
            }
            echo '</td>';
			echo '<td>';
            echo '<select class="form-control" name="username_priv">';
            echo '<option value="1">Invite - Prive</option>';
            echo '<option value="2">Gestionnaire</option>';
            echo '<option value="3" >Administrateur</option>';
            echo '</select>';
			echo '</td>';

			echo '<td><input class="form-control" type="text" name="NewFirstName" placeholder="Prenom" '.$btnN3.'></td>';
			echo '<td><input class="form-control" type="text" name="NewLastName" placeholder="Nom" '.$btnN3.'></td>';
			echo '<td><input class="form-control" type="password" name="username_pass" placeholder="Password" '.$btnN3.'></td>';
            echo '<td>';
            echo '<button class="btn btn-success" type="submit" value="Enregistrer" name="cmd" '.$btnN3.'>';
            echo '<i class="glyphicon glyphicon-ok"></i> Enregistrer';
            echo '</button>';
            echo '</td>';
			echo '</tr>';

            echo '<tr>';
            echo '<td colspan="6">';
            echo '<div class="alert alert-warning fade in">';
            echo '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>';
            echo '<i class="glyphicon glyphicon-info-sign"></i>';
            echo ' <strong>MODE Invite</strong>: Pas de Simulateur autorise coche = <strong>MODE Demo</strong> ou Simulateur(s) autorise(s) coche(s) = <strong>MODE Prive</strong>';
            echo '</div>';
            echo '</td>';
            echo '</tr>';

			echo '</table>';
			echo '</form>';
		}

		// ******************************************************
		if ($_POST['cmd'] == 'Change')
		{
		    if ($_POST['NewPass1'] == $_POST['NewPass2'])
		    {	
		        $encryptedPassword = sha1($_POST['NewPass1']);
		        $sqlUp = "
                    UPDATE users 
                    SET `password` = '".$encryptedPassword."' 
                    WHERE `firstname` = '".$_POST['oldFirstName']."' 
                    AND `lastname` = '".$_POST['oldLastName']."'
                ";	
		        $reqUp = mysql_query($sqlUp) or die('Erreur SQL !<p>'.$sqlUp.'</p>'.mysql_error());

                echo "<p class='alert alert-success alert-anim'>";
                echo "<i class='glyphicon glyphicon-ok'></i>";
		        echo "Mot de passe modifie avec succes ...</p>";
		    }
			
			else
			{
                echo "<p class='alert alert-success alert-anim'>";
                echo "<i class='glyphicon glyphicon-ok'></i>";
			    echo "Mot de passe <b>NON</b> modifie, veuillez recommencer!</p>";
			}
		}

		// ******************************************************
		if ($_POST['cmd'] == 'Enregistrer')
		{
			$sql = 'SELECT * FROM moteurs';
			$req = mysql_query($sql) or die('Erreur SQL !<p>'.$sql.'</p>'.mysql_error());

			while($data = mysql_fetch_assoc($req))
			{
			    if ($_POST[$data['id_os']] != '')
				{
				    $clesprivilege = $clesprivilege.$_POST[$data['id_os']]."|";
				}
			}

			$encryptedPassword = sha1($_POST['username_pass']);
			$sqlIns = "
                INSERT INTO users (`firstname` ,`lastname` ,`password` ,`privilege`, `osAutorise`)
                VALUES (
                    '".$_POST['NewFirstName']."', 
                    '".$_POST['NewLastName']."', 
                    '".$encryptedPassword."', '".$_POST['username_priv']."', 
                    '".$clesprivilege."'
                )
            ";
			$reqIns = mysql_query($sqlIns) or die('Erreur SQL !<p>'.$sqlIns.'</p>'.mysql_error());

			echo "<p class='alert alert-success alert-anim'>";
            echo "<i class='glyphicon glyphicon-ok'></i>";
            echo " Utilisateur <strong>".$_POST['NewFirstName']." ".$_POST['NewLastName']."</strong> enregistre avec succes</p>";  
		}

		// ******************************************************
		if ($_POST['cmd'] == 'Modifier')
		{
            $sql = 'SELECT * FROM moteurs';
            $req = mysql_query($sql) or die('Erreur SQL !<p>'.$sql.'</p>'.mysql_error());
                
            while($data = mysql_fetch_assoc($req))
            {
                if ($_POST[$data['id_os']] != '')
                {
                    $clesprivilege = $clesprivilege.$_POST[$data['id_os']]."|";
                }
            }

            $sqlUp = "
                UPDATE users 
                SET `firstname` = '".$_POST['NewFirstName']."', 
                    `lastname` = '".$_POST['NewLastName']."', 
                    `privilege` = '".$_POST['username_priv']."', 
                    `osAutorise` = '".$clesprivilege."' 
                WHERE `firstname` = '".$_POST['oldFirstName']."' 
                AND `lastname` = '".$_POST['oldLastName']."'
            ";
            $reqUp = mysql_query($sqlUp) or die('Erreur SQL !<p>'.$sqlUp.'</p>'.mysql_error());

            echo "<p class='alert alert-success alert-anim'>";
            echo "<i class='glyphicon glyphicon-ok'></i>";
            echo " Utilisateur <strong>".$_POST['NewFirstName']." ".$_POST['NewLastName']."</strong> modifie avec succes</p>";            
        }
    }
		
    // ******************************************************
    if ($_POST['cmd'] == 'Supprimer')
    {	
        $sqlDel = "
            DELETE FROM users 
            WHERE `firstname` = '".$_POST['oldFirstName']."' 
            AND `lastname` = '".$_POST['oldLastName']."' 
        ";	
        $reqDel = mysql_query($sqlDel) or die('Erreur SQL !<p>'.$sqlDel.'</p>'.mysql_error());

        echo "<p class='alert alert-success alert-anim'>";
        echo "<i class='glyphicon glyphicon-ok'></i>";
        echo " Utilisateur <strong>".$_POST['NewFirstName']." ".$_POST['NewLastName']."</strong> supprime avec succes</p>";  
    }

    // ******************************************************
    // ************** LISTE DES UTILISATEURS ****************
    // ******************************************************
    if ($_POST['cmd'] != 'Ajouter')
    {
        echo '<form class="form-group pull-right" method="post" action="">';
        echo '<input type="hidden" value="Ajouter" name="cmd" '.$btnN3.'>';
        echo '<button class="btn btn-success" type="submit" '.$btnN3.'>';
        echo '<i class="glyphicon glyphicon-ok"></i> Ajouter un Utilisateur</button>';
        echo '</form>';

        echo '<h3>Liste des Utilisateurs</h3>';

        $sql = 'SELECT * FROM users ORDER BY id ASC';
        // $sql = 'SELECT * FROM users';
        $req = mysql_query($sql) or die('Erreur SQL !<p>'.$sql.'</p>'.mysql_error());

        while ($data = mysql_fetch_assoc($req)) {$n++;}

        echo "<p>Nombre total d'utilisateurs <span class='badge'>".$n."</span></p>";

        echo '<table class="table table-hover">';
        echo '<tr>';
        echo '<th>#</th>';
        echo '<th>Simulateurs</th>';
        echo '<th>Privilege</th>';
        echo '<th>Prenom</th>';
        echo '<th>Nom</th>';
        echo '<th>Password</th>';
        echo '<th>Modifier</th>';
        echo '<th>Supprimer</th>';
        echo '</tr>';

        $sql = 'SELECT * FROM users ORDER BY id ASC';
        $req = mysql_query($sql) or die('Erreur SQL !<p>'.$sql.'</p>'.mysql_error());
        $n = 0;

        while ($data = mysql_fetch_assoc($req))
        {
            $n++;
            $privilegetxt1 = $privilegetxt2 = $privilegetxt3 = 0;
            $privilege = $data['privilege'];
            $oldbtnN3 =  $btnN3;

            switch ($privilege)
            {
                case 1: $privilegetxt1 = "selected"; break;
                case 2: $privilegetxt2 = "selected"; break;
                case 3: $privilegetxt3 = "selected"; break;
                case 4: 

                if ($_SESSION['privilege'] == 4)
                {
                    $privilegetxt4 = "<option value='4' selected>Super Administrateur</option>";
                    $block = "";
                    $btnN3 = "";
                    break;
                }

                else
                {
                    $privilegetxt4 = "<option value='4' selected>Super Administrateur</option>";
                    $block = "disabled";
                    $btnN3 = "disabled";
                    break;
                }
            }

            echo '<tr>';
            echo '<form class="form-group" method="post" action="">';
            echo '<td><div class="badge">'.$n.'</div></td>';

            if ($data['privilege'] > 1) echo '<td>Tous les Simulateurs</td>';

            if ($data['privilege'] == 1)
            {
                echo '<td>';
                
                $sql1 = 'SELECT * FROM moteurs';
                $req1 = mysql_query($sql1) or die('Erreur SQL !<p>'.$sql1.'</p>'.mysql_error());

                while($data1 = mysql_fetch_assoc($req1))
                {
                    $moteursOK = "";
                    $osAutorise = explode("|", $data['osAutorise']);

                    // echo "osAutorise =  ".$data['osAutorise'];

                    for ($i = 0; $i < count($osAutorise); $i++)
                    {
                        if ($data1['osAutorise'] == $osAutorise[$i])
                        {
                            $moteursOK = "CHECKED";
                            break;
                        }
                    }

                    echo '<div class="checkbox">';
                    echo '<label>';
                    echo '<label><input type="checkbox" value="'.$data1['osAutorise'].'" name="'.$data1['id_os'].'" '.$moteursOK.'>'.$data1['id_os'].'</label>';
                    echo '</div>';
                }
                echo '</td>';
            }

            echo '<td>';
            echo '<input type="hidden" name="oldFirstName" value="'.$data['firstname'].'" >';
            echo '<input type="hidden" name="oldLastName" value="'.$data['lastname'].'" >';

            echo '<select class="form-control" name="username_priv" '.$block.'>';
            echo '<option value="1" '.$privilegetxt1.' >Invite - Prive</option>';
            echo '<option value="2" '.$privilegetxt2.'>Gestionnaire</option>';
            echo '<option value="3" '.$privilegetxt3.'>Administrateur</option>';
            echo '<option value="4" '.$privilegetxt4.'>Super Administrateur</option>';
            echo '</select>';
            echo '</td>';

            echo '<td><input class="form-control" type="text" name="NewFirstName" value="'.$data['firstname'].'" '.$btnN3.'></td>';
            echo '<td><input class="form-control" type="text" name="NewLastName" value="'.$data['lastname'].'" '.$btnN3.'></td>';
            echo '<td><button class="btn btn-danger" type="submit" name="cmd" value="Reset" '.$btnN3.'><i class="glyphicon glyphicon-refresh"></i> Reset</button></td>';

            echo '<td><button class="btn btn-success" type="submit" value="Modifier" name="cmd" '.$btnN3.'><i class="glyphicon glyphicon-edit"></i> Modifier</button></td>';
            echo '<td><button class="btn btn-danger" type="submit" value="Supprimer" name="cmd" '.$btnN3.'><i class="glyphicon glyphicon-trash"></i> Supprimer</button></td>';

            echo '</form>';
            echo '</tr>';

            if ($data['privilege'] == "1")
            {
                echo '<tr>';
                // echo '<td></td><td></td>';
                echo '<td colspan="8">';
                echo '<div class="alert alert-warning fade in">';
                echo '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>';
                echo '<i class="glyphicon glyphicon-info-sign"></i>';
                echo ' <strong>MODE Invite</strong>: Pas de Simulateur autorise coche = <strong>MODE Demo</strong> ou Simulateur(s) autorise(s) coche(s) = <strong>MODE Prive</strong>';
                echo '</div>';
                echo '</td>';
                echo '</tr>';
            }

            $btnN3 = $oldbtnN3;
            $privilegetxt4 = "";
            $block = "";
        }
    }

    echo '</table>';
	mysql_close();
}
else {header('Location: index.php');}
?>