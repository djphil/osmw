<?php 
if (isset($_SESSION['authentification']) && $_SESSION['privilege'] >= 3)
{
    echo '<p class="pull-right"><span class="label label-danger">Espace Securise Niveau '.$_SESSION['privilege'].'</span></p>';
    echo '<h1>Gestion des Simulateurs</h1>';
    echo '<div class="clearfix"></div>';

    echo '<p>Simulateur selectionne ';
    echo '<strong class="label label-info">'.$_SESSION['opensim_select'].' '.INI_Conf_Moteur($_SESSION['opensim_select'], "version").'</strong>';
    echo '</p>';

	$btnN1 = "disabled"; $btnN2 = "disabled"; $btnN3 = "disabled";
	if( $_SESSION['privilege'] == 4) {$btnN1 = ""; $btnN2 = ""; $btnN3 = "";} // Niv 4	
	if( $_SESSION['privilege'] == 3) {$btnN1 = ""; $btnN2 = ""; $btnN3 = "";} // Niv 3
	if( $_SESSION['privilege'] == 2) {$btnN1 = ""; $btnN2 = "";}              // Niv 2
	if( $_SESSION['privilege'] == 1) {$btnN1 = "";}                           // Niv 1
    // if ($moteursOK == true){if( $_SESSION['privilege'] == 1){$btnN1 = ""; $btnN2 = ""; $btnN3 = "";}}

 	echo '<form class="form-group" method="post" action="">';
    echo '<input type="hidden" name="cmd" value="Ajouter" '.$btnN3.'>';
	echo '<button class="btn btn-success" type="submit" value="Ajouter un Simulateur" '.$btnN3.'><i class="glyphicon glyphicon-ok"></i> Ajouter un Simulateur</button>';
	echo '</form>';

	if (isset($_POST['cmd']))
	{
		if($_POST['cmd'] == 'Ajouter')
		{
			$i = NbOpensim() + 1;
			echo '<form method=post sction="">';
			echo '<table class="table table-hover">';
			echo '<tr>';
			echo '<th>Name</th>';   
			echo '<th>Version</th>';
			echo '<th>Path</th>';
			echo '<th>HG url</th>';
			echo '<th>Database</th>';
            echo '<th>Save</th>';
			echo '</tr>';
			echo '<tr>';
            echo '<td><input class="form-control" type="text" name = "NewName" value="My Simulator '.$i.'" '.$btnN3.'"></td>';
            echo '<td><input class="form-control" type="text" name = "version" value="0.8.1.1" '.$btnN3.'"></td>';
            echo '<td><input class="form-control" type="text" name = "address" value="/home/user/simulateur'.$i.'/" '.$btnN3.'></td>';
            echo '<td><input class="form-control" type="text" name = "hypergrid" value="hg.simulator'.$i.'.com:8002" '.$btnN3.'></td>';
            echo '<td><input class="form-control" type="text" name = "DB_OS" value="My Simulator '.$i.' database" '.$btnN3.'></td>';
            echo '<td><input class="btn btn-success" type="submit" value="Enregistrer" name="cmd" '.$btnN3.'></td>';
            echo '</tr></table></form>';
		}

		if (isset($_POST['cmd']) && $_POST['cmd'] == 'Enregistrer')
		{	
			$sqlIns = "INSERT INTO moteurs (`osAutorise` ,`id_os` ,`name` ,`version` ,`address` , `DB_OS`, `hypergrid`)
                        VALUES (NULL , '".$_POST['NewName']."', '".$_POST['NewName']."', '".$_POST['version']."', '".$_POST['address']."', '".$_POST['DB_OS']."', '".$_POST['hypergrid']."')";
			$reqIns = mysqli_query($db, $sqlIns) or die('Erreur SQL !<p>'.$sqlIns.'</p>'.mysqli_error($db));
            
			echo "<p class='alert alert-success alert-anim'>";
            echo "<i class='glyphicon glyphicon-ok'></i>";
            echo " Simulateur <strong>".$_POST['NewName']."</strong> ajoute avec succes</p>";
		} 
        
		if (isset($_POST['cmd']) && $_POST['cmd'] == 'Update')
		{
			$sqlIns = "
                UPDATE moteurs 
                SET 
                    id_os = '".$_POST['NewName']."',
                    name = '".$_POST['NewName']."',
                    version = '".$_POST['version']."',
                    address = '".$_POST['address']."',
                    DB_OS = '".$_POST['DB_OS']."',
                    hypergrid = '".$_POST['hypergrid']."'
                WHERE id_os = '".$_POST['NewName']."'
            ";
            $reqIns = mysqli_query($db, $sqlIns) or die('Erreur SQL !<p>'.$sqlIns.'</p>'.mysqli_error($db));
			echo "<p class='alert alert-success alert-anim'>";
            echo "<i class='glyphicon glyphicon-ok'></i>";
            echo " Simulateur <strong>".$_POST['NewName']."</strong> mis a jour avec succes</p>";
		}

		if (isset($_POST['cmd']) && $_POST['cmd'] == 'Supprimer')
		{			
			$sqlIns = "DELETE FROM moteurs WHERE `moteurs`.`osAutorise` = ".$_POST['osAutorise'];
			$reqIns = mysqli_query($db, $sqlIns) or die('Erreur SQL !<p>'.$sqlIns.'</p>'.mysqli_error($db));
			echo "<p class='alert alert-success alert-anim'>";
            echo "<i class='glyphicon glyphicon-ok'></i>";
            echo " Simulateur <strong>".$_POST['NewName']."</strong> supprime avec succes</p>";
		}
    }

	$sql = 'SELECT * FROM moteurs';
	$req = mysqli_query($db, $sql) or die('Erreur SQL !<p>'.$sql.'</p>'.mysqli_error($db));
    
	if (NbOpensim() >= 4) {$btn = 'disabled';}
	else {$btn = $btnN3;}

	if (INI_Conf("Parametre_OSMW", "Autorized") == '1') {$btn = '';}

    echo '<p>Nombre total de Simulateurs <span class="badge">'.NbOpensim().'</span></p>';
	echo '<table class="table table-hover">';
	echo '<tr>';
	echo '<th>Name</th>';
	echo '<th>Version</th>';
	echo '<th>Path</th>';
	echo '<th>HG url</th>';
	echo '<th>Database</th>';
	echo '<th>Edit</th>';
    echo '<th>Delete</th>';
	echo '</tr>';

	while($data = mysqli_fetch_assoc($req))
	{
		echo '<tr>';
		echo '<form method=post action="">';
		echo '<input type="hidden" name="osAutorise" value="'.$data['osAutorise'].'" >';
		echo '<tr>';
		echo '<td><input class="form-control" type="text" name="NewName" value="'.$data['name'].'" '.$btnN3.'></td>';
		echo '<td><input class="form-control" type="text" name="version" value="'.$data['version'].'" '.$btnN3.'></td>';
		echo '<td><input class="form-control" type="text" name="address" value="'.$data['address'].'" '.$btnN3.'></td>';
		echo '<td><input class="form-control" type="text" name="hypergrid" value="'.$data['hypergrid'].'" '.$btnN3.'></td>';
		echo '<td><input class="form-control" type="text" name="DB_OS" value="'.$data['DB_OS'].'" '.$btnN3.'></td>';
        echo '<td><button class="btn btn-success" type="submit" name="cmd" value="Update" '.$btnN3.'><i class="glyphicon glyphicon-edit"></i> Update</button></td>';
        echo '<td><button class="btn btn-danger" type="submit" name="cmd" value="Supprimer" '.$btnN3.'><i class="glyphicon glyphicon-trash"></i> Supprimer</button></td>';
		echo '</tr>';
		echo '</form>';
		echo '</tr>';
	}
	echo '</table>';
    mysqli_close($db);
}
else {header('Location: index.php');}
?>
