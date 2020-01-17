<h1>Gestion fichiers sauvegardes</h1>
Gestion des fichiers sauvegardes du serveur
<?php
if (isset($_SESSION['authentification']))
{
    if (isset($_POST['OSSelect']))
    {
        $_SESSION['opensim_select'] = trim($_POST['OSSelect']);
    }
    echo 'Moteur sélectionné: '.$_SESSION['opensim_select'].' - '.INI_Conf_Moteur($_SESSION['opensim_select'],"version");

    $moteursOK = false;

    if ($_SESSION['osAutorise'] != '')
    {
        $osAutorise = explode(";", $_SESSION['osAutorise']);
        for($i = 0; $i < count($osAutorise);$i++)
        {
            if (INI_Conf_Moteur($_SESSION['opensim_select'], "osAutorise") == $osAutorise[$i]) {$moteursOK = true;}
        }
    }

	$btnN1 = "disabled";
    $btnN2 = "disabled";
    $btnN3 = "disabled";
	if ($_SESSION['privilege'] == 4) {$btnN1 = ""; $btnN2 = ""; $btnN3 = "";}   // Niv 4	
	if ($_SESSION['privilege'] == 3) {$btnN1 = ""; $btnN2 = ""; $btnN3 = "";}   // Niv 3
	if ($_SESSION['privilege'] == 2) {$btnN1 = ""; $btnN2 = "";}                // Niv 2
    // if ($_SESSION['privilege'] == 1) {$btnN1 = "";}                          // Niv 1
    if ($moteursOK == true) {if( $_SESSION['privilege'] == 1){$btnN1 = ""; $btnN2 = ""; $btnN3 = "";}}

    if (isset($_POST['cmd']) && $_POST['cmd'] == "Charger")
    { 
        echo $_POST['name_sim'];
        echo $_POST['name_file'];
    }

    $sql = 'SELECT * FROM moteurs';
    $req = mysqli_query($db, $sql) or die('Erreur SQL !<br />'.$sql.'<br>'.mysqli_error($db));

    echo '<form method=post action="">';
    echo '<select name="OSSelect">';
    while($data = mysqli_fetch_assoc($req))
    {
        $sel = "";
        if ($data['id_os'] == $_SESSION['opensim_select']) {$sel = "selected";}
        echo '<option value="'.$data['id_os'].'" '.$sel.'>'.$data['name'].' - '.$data['version'].'</option>';
    }
    echo '</select>';
    echo '<input type="submit" value="choisir" >';
    echo '</form>';
    mysqli_close($db);

    echo '<table class="table">';
    echo '<tr>';
    echo '<td>';
    if (!$dir) {$dir = INI_Conf_Moteur($_SESSION['opensim_select'], "address");} 
    list_file(rawurldecode($dir)); 
    echo '</td>';
    echo '</tr>';
    echo '</table>';
}
else {header('Location: index.php');}
?>
