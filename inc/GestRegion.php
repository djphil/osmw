<?php 
if (isset($_SESSION['authentification']) && $_SESSION['privilege'] >= 3)
{
    echo '<p class="pull-right"><span class="label label-danger">Espace Securise Niveau '.$_SESSION['privilege'].'</span></p>';
    echo '<h1>Configuration des Regions</h1>';
    echo '<div class="clearfix"></div>';

    echo '<p>Simulateur selectionne ';
    echo '<strong class="label label-info">'.$_SESSION['opensim_select'].' '.INI_Conf_Moteur($_SESSION['opensim_select'], "version").'</strong>';
    echo '</p>';

    $btnN1 = "disabled"; $btnN2 = "disabled"; $btnN3 = "disabled";
    if ($_SESSION['privilege'] == 4) {$btnN1 = ""; $btnN2 = ""; $btnN3 = "";}   // Niv 4	
    if ($_SESSION['privilege'] == 3) {$btnN1 = ""; $btnN2 = ""; $btnN3 = "";}   // Niv 3
    if ($_SESSION['privilege'] == 2) {$btnN1 = ""; $btnN2 = "";}                // Niv 2
    if ($_SESSION['privilege'] == 1) {$btnN1 = "";}                             // Niv 1
    // if ($moteursOK == true) {if( $_SESSION['privilege'] == 1) {$btnN1 = ""; $btnN2 = ""; $btnN3 = "";}}

    $sql = 'SELECT * FROM moteurs';
    $req = mysqli_query($db, $sql) or die('Erreur SQL !<p>'.$sql.'</p>'.mysqli_error($db));

    // echo '<h4>Selectionner un Simulateur</h4>';
    echo '<form class="form-group" method="post" action="">';
    echo '<div class="form-inline">';
    echo '<label for="OSSelect"></label>Select Simulator ';
    echo '<select class="form-control" name="OSSelect">';

    while($data = mysqli_fetch_assoc($req))
    {
        if ($data['osAutorise'] != '') {echo $data['osAutorise'];}
        else {$osAutorise = explode(";", $data['osAutorise']);
        echo count($osAutorise);}
        $sel = "";
        if ($data['id_os'] == $_SESSION['opensim_select']) {$sel = "selected";}
        echo '<option value="'.$data['id_os'].'" '.$sel.'>'.$data['name'].' '.$data['version'].'</option>';
    }

    echo'</select>';
    echo' <button type="submit" class="btn btn-success"><i class="glyphicon glyphicon-ok"></i> Choisir</button>';
    echo '</div>';
    echo'</form>';
    mysqli_close($db);

    $RegionMax = INI_Conf('NbAutorized', 'NbAutorized');
    echo '<p>Nombre Maximum de Regions Aurorisees <span class="badge">'.$RegionMax.'</span></p>';

    echo '<form class="form-group" method="post" action="">';
    echo '<input type="hidden" name="cmd" value="Ajouter" '.$btnN3.'>';
    echo '<button class="btn btn-success" type="submit" '.$btnN3.'><i class="glyphicon glyphicon-ok"></i> Ajouter une Region</button>';
    echo '</form>';

    if (isset($_POST['cmd']))
    {
        $filename = INI_Conf_Moteur($_SESSION['opensim_select'], "address")."Regions/Regions.ini";

        if (file_exists($filename)) {;}
        else {echo "<div class='alert alert-danger alert-anim'>Le fichier $filename n'existe pas.</div>";}

        $tableauIni = parse_ini_file($filename, true);
        if ($tableauIni == FALSE) {echo '<p>Probleme de lecture du fichier .ini $filename</p>';}

        if ($_POST['cmd'] == 'Ajouter')
        {
            echo '<form method="post" action="">';
            echo '<table class="table table-hover">';
            echo '<tr>';
            echo '<th>Name</th>';
            echo '<th>Location</th>';
            echo '<th>Internal Port</th>';
            echo '<th>Public IP</th>';
            echo '<th>Uuid (auto generate)</th>';
            echo '<th>Action</th>';
            echo '</tr>';

            echo '<tr>';
            echo '<td><input class="form-control" type="text" name="NewName" placeholder="Nom de la region" '.$btnN3.'></td>';
            echo '<td><input class="form-control" type="text" name="Location" placeholder="5000,5000" '.$btnN3.'></td>';
            echo '<td><input class="form-control" type="text" name="InternalPort" placeholder="9000" '.$btnN3.'></td>';
            echo '<td><input class="form-control" type="text" name="ExternalHostName" placeholder="domaine.com" '.$btnN3.'></td>';
            echo '<td><input class="form-control" type="text" name="RegionUUID" value="'.GenUUID().'" '.$btnN3.'></td>';
            echo '<td><button class="btn btn-success" type="submit" value="Ajouter" name="cmd" '.$btnN3.'><i class="glyphicon glyphicon-ok"></i> Ajouter</button></td>';
            echo '</table>';
            echo '</form>';
        }

        if ($_POST['cmd'] == 'Enregistrer')
        {
            $tableauIni[$_POST['NewName']]['RegionUUID']        = $_POST['RegionUUID'];
            $tableauIni[$_POST['NewName']]['Location']          = $_POST['Location'];
            $tableauIni[$_POST['NewName']]['InternalAddress']   = "0.0.0.0";
            $tableauIni[$_POST['NewName']]['InternalPort']      = $_POST['InternalPort'];
            $tableauIni[$_POST['NewName']]['ExternalHostName']  = $_POST['ExternalHostName'];

            $fp = fopen (INI_Conf_Moteur($_SESSION['opensim_select'], "address")."Regions/RegionTemp.ini", "w");  
            foreach($tableauIni as $key => $val)
            {
                fputs($fp, "[".$key."]\r\n");
                fputs($fp, "RegionUUID          = ".$tableauIni[$key]['RegionUUID']."\r\n");
                fputs($fp, "Location            = ".$tableauIni[$key]['Location']."\r\n");
                fputs($fp, "InternalAddress     = 0.0.0.0\r\n");
                fputs($fp, "InternalPort        = ".$tableauIni[$key]['InternalPort']."\r\n");
                fputs($fp, "AllowAlternatePorts = False\r\n");
                fputs($fp, "ExternalHostName    = ".$tableauIni[$key]['ExternalHostName']."\r\n");
            }
            fclose ($fp);  
            exec_command("chmod -R 777 ".INI_Conf_Moteur($_SESSION['opensim_select'], "address")."Regions/");
            unlink($filename); 
            rename(INI_Conf_Moteur($_SESSION['opensim_select'], "address")."Regions/RegionTemp.ini",$filename);

            echo "<p class='alert alert-success alert-anim'>";
            echo "<i class='glyphicon glyphicon-ok'></i>";
            echo " Region <strong>".$_POST['NewName']."</strong> enregistree avec succes</p>";
        }

        if ($_POST['cmd'] == 'Modifier')
        {
            if ($_POST['name_sim'] == $_POST['NewName'])
            {
                $tableauIni[$_POST['NewName']]['RegionUUID']        = $_POST['RegionUUID'];
                $tableauIni[$_POST['NewName']]['Location']          = $_POST['Location'];
                $tableauIni[$_POST['NewName']]['InternalAddress']   = "0.0.0.0";
                $tableauIni[$_POST['NewName']]['InternalPort']      = $_POST['InternalPort'];
                $tableauIni[$_POST['NewName']]['ExternalHostName']  = $_POST['ExternalHostName'];
            }

            if ($_POST['name_sim'] <> $_POST['NewName'])
            {
                $tableauIni[$_POST['NewName']]['RegionUUID']        = $_POST['RegionUUID'];
                $tableauIni[$_POST['NewName']]['Location']          = $_POST['Location'];
                $tableauIni[$_POST['NewName']]['InternalAddress']   = "0.0.0.0";
                $tableauIni[$_POST['NewName']]['InternalPort']      = $_POST['InternalPort'];
                $tableauIni[$_POST['NewName']]['ExternalHostName']  = $_POST['ExternalHostName'];
                unset($tableauIni[$_POST['name_sim']]['RegionUUID']);
                unset($tableauIni[$_POST['name_sim']]['Location']);
                unset($tableauIni[$_POST['name_sim']]['InternalAddress']);
                unset($tableauIni[$_POST['name_sim']]['InternalPort']);
                unset($tableauIni[$_POST['name_sim']]['AllowAlternatePorts']);
                unset($tableauIni[$_POST['name_sim']]['ExternalHostName']);
                unset($tableauIni[$_POST['name_sim']]);
            }

            $fp = fopen (INI_Conf_Moteur($_SESSION['opensim_select'], "address")."Regions/RegionTemp.ini", "w");  
            foreach($tableauIni as $key => $val)
            {
                fputs($fp, "[".$key."]\r\n");
                fputs($fp, "RegionUUID          = ".$tableauIni[$key]['RegionUUID']."\r\n");
                fputs($fp, "Location            = ".$tableauIni[$key]['Location']."\r\n");
                fputs($fp, "InternalPort        = ".$tableauIni[$key]['InternalPort']."\r\n");
                fputs($fp, "InternalAddress     = 0.0.0.0\r\n");
                fputs($fp, "AllowAlternatePorts = False\r\n");
                fputs($fp, "ExternalHostName    = ".$tableauIni[$key]['ExternalHostName']."\r\n");
            }
            fclose ($fp);  
            unlink($filename); 
            rename(INI_Conf_Moteur($_SESSION['opensim_select'],"address")."Regions/RegionTemp.ini", $filename); 
            exec_command("chmod -R 777 ".INI_Conf_Moteur($_SESSION['opensim_select'], "address")."Regions/");
            echo "<p class='alert alert-success alert-anim'>";
            echo "<i class='glyphicon glyphicon-ok'></i>";
            echo " Region <strong>".$_POST['NewName']."</strong> modifiee avec succes</p>";
        } 

        if ($_POST['cmd'] == 'Supprimer')
        {			
            unset($tableauIni[$_POST['name_sim']]['RegionUUID']);
            unset($tableauIni[$_POST['name_sim']]['Location']);
            unset($tableauIni[$_POST['name_sim']]['InternalAddress']);
            unset($tableauIni[$_POST['name_sim']]['InternalPort']);
            unset($tableauIni[$_POST['name_sim']]['AllowAlternatePorts'] );
            unset($tableauIni[$_POST['name_sim']]['ExternalHostName']);
            unset($tableauIni[$_POST['name_sim']]);

            $fp = fopen (INI_Conf_Moteur($_SESSION['opensim_select'], "address")."Regions/RegionTemp.ini", "w");  
            foreach($tableauIni as $key => $val)
            {
                fputs($fp, "[".$key."]\r\n");
                fputs($fp, "RegionUUID          = ".$tableauIni[$key]['RegionUUID']."\r\n");
                fputs($fp, "Location            = ".$tableauIni[$key]['Location']."\r\n");
                fputs($fp, "InternalAddress     = 0.0.0.0\r\n");
                fputs($fp, "InternalPort        = ".$tableauIni[$key]['InternalPort']."\r\n");
                fputs($fp, "AllowAlternatePorts = False\r\n");
                fputs($fp, "ExternalHostName    = ".$tableauIni[$key]['ExternalHostName']."\r\n");
            }

            fclose ($fp);  
            unlink($filename); 
            rename(INI_Conf_Moteur($_SESSION['opensim_select'],"address")."Regions/RegionTemp.ini",$filename); 
            exec_command("chmod -R 777 ".INI_Conf_Moteur($_SESSION['opensim_select'], "address")."Regions/");
            echo "<p class='alert alert-success alert-anim'>";
            echo "<i class='glyphicon glyphicon-ok'></i>";
            echo " Region <strong>".$_POST['NewName']."</strong> supprimee avec succes</p>";
        } 
    }

    $filename2 = INI_Conf_Moteur($_SESSION['opensim_select'], "address")."Regions/Regions.ini";

    if (file_exists($filename2)) {$filename = $filename2;}
    else
    {
        echo "<p class='alert alert-success alert-anim'>";
        echo "<i class='glyphicon glyphicon-ok'></i>";
        echo " Fichier <strong>".$filename2."</strong> innexistant ...</p>";
    }

    $tableauIni = parse_ini_file($filename, true);

    if ($tableauIni == FALSE)
    {
        echo "<p class='alert alert-success alert-anim'>";
        echo "<i class='glyphicon glyphicon-ok'></i>";
        echo " Problemes de lecture du fichier .ini <strong>".$filename."</strong> ...</p>";
    }

    $i = 0;
    if (count($tableauIni) >= $RegionMax) {$btn = 'disabled';}
    else {$btn = $btnN3;}

    if (INI_Conf("Parametre_OSMW", "Autorized") == '1') {$btn = '';}

    echo '<p>Nombre total de Regions <span class="badge">'.count($tableauIni).'</span></p>';
    echo '<table class="table table-hover">';
    echo '<tr>';
    echo '<th>Name</th>';
    echo '<th>Location</th>';
    echo '<th>Port Http</th>';
    echo '<th>Public Ip</th>';
    echo '<th>Uuid</th>';
    echo '<th>Modify</th>';
    echo '<th>Delete</th>';
    echo '</tr>';

    foreach($tableauIni as $key => $val)
    {
        echo '<tr>';
        echo '<form class="form-group" method="post" action="">';
        echo '<input type="hidden" name="name_sim" value="'.$key.'" >';
        echo '<tr>';
        echo '<td><input class="form-control" type="text" name="NewName" value="'.$key.'" '.$btnN3.'></td>';
        echo '<td><input class="form-control" type="text" name="Location" value="'.$tableauIni[$key]['Location'].'" '.$btnN3.'></td>';
        echo '<td><input class="form-control" type="text" name="InternalPort" value="'.$tableauIni[$key]['InternalPort'].'" '.$btnN3.'></td>';
        echo '<td><input class="form-control" type="text" name="ExternalHostName" value="'.$tableauIni[$key]['ExternalHostName'].'" '.$btnN3.'></td>';
        echo '<td><input class="form-control" type="text" name="RegionUUID" value="'.$tableauIni[$key]['RegionUUID'].'" '.$btnN3.'></td>';
        echo '<td><button class="btn btn-success" type="submit" value="Modifier" name="cmd" '.$btnN3.'><i class="glyphicon glyphicon-edit"></i> Modifier</button></td>';
        echo '<td><button class="btn btn-danger" type="submit" value="Supprimer" name="cmd" '.$btnN3.'><i class="glyphicon glyphicon-trash"></i> Supprimer</button></td>';
        echo '</tr>';
        echo '</form>';
        echo '</tr>';
    }
    echo '</table>';
}
else {header('Location: index.php');}
?>
