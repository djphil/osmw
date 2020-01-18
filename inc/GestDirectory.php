<?php
if (isset($_SESSION['authentification']))
{
    echo '<p class="pull-right"><span class="label label-danger">Espace Securise Niveau '.$_SESSION['privilege'].'</span></p>';
    echo '<h1>Fichiers Sauvegardés</h1>';
    echo '<div class="clearfix"></div>';

    if (isset($_POST['OSSelect'])) {$_SESSION['opensim_select'] = trim($_POST['OSSelect']);}

    echo '<p>Simulateur sélectionné ';
    echo '<strong class="label label-info">'.$_SESSION['opensim_select'].' '.INI_Conf_Moteur($_SESSION['opensim_select'], "version").'</strong>';
    echo '</p>';

    if ($_SESSION['osAutorise'] != '')
    {
        $osAutorise = explode(";", $_SESSION['osAutorise']);

        for ($i = 0; $i < count($osAutorise); $i++)
        {
            if (INI_Conf_Moteur($_SESSION['opensim_select'], "osAutorise") == $osAutorise[$i])
            {
                $moteursOK = "OK";
            }
        }
    }
    else {$moteursOK = "NOK";}

    $btnN1 = "disabled"; $btnN2 = "disabled"; $btnN3 = "disabled";
    if ($_SESSION['privilege'] == 4) {$btnN1 = ""; $btnN2 = ""; $btnN3 = "";}   // Niv 4	
    if ($_SESSION['privilege'] == 3) {$btnN1 = ""; $btnN2 = ""; $btnN3 = "";}   // Niv 3
    if ($_SESSION['privilege'] == 2) {$btnN1 = ""; $btnN2 = "";}                // Niv 2
    // if ($_SESSION['privilege'] == 1) {$btnN1 = "";}                          // Niv 1
    if ($moteursOK == true) {if( $_SESSION['privilege'] == 1) {$btnN1 = ""; $btnN2 = ""; $btnN3 = "";}}

    /* Actions des Boutons */
    if (isset($_POST['cmd']))
    {
        if ($_POST['cmd'] == "download")
        { 
            echo INI_Conf_Moteur($_SESSION['opensim_select'], "address").$_POST['name_file']."<br />";
            $a = DownloadFile(INI_Conf_Moteur($_SESSION['opensim_select'], "address").$_POST['name_file']);
        }

        if ($_POST['cmd'] == "delete")
        {
            $cheminWIN = str_replace('/','\\',INI_Conf_Moteur($_SESSION['opensim_select'],"address"));;		
            $cmd= 'DEL '.$cheminWIN.$_POST['name_file'];	 
            exec_command($cmd);
            echo '<div class="alert alert-success alert-anim" role="alert">';
            echo 'Fichier '.$cheminWIN.$_POST['name_file'].' supprimé avec succès ...';
            echo '<strong> OpenSim.log</strong>';
            echo '</div>';
        }
    }

    $sql = 'SELECT * FROM moteurs';
    $req = mysqli_query($db, $sql) or die('Erreur SQL !<p>'.$sql.'</p>'.mysqli_error($db));

    echo '<form class="form-group" method="post" action="">';
    echo '<div class="form-inline">';
    echo '<label for="OSSelect"></label>Select Simulator ';
    echo '<select class="form-control" name="OSSelect">';

    while($data = mysqli_fetch_assoc($req))
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
    mysqli_close($db);
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
    $dir = "";
    $dir = INI_Conf_Moteur($_SESSION['opensim_select'], "address");

    if ($dir) {list_file(rawurldecode($dir));}

    else
    {
        echo '<div class="alert alert-danger alert-anim" role="alert">';
        echo 'Le <strong>chemin</strong> incorrecte ...';
        echo '</div>';
    }
}
else {header('Location: index.php');}
?>
