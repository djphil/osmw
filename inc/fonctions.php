<?php
function debug($variable)
{
    echo '<pre>'.print_r($variable, true).'</pre>';
}

function str_random($length)
{
    $alphabet = "0123456789azertyuiopqsdfghjklmwxcvbnAZERTYUIOPQSDFGHJKLMWXCVBN";
    return substr(str_shuffle(str_repeat($alphabet, $length)), 0, $length);
}

function build_url($folder)
{
    return 'css/'.htmlspecialchars($folder).'/'.htmlspecialchars($folder).'.css';
}

/* For NPC's */
function LectureFichier($file)
{
    error_reporting(0);
    $monfichier = fopen("inc/".$file, 'r+');
    $ligne = fgets($monfichier);
    fclose($monfichier);
    return '<pre title="Contenu du fichier ...">'.$ligne.'</pre>';
}

function EcritureFichier($file,$commande)
{
    error_reporting(0);
    $monfichier = fopen("inc/".$file, 'w+');
    fseek($monfichier, 0); 
    fputs($monfichier, $commande); 
    fclose($monfichier);
}

/* */
function ExtractValeur($chaine)
{
    $posEgal = strpos($chaine, ';');

    if($posEgal <> 0)
    {
        $longueur = strlen($chaine);
        $ExtractValeur[0] = "Commentaire:";
        $ExtractValeur[1] = substr($chaine, 0, $longueur );
        return $ExtractValeur;
    }
    
    $posEgal = strpos($chaine, '=');

    if ($posEgal === false)
    {
        $posEgal2 = strpos($chaine,']');
        $longueur = strlen($chaine);
        $ExtractValeur[0] = "CLES";
        $ExtractValeur[1] = substr($chaine, 1, $longueur - 3);
        return $ExtractValeur;
    }

    else
    {
        $longueur = strlen($chaine);
        $ExtractValeur[0] = trim(substr($chaine, 0, $posEgal - 1));
        $ExtractValeur[1] = trim(substr($chaine, $posEgal + 1));
        return $ExtractValeur;
    }
}

/* */
function INI_Conf($cles, $valeur)
{
    require('inc/config.php');
    require('inc/mysqli.php');

    $sql = "SELECT * FROM config";
    $req = mysqli_query($db, $sql) or die('Erreur SQL !<p>'.$sql.'</p>'.mysqli_error($db));
    $data = mysqli_fetch_array($req);
    
    switch ($valeur)
    {
        default:
            $Version = "N.C";
        case "cheminAppli":
            $Versions = $data['cheminAppli'];
            break;
        case "destinataire":
            $Version = $data['destinataire'];
            break;
        case "Autorized":
            $Version = $data['Autorized'];
            break;
        case "NbAutorized":
            $Version = $data['NbAutorized'];
            break;
        case "VersionOSMW":
            $Version = $data['VersionOSMW'];
            break;
        case "urlOSMW":
            $Version = $data['urlOSMW'];
            break;
    }
    mysqli_close($db);
    return $Version;
}

/* */
function INI_Conf_Moteur($cles, $valeur)
{
    require('inc/config.php');
    require('inc/mysqli.php');

    $sql = "SELECT * FROM moteurs WHERE id_os = '".$cles."'";
    $req = mysqli_query($db, $sql) or die('Erreur SQL !<p>'.$sql.'</p>'.mysqli_error($db));
    $data = mysqli_fetch_array($req);
    $Version = "";

    switch ($valeur)
    {
        default:
            $Version = "N.C";
        case "name":
            $Versions = $data['name'];
            break;
        case "version":
            $Version = $data['version'];
            break;
        case "address":
            $Version = $data['address'];
            break;
        case "DB_OS":
            $Version = $data['DB_OS'];
            break;
        case "osAutorise":
            $Version = $data['osAutorise'];
            break;
    }
    mysqli_close($db);
    return $Version;
}

/* */
function NbOpensim()
{
    global $db;
    $cpt = "SELECT COUNT(DISTINCT id_os) AS compteur FROM moteurs";
    $req = mysqli_query($db, $cpt); 
    $tab = mysqli_fetch_array($req) ;
    $Version = $tab["compteur"];
    return $Version;
}

/* */
function exec_command($cmd)
{
    $cmd = escapeshellcmd($cmd);
    $result = exec($cmd);
    return $result;
}

/* */
function GenUUID()
{
    return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
    mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff),
    mt_rand(0, 0x0fff) | 0x4000,
    mt_rand(0, 0x3fff) | 0x8000,
    mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff));
}

/* */
function Test_Url($server)
{
    $tab = parse_url($server);
    $tab['port'] = isset($tab['port']) ? $tab['port'] : 80;

    if (false != ($fp = @fsockopen($tab['host'], $tab['port'], $errno, $errstr, 1)))
    {
        fclose($fp);
        // echo 'Location: ' . $server; 
        return true;
    }
    // echo 'Erreur #' . $errno . ' : ' . $errstr;
    return false;
}

/* Matrice pour transfert de fichiers */
function gen_matrice($cur)
{
    global $PHP_SELF, $order, $asc, $order0;

    if ($dir = opendir($cur))
    {
        /* tableaux */
        $tab_dir = array();
        $tab_file = array();

        /* extraction */
        while($file = readdir($dir))
        {
            if (is_dir($cur."/".$file))
            {
                if (!in_array($file, array(".", "..")))
                {
                    $tab_dir[] = addScheme($file, $cur, 'dir');
                }
            }
            else {$tab_file[] = addScheme($file, $cur, 'file');}
        }

        foreach($tab_file as $elem) 
        {
            if (assocExt($elem['ext']) <> 'inconnu')
            {
                echo '<div class="checkbox">';
                echo '<label><input type="checkbox" name="matrice[]" value="'.$elem['name'].'">';
                echo ' <i class="glyphicon glyphicon-saved text-success"></i> '.$elem['name'].' ';
                echo '</label> ';
                echo formatSize($elem['size']);
                echo '</div>';
            }
        }
        closedir($dir);
    }
}

/* GestDirectory.php */
/* Files List */
function list_file($cur)
{
    global $PHP_SELF, $order, $asc, $order0;

    if ($dir = opendir($cur))
    {
        /* tableaux */
        $tab_dir = array();
        $tab_file = array();

        /* extraction */
        while($file = readdir($dir))
        {
            if (is_dir($cur."/".$file))
            {
                if (!in_array($file, array(".", "..")))
                {
                    $tab_dir[] = addScheme($file, $cur, 'dir');
                }
            }
            else {$tab_file[] = addScheme($file, $cur, 'file');}
        }

        $btnN1 = "disabled";
        $btnN2 = "disabled";
        $btnN3 = "disabled";
        if ($_SESSION['privilege'] == 4) {$btnN1 = ""; $btnN2 = ""; $btnN3 = "";} // Niv 4
        if ($_SESSION['privilege'] == 3) {$btnN1 = ""; $btnN2 = ""; $btnN3 = "";} // Niv 3
        if ($_SESSION['privilege'] == 2) {$btnN1 = ""; $btnN2 = "";}              // Niv 2

        echo "<table class='table table-striped table-hover'>";
        echo "<tr>";
        echo "<th>".(($order == 'name') ? (($asc == 'a')?'/\\ ':'\\/ '):'')."Nom</th>";
        echo "<th>".(($order == 'size') ? (($asc == 'a')?'/\\ ':'\\/ '):'')."Taille</th>";
        echo "<th>".(($order == 'date') ? (($asc == 'a')?'/\\ ':'\\/ '):'')."Date</th>";
        echo "<th>".(($order == 'time') ? (($asc == 'a')?'/\\ ':'\\/ '):'')."Time</th>";
        echo "<th>".(($order == 'ext') ? (($asc == 'a')?'/\\ ':'\\/ '):'')."Type</th>";
        echo "<th>".(($order == 'name') ? (($asc == 'a')?'/\\ ':'\\/ '):'')."Download</th>";
        echo "<th>".(($order == 'name') ? (($asc == 'a')?'/\\ ':'\\/ '):'')."Delete</th>";
        echo "</tr>";

        foreach($tab_file as $elem) 
        {
            if (assocExt($elem['ext']) <> 'inconnu')
            {
                echo '<tr>';
                echo '<td>';
                echo '<h5><i class="glyphicon glyphicon-saved text-success"></i>';
                echo ' <input type="hidden" value="'.$_SESSION['opensim_select'].'" name="name_sim">';
                echo '<input type="hidden" value="'.$elem['name'].'" name="name_file">'.$elem['name'].'';
                echo '</h5></td>';
                echo '<td><h5>'.formatSize($elem['size']).'</h5></td>';
                echo '<td><h5><span class="badge">'.date("d-m-Y", $elem['date']).'</span></h5></td>';
                echo '<td><h5><span class="badge">'.date("H:i:s a", $elem['date']).'</span></h5></td>';
                echo '<td><h5>'.assocExt($elem['ext']).'</h5></td>';
                echo '<td>';

                if ($_SESSION['osAutorise'] != '')
                {
                    $osAutorise = explode(";", $_SESSION['osAutorise']);

                    for ($i = 0; $i < count($osAutorise); $i++)
                    {
                        if (INI_Conf_Moteur($_SESSION['opensim_select'], "osAutorise") == $osAutorise[$i])
                        {
                            $moteursOK = "OK";
                            $btnN2 = "";
                        }
                    }
                }

                else {$moteursOK = "NOK";}

                if ($_SESSION['privilege'] >= 3)
                {
                    $action = "inc/download.php?file=".INI_Conf_Moteur($_SESSION['opensim_select'], "address").$elem['name'];
                    echo '<form method="post" action="'.$action.'">';
                    echo '<input type="hidden" value="'.$_SESSION['opensim_select'].'" name="name_sim">';
                    echo '<input type="hidden" value="'.$elem['name'].'" name="name_file">';
                    echo '<button class="btn btn-success" type="submit" value="download" name="cmd" '.$btnN3.'>';
                    echo '<i class="glyphicon glyphicon-download-alt"></i> Download</button>';
                    echo '</form>';
                    echo '<td>';
                    echo '<form method="post" action="">';
                    echo '<input type="hidden" value="'.$_SESSION['opensim_select'].'" name="name_sim">';
                    echo '<input type="hidden" value="'.$elem['name'].'" name="name_file">';
                    echo ' <button class="btn btn-danger" type="submit" value="delete" name="cmd" '.$btnN3.'>';
                    echo '<i class="glyphicon glyphicon-trash"></i> Effacer</button>';
                    echo '</td>';
                    echo '</form>';
                }

                else if ($moteursOK == "OK")
                {
                    echo '<form method="post" action="">';
                    echo '<input type="hidden" value="'.$_SESSION['opensim_select'].'" name="name_sim">';
                    echo '<input type="hidden" value="'.$elem['name'].'" name="name_file">';
                    echo '<button class="btn btn-success" type="submit" value="download" name="cmd" '.$btnN2.'>';
                    echo '<i class="glyphicon glyphicon-download-alt"></i> Download</button>';
                    echo '<td>';
                    echo ' <button class="btn btn-danger" type="submit" value="delete" name="cmd" '.$btnN3.'>';
                    echo '<i class="glyphicon glyphicon-trash"></i> Effacer</button>';
                    echo '</td>';
                    echo '</form>';
                }

                else
                {
                    echo '<form method="post" action="">';
                    echo '<button class="btn btn-success" type="submit" name="cmd" disabled>';
                    echo '<i class="glyphicon glyphicon-download-alt"></i> Download</button>';
                    echo '<td>';
                    echo ' <button class="btn btn-danger" type="submit" name="cmd" disabled>';
                    echo '<i class="glyphicon glyphicon-trash"></i> Effacer</button>';
                    echo '</td>';
                    echo '</form>';
                }
                echo '</td>';
                echo '</tr>';
            }
        }
        echo '</table>';
        closedir($dir);
    }
}

/* Directory List */
function list_dir($base, $cur, $level = 0)
{
    global $PHP_SELF, $order, $asc;

    if ($dir = opendir($base)) 
    {
        $tab = array();

        while($entry = readdir($dir)) 
        {
            if (is_dir($base."/".$entry) && !in_array($entry, array(".", "..")))
            {
                $tab[] = addScheme($entry, $base, 'dir');
            }
        }

        /* tri */
        usort($tab, "cmp_name");
        foreach($tab as $elem) 
        {
            $entry = $elem['name'];
            $file = $base."/".$entry;
            for ($i = 1; $i <= (4 * $level); $i++) {echo "&nbsp;";}
            if ($file == $cur) {echo "<p><i class='glyphicon glyphicon-star'></i> $entry</p>\n";}
            else
            {
                echo "<p><i class='glyphicon glyphicon-star'></i>";
                echo " <a href=\"$PHP_SELF?dir=". rawurlencode($file) ."&order=$order&asc=$asc\">$entry</a></p>\n";
            }
            if (ereg($file."/", $cur."/")) {list_dir($file, $cur, $level + 1);}
        }
        closedir($dir);
    }
}

/* Extract Infos */
function addScheme($entry, $base,$type)
{
    $tab['name'] = $entry;
    $tab['type'] = filetype($base."/".$entry);
    $tab['date'] = filemtime($base."/".$entry);
    $tab['size'] = filesize($base."/".$entry);
    $tab['perms']= fileperms($base."/".$entry);
    $tab['access'] = fileatime($base."/".$entry);
    $exp = explode(".", $entry);
    $tab['ext'] = $exp[count($exp) - 1];
    return $tab;
}

/* Format Size */
function formatSize($s)
{
    $u = array('o', 'Ko', 'Mo', 'Go', 'To');
    $i = 0; $m = 0;
    while($s >= 1) {$m = $s; $s /= 1024; $i++;}
    if (!$i) $i = 1;
    $d = explode(".", $m);
    if ($d[0] != $m) $m = number_format($m, 2, ",", " ");
    return $m." ".$u[$i - 1];
}

/* Formate Type */
function assocType($type) {
    $t = array(
        'fifo'      => "file",
        'char'      => "fichier special en mode caractere",
        'dir'       => "dossier",
        'block'     => "fichier special en mode bloc",
        'link'      => "lien symbolique",
        'file'      => "fichier",
        'unknown'   => "inconnu"
    );
    return $t[$type];
}

/* Description des Extension */
function assocExt($ext)
{
    $e = array(
        ''      => "inconnu",
        'oar'   => "<i class='glyphicon glyphicon-compressed'></i> Archive OAR",
        'iar'   => "<i class='glyphicon glyphicon-compressed'></i> Archive IAR",
        'xml2'  => "Archive OS XML2",
        'jpg'   => "Image JPG",
        'bmp'   => "<i class='glyphicon glyphicon-picture'></i> Image BMP",
        'gz'    => "<i class='glyphicon glyphicon-compressed'></i> Backup GZ",
        'raw'   => "Terrain OS"
    );
    if (in_array($ext, array_keys($e))) return $e[$ext];
    return $e[''];
}

/* */
function cmp_name($a, $b)
{
    global $asc;
    if ($a['name'] == $b['name']) return 0;
    if ($asc == 'a') return ($a['name'] < $b['name']) ? -1 : 1;
    return ($a['name'] > $b['name']) ? -1 : 1;
}

/* */
function cmp_size($a, $b)
{
    global $asc;
    if ($a['size'] == $b['size']) return cmp_name($a, $b);
    if ($asc == 'a') return ($a['size'] < $b['size']) ? -1 : 1;
    return ($a['size'] > $b['size']) ? -1 : 1;
}

/* */
function cmp_date($a, $b)
{
    global $asc;
    if ($a['date'] == $b['date']) return cmp_name($a, $b);
    if ($asc == 'a') return ($a['date'] < $b['date']) ? -1 : 1;
    return ($a['date'] > $b['date']) ? -1 : 1;
}

/* */
function cmp_access($a, $b)
{
    global $asc;
    if ($a['access'] == $b['access']) return cmp_name($a, $b);
    if ($asc == 'a') return ($a['access'] < $b['access']) ? -1 : 1;
    return ($a['access'] > $b['access']) ? -1 : 1;
}

/* */
function cmp_perms($a, $b)
{
    global $asc;
    if ($a['perms'] == $b['perms']) return cmp_name($a, $b);
    if ($asc == 'a') return ($a['perms'] < $b['perms']) ? -1 : 1;
    return ($a['perms'] > $b['perms']) ? -1 : 1;
}

/* */
function cmp_type($a, $b)
{
    global $asc;
    if ($a['type'] == $b['type']) return cmp_name($a, $b);
    if ($asc == 'a') return ($a['type'] < $b['type']) ? -1 : 1;
    return ($a['type'] > $b['type']) ? -1 : 1;
}

/* */
function cmp_ext($a, $b)
{
    global $asc;
    if ($a['ext'] == $b['ext']) return cmp_name($a, $b);
    if ($asc == 'a') return ($a['ext'] < $b['ext']) ? -1 : 1;
    return ($a['ext'] > $b['ext']) ? -1 : 1;
}

/* FORMULAIRES */
/* */
function Rec($text)
{
    $text = trim($text);
    if (get_magic_quotes_gpc() === 1) {$stripslashes = function($txt) {return stripslashes($txt);};}
    else {$stripslashes = function($txt) {return $txt;};}
    $text = $stripslashes($text);
    $text = htmlspecialchars($text, ENT_QUOTES);
    $text = nl2br($text);
    return $text;
}

/* */
function IsEmail($email)
{
    $pattern = "/^([a-z0-9_]|\\-|\\.)+@(([a-z0-9_]|\\-)+\\.)+[a-z]{2,7}$/";
    return (preg_match($pattern, $email)) ? true : false;
}
?>
