<?php
// include_once './inc/functions.php';

if (isset($_SESSION['authentification']))
{

    echo '<p class="pull-right"><span class="label label-danger">Espace Securise Niveau '.$_SESSION['privilege'].'</span></p>';
    echo '<h1>Regions Map</h1>';
    echo '<div class="clearfix"></div>';
        
    echo '<p>Simulateur selectionne ';
    echo '<strong class="label label-info">'.$_SESSION['opensim_select'].' '.INI_Conf_Moteur($_SESSION['opensim_select'], "version").'</strong>';
    echo '</p>';

    // *******************************************************
    // Initialisation des variables ET du tableau
    // *******************************************************
    // Position Initial
    $px = 1000;
    $py = 1000;

    // Offset
    $ox = 0;
    $oy = 0;

    // Limite de 50x50
    $max = 2;
    
    for ($x = -$max; $x < ($max - 1); $x++)
    {
        // echo "<hr>X:".$x.'<hr>';
        // Limite de 50x50
        for($y = -$max; $y < ($max - 1); $y++)
        {
            // echo "<hr>Y:".$y.'<hr>';
            $Matrice[$x][$y]['name'] = "";	
            $Matrice[$x][$y]['img']  = "";
            $Matrice[$x][$y]['ip']   = "";
            $Matrice[$x][$y]['port'] = "";	
            $Matrice[$x][$y]['uuid'] = "";
        } 
    } 
    //*******************************************************

    // *******************************************************	
    // Lecture des regions.ini et enregistrement dans Matrice
    // *******************************************************
    // Parcours des serveur installes

    $db = mysql_connect($hostnameBDD, $userBDD, $passBDD);
    mysql_select_db($database, $db);

    $sql = 'SELECT * FROM moteurs';
    $req = mysql_query($sql) or die('Erreur SQL !<p>'.$sql.'</p>'.mysql_error());

    while($data = mysql_fetch_assoc($req))
    {
        // Pour chaque serveur
        $tableauIni = parse_ini_file($data['address']."Regions/Regions.ini", true);

        if ($tableauIni == FALSE)
        {
            echo 'Probleme Lecture Fichier .ini '.$data['address']."Regions/Regions.ini".'<br>';
        }

        // echo '<p>Serveur Name:'.$data['name'].' - Version:'.$data['version'].'</p>';
        // while (list($keyi, $vali) = each($tableauIni))
        while (list($keyi) = each($tableauIni))
        {
            // **** Recuperation du port http du serveur ******		
            // $filename = $data['address']."OpenSimDefaults.ini";
            $filename = $data['address'].$FichierINIOpensim;
       
            
            if (!$fp = fopen($filename, "r"))
            {
                echo "Echec de l'ouverture du fichier ".$filename;
            }		
            
            $tabfich = file($filename); 
            $c = count($tabfich);

            for ($i = 1; $i < $c; $i++)
            {
                // echo "<p>".$tabfich[$i]."</p>";
                $porthttp = strstr($tabfich[$i], "http_listener_port");
                
                if ($porthttp)
                {
                    $posEgal    = strpos($porthttp, '=');
                    $longueur   = strlen($porthttp);
                    $srvOS      = substr($porthttp, $posEgal + 1);
                }
            }
            fclose($fp);

            // Recuperation des valeurs ET enregistrement des valeurs dans le tableau
            // echo $key.$tableauIni[$key]['RegionUUID'].$tableauIni[$key]['Location'].$tableauIni[$key]['InternalPort'].'<br>';
            $location                               = explode(",", $tableauIni[$keyi]['Location']);
            $coordX                                 = $location[0] - $px - $ox;
            $coordY                                 = $location[1] - $py - $oy;
            $Matrice[$coordX][$coordY]['name']      = $keyi;
            $uuid                                   = str_replace("-", "", $tableauIni[$keyi]['RegionUUID']);
            $ImgMap                                 = "http://".$tableauIni[$keyi]['ExternalHostName'].":".trim($srvOS)."/index.php?method=regionImage".$uuid;
            $Matrice[$coordX][$coordY]['img']       = $ImgMap;
            $Matrice[$coordX][$coordY]['ip']        = $tableauIni[$keyi]['ExternalHostName'];
            $Matrice[$coordX][$coordY]['port']      = $tableauIni[$keyi]['InternalPort'];	
            $Matrice[$coordX][$coordY]['uuid']      = $key.$tableauIni[$keyi]['RegionUUID'];
            $Matrice[$coordX][$coordY]['hypergrid'] = $data[hypergrid];
        }
    }
    mysql_close();

    // ****************************
    // *** Map en construction ****
    // ****************************
    // echo $_POST['zooming'];
    if (isset($_POST['zooming']))
    {
        $widthMap = $_POST['zooming'];
        $heightMap = $_POST['zooming'];

        // $select = "";
        $_SESSION['zooming_select'] = trim($_POST['zooming']);
        if ($_SESSION['zooming_select'] == 25) {$select1 = "selected";}
        if ($_SESSION['zooming_select'] == 50) {$select2 = "selected";}
        if ($_SESSION['zooming_select'] == 100) {$select3 = "selected";}
        if ($_SESSION['zooming_select'] == 200) {$select4 = "selected";}
        if ($_SESSION['zooming_select'] == "") {$select5 = "selected";}
    }

    else
    {
        if ($_SESSION['zooming_select'] == 25) {$select1 = "selected";}
        if ($_SESSION['zooming_select'] == 50) {$select2 = "selected";}
        if ($_SESSION['zooming_select'] == 100) {$select3 = "selected";}
        if ($_SESSION['zooming_select'] == 200) {$select4 = "selected";}
        if ($_SESSION['zooming_select'] == "") {$select5 = "selected";}
        $widthMap = $_SESSION['zooming_select'];
        $heightMap = $_SESSION['zooming_select'];
    }

    echo '<form class="form-group" method=post action="">';
    echo '<div class="form-inline">';
    echo '<select class="form-control" name="zooming">';
    echo '<option value="25" name="zooming" '.$select1.'>Zoom 1</option>';
    echo '<option value="50" name="zooming" '.$select2.'>Zoom 2</option>';
    echo '<option value="100" name="zooming" '.$select3.'>Zoom 3</option>';
    echo '<option value="200" name="zooming" '.$select4.'>Zoom 4</option>';
    echo '<option value="" name="zooming" '.$select5.'>Zoom 5</option>';
    echo '</select>';
    echo ' <button type="submit" class="btn btn-success" name="goto">';
    echo '<i class="glyphicon glyphicon-ok"></i> Appliquer Zoom';
    echo '</button>';
    echo '</div>';
    
    echo '<br />';
    echo '<center>';
    echo '<table>';
    // for ($y = $max; $y > (-$max - 1); $y--) // Limite Y
    for ($x = -$max; $x < ($max + 1); $x++) // Limite X
    {
        echo '<tr>';
        // for ($x = -$max; $x < $max+1; $x++) // Limite X
        for($y = -$max; $y < ($max + 1); $y++) // Limite Y
        {
            echo '<td>';

            if ($Matrice[$x][$y]['img'])
            {
                $textemap = $Matrice[$x][$y]['name'];
                $locX = $Matrice[$x][$y]['locX'];
                $locY = $Matrice[$x][$y]['locY'];
                echo '<img class="img-responsive" src="'.$Matrice[$x][$y]['img'].'" width="'.$widthMap.'" height="'.$heightMap.'" alt="'.$textemap.'" title="'.$textemap.' '.$locX.' '.$locY.'" data-toggle="tooltip" data-placement="top">';
            }

            else
            {
                $textemap = "Water (Free)";
                echo '<img class="img-responsive" src="./img/water.jpg" width="'.$widthMap.'" height="'.$heightMap.'" alt="'.$textemap.'" title="'.$textemap.' '.$locX.' '.$locY.'" data-toggle="tooltip" data-placement="top">';
           }
            echo '</td>';
        } 
        echo '</tr>';
    } 
    echo '</table>';
    echo '</center>';		
}
else {header('Location: index.php');}
?>
