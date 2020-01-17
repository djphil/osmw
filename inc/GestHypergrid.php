<?php 
echo '<p class="pull-right"><span class="label label-danger">Espace Securise Niveau '.$_SESSION['privilege'].'</span></p>';
echo '<h1>Raccourcis Hypergrid</h1>';
echo '<div class="clearfix"></div>';

echo '<p>Simulateur selectionne ';
echo '<strong class="label label-info">'.$_SESSION['opensim_select'].' '.INI_Conf_Moteur($_SESSION['opensim_select'], "version").'</strong>';
echo '</p>';

$sql = 'SELECT * FROM moteurs';
$req = mysqli_query($db, $sql) or die('Erreur SQL !<p>'.$sql.'</p>'.mysqli_error($db));

// echo '<h4>Selectionner un Simulateur</h4>';
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

echo '</select>';
echo ' <button type="submit" class="btn btn-success"><i class="glyphicon glyphicon-ok"></i> Choisir</button>';
echo '</div>';
echo '</form>';

$sql = 'SELECT * FROM moteurs';
$req = mysqli_query($db, $sql) or die('Erreur SQL !<p>'.$sql.'</p>'.mysqli_error($db));

while ($data = mysqli_fetch_assoc($req))
{
    // $hypergrid = "";
    $hypergrid = $data['hypergrid'];
    $i = 0;

    if ($hypergrid != "")
    {
        if ($data['name'] == $_SESSION['opensim_select'])
        {
            $tableauIni = parse_ini_file($data['address']."Regions/".$FichierINIRegions, true);
            echo '<div class="btn-group form-group" role="group" aria-label="...">';
            echo '<a class="btn btn-success" href="secondlife://'.$hypergrid.'/jump4000/128/128/25/">Jump 4000</a>';
            echo '<a class="btn btn-warning" href="secondlife://'.$hypergrid.'/jump8000/128/128/25/">Jump 8000</a>';
            echo '</div>';

            if ($tableauIni == FALSE && $data['name'] == $_SESSION['opensim_select'])
            {
                echo '<div class="alert alert-danger alert-anim" role="alert">';
                echo 'Probleme de lecture du fichier .ini <strong>'.$FichierINIRegions.'</strong> ('.$data['address'].'Regions/)';
                echo '</div>';
            }

            if ($data['name'] == $_SESSION['opensim_select'])
            {
                echo '<div class="alert alert-success alert-anim" role="alert">';
                echo 'Le fichier <strong>'.$FichierINIRegions.'</strong> existe ('.$data['address'].'Regions/)';
                echo '</div>';
            }

            $cpt = 0;
            echo  '<div class="row">';

            foreach($tableauIni as $keyi => $vali)
            {
                // debug($vali);
                $filename = $data['address'].$FichierINIOpensim;

                if (!$fp = fopen($filename, "r"))
                {
                    echo '<div class="alert alert-danger alert-anim" role="alert">';
                    echo "Echec d'ouverture du fichier <strong>".$filename."</strong>";
                    echo '</div>';
                }

                $tabfich = file($filename);
                $n = count($tabfich);

                for ($i = 1 ; $i < $n; $i++)
                {
                    if (strpos($tabfich[$i], ";") === false)
                    {
                        $porthttp = strstr($tabfich[$i], "http_listener_port");

                        if ($porthttp)
                        {
                            $posEgal = strpos($porthttp, '=');
                            $longueur = strlen($porthttp);
                            $srvOS = substr($porthttp, $posEgal + 1);
                        }
                    }
                }
                fclose($fp);

                $ImgMap = "http://".$vali['ExternalHostName'].":".trim($srvOS)."/index.php?method=regionImage".str_replace("-", "", $vali['RegionUUID']);
                if (Test_Url($ImgMap) == false) {$ImgMap = "img/offline.jpg";}

                $TD_Hypergrid  = "";
                $TD_Hypergrid .= '<div class="col-sm-6 col-md-4">';
                $TD_Hypergrid .= '<div class="thumbnail">';
                $TD_Hypergrid .= '<img class=" btn3d btn btn-default img-rounded" alt="" src="'.$ImgMap.'">';
                $TD_Hypergrid .= '<div class="caption text-center">';
                $TD_Hypergrid .= '<h4>Region: <strong>'.$keyi.'</strong></h4>';
                $TD_Hypergrid .= '<p>Location: <strong>'.$vali['Location'].'</strong></p>';
                $TD_Hypergrid .= '<div class="btn-group" role="group" aria-label="...">';
                $TD_Hypergrid .= '<a class="btn btn-primary" href="secondlife://'.$keyi.'/128/128/25">TP Local</a>';
                $TD_Hypergrid .= '<a class="btn btn-success" href="secondlife://'.$hypergrid.'/'.$keyi.'/128/128/25">TP Hg</a>';
                $TD_Hypergrid .= '<a class="btn btn-warning" href="secondlife://http|!!'.$hypergrid.'/'.$keyi.'/128/128/25">TP Hg v3</a>';
                $TD_Hypergrid .= '</div>';
                $TD_Hypergrid .= '</div>';
                $TD_Hypergrid .= '</div>';
                $TD_Hypergrid .= '</div>';

                if ($cpt == 3)
                {
                    echo $TD_Hypergrid;
                    $cpt = 0;
                }

                else
                {
                    echo $TD_Hypergrid;
                    $cpt++;
                }
            }
            echo '</div>';
        }
    }
}
mysqli_close($db);
?>
