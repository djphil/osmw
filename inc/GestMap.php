<?php
if (isset($_SESSION['authentification']))
{

    echo '<p class="pull-right"><span class="label label-danger">Espace Securise Niveau '.$_SESSION['privilege'].'</span></p>';
    echo '<h1>Regions Map</h1>';
    echo '<div class="clearfix"></div>';
        
    echo '<p>Simulateur selectionne ';
    echo '<strong class="label label-info">'.$_SESSION['opensim_select'].' '.INI_Conf_Moteur($_SESSION['opensim_select'], "version").'</strong>';
    echo '</p>';

    $px = 10000;
    $py = 10000;
    // Offset
    $ox = 0;
    $oy = 0;

    // Limite de 5x5
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

    $sql = "SELECT * FROM moteurs";
	$req = mysqli_query($db, $sql) or die('Erreur SQL !<p>'.$sql.'</p>'.mysqli_error($db));

    while($data = mysqli_fetch_assoc($req))
    {
        $tableauIni = @parse_ini_file($data['address']."Regions/".$FichierINIRegions, true);

        if ($tableauIni == FALSE)
        {
            echo 'Probleme Lecture Fichier .ini '.$data['address']."Regions/Regions.ini".'<br>';
        }

        foreach($tableauIni AS $key => $val)
        {
            $filename = $data['address'].$FichierINIOpensim;

            if (!$fp = fopen($filename, "r"))
            {
                echo "Echec de l'ouverture du fichier ".$filename;
            }		

            $tabfich = file($filename); 
            $n = count($tabfich);
            $srvOS = 9000;

            for ($i = 1; $i < $n; $i++)
            {
                // if (strpos($tabfich[$i], ";") === false || strpos($tabfich[$i], ";;") === false)
                // strpos($tabfich[$i], "#") === false
                if (strpos($tabfich[$i], ";" ) === false)
                {
                    // echo "<p>".$tabfich[$i]."</p>";
                    $porthttp = strstr($tabfich[$i], "http_listener_port");

                    if(!empty($porthttp))
                    {
                        $posEgal = strpos($porthttp, '=');
                        $longueur = strlen($porthttp);
                        $srvOS = substr($porthttp, $posEgal + 1);
                    }
                }
            }
            fclose($fp);

            // Recuperation des valeurs ET enregistrement des valeurs dans le tableau
            // echo $key.$tableauIni[$key]['RegionUUID'].$tableauIni[$key]['Location'].$tableauIni[$key]['InternalPort'].'<br>';
            $location                               = explode(",", $val['Location']);
            $coordX                                 = $location[0] - $px - $ox;
            $coordY                                 = $location[1] - $py - $oy;
            $Matrice[$coordX][$coordY]['locX']      = $location[0];
            $Matrice[$coordX][$coordY]['locY']      = $location[1];

            // $Matrice[$coordX][$coordY]['name']      = $val;
            $Matrice[$coordX][$coordY]['name']      = $key;

            $uuid                                   = str_replace("-", "", $val['RegionUUID']);
            $ImgMap                                 = "http://".$val['ExternalHostName'].":".trim($srvOS)."/index.php?method=regionImage".$uuid;
            $Matrice[$coordX][$coordY]['img']       = $ImgMap;
            $Matrice[$coordX][$coordY]['ip']        = $val['ExternalHostName'];
            $Matrice[$coordX][$coordY]['port']      = $val['InternalPort'];	
            $Matrice[$coordX][$coordY]['uuid']      = $val['RegionUUID'];
            $Matrice[$coordX][$coordY]['hypergrid'] = $data['hypergrid'];
        }
    }
    mysqli_close($db);

    // ****************************
    // *** Map en construction ****
    // ****************************
    $_SESSION['zooming_select'] = null;

    if (isset($_POST['zooming']))
    {
        $zooming = trim($_POST['zooming']);
        $widthMap = $zooming;
        $heightMap = $zooming;
        $_SESSION['zooming_select'] = $zooming;
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
        $zooming = trim($_SESSION['zooming_select']);
        $widthMap = $zooming;
        $heightMap = $zooming;
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

    for ($y = $max; $y > (-$max - 1); $y--) // Limite Y
    // for ($x = -$max; $x < ($max + 1); $x++) // Limite X
    {
        echo '<tr>';
        for ($x = -$max; $x < $max + 1; $x++) // Limite X
        // for($y = -$max; $y < ($max + 1); $y++) // Limite Y
        {
            echo '<td>';

            if (!empty($Matrice[$x][$y]['img']))
            {
                if (!empty($Matrice[$x][$y]['name']))
                    $textemap = strval($Matrice[$x][$y]['name']);
                if (!empty($Matrice[$x][$y]['locX']))
                    $locX = strval($Matrice[$x][$y]['locX']);
                if (!empty($Matrice[$x][$y]['locY']))
                    $locY = strval($Matrice[$x][$y]['locY']);

                echo '<a href="secondlife://'.$Matrice[$x][$x]['hypergrid'].':'.$textemap.'">';
                echo '<img class="img-responsive" src="'.$Matrice[$x][$y]['img'].'" width="'.$widthMap.'" height="'.$heightMap.'" alt="'.$textemap.'" title="'.$textemap.' '.$locX.' '.$locY.'" data-toggle="tooltip" data-placement="top">';
                echo '</a>';

                // echo '<img class="img-responsive" src="'.$Matrice[$x][$y]['img'].'" width="'.$widthMap.'" height="'.$heightMap.'" alt="'.$textemap.'" data-toggle="popover" data-placement="top" data-content="'.$textemap.'">';
                // echo '<a href="secondlife://'.$Matrice[$x][$x]['hypergrid'].':'.$Matrice[$x][$y]['name'].'">';
                // echo '<img src="'.$Matrice[$x][$y]['img'].'" width="'.$widthMap.'" height="'.$heightMap.'"  alt="'.$textemap.'"></a>';
            }

            else
            {
                $textemap = "Water (Free)";
                $locX = $px + $x;
                $locY = $py + $y;
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
