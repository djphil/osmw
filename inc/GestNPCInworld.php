<?php include_once("config.php"); ?>
<?php include_once("mysqli.php"); ?>
<?php include_once("fonctions.php"); ?>
<?php
if (isset($_POST['server_url']) AND isset($_POST['server_uuid']) AND isset($_POST['object_uuid']))
{
    $server_url = $_POST['server_url'];
    $server_uuid = $_POST['server_uuid'];
    $object_uuid = $_POST['object_uuid'];
    
    $sql = "
        UPDATE osnpc_terminals 
        SET server_url = '".$server_url."',
            server_uuid = '".$server_uuid."'
        WHERE uuid = '".$object_uuid."'
    ";
    $req = mysqli_query($db, $sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysqli_error($db));
    echo "[PONG] ".$req;
}

if (isset($_POST['parameter']))
{
    $parameter = mysqli_real_escape_string($db, $_POST['parameter']);

    // ENREGISTREMENT DU GESTIONNAIRE DE NPC
	if ($parameter == "REG_WEB_NPC" )
	{
        // Si il existe on le met a jour, sinon on l'insert ...
        $uuid = mysqli_real_escape_string($db, $_POST['uuid']);
        $region = mysqli_real_escape_string($db, $_POST['region']);

        $sql = "
            SELECT uuid 
            FROM osnpc_terminals 
            WHERE uuid = '".$uuid."'
        ";

        $req = mysqli_query($db, $sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysqli_error($db));

        if (mysqli_num_rows($req) == 0)
        {
            $sql = "
                INSERT INTO osnpc_terminals (uuid, region) 
                VALUES ('".$uuid."', '".$region."');
            ";
            $req = mysqli_query($db, $sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysqli_error($db));
            echo "Info_NPC, Enregistrement de votre NPC dans la base de données effectué avec succès ...";
        }

        else
        {
            $sql = "
                UPDATE osnpc_terminals 
                SET region = '".$region."'
                WHERE uuid = '".$uuid."'
            ";
            $req = mysqli_query($db, $sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysqli_error($db));
            echo "Info_NPC, Mise à jour de votre NPC dans la base de données effectué avec succès ...";
        }

		$FILE_NPC_TIMER = $region.".txt";

        if (!file_exists($FILE_NPC_TIMER))
        {
            $f = fopen($FILE_NPC_TIMER, "x+");
            fclose($f);
            
            echo "Info_NPC, Mise a jour de votre NPC dans le fichier ".$FILE_NPC_TIMER." effectué avec succès ...";
        }
        echo "Info_NPC, Le fichier ".$FILE_NPC_TIMER." existe déjà ...";
        echo "Info_NPC, Votre Gestionnaire de NPC est opérationnel, Bonne utilisation ...";
	}

    // ENREGISTREMENT DU NPC CREER
    if ($parameter == "NPC_CREATE")
	{
        $uuid = mysqli_real_escape_string($db, $_POST['uuid']);
        $region = mysqli_real_escape_string($db, $_POST['region']);
        $firstname = mysqli_real_escape_string($db, $_POST['firstname']);
        $lastname = mysqli_real_escape_string($db, $_POST['lastname']);
        $full = $firstname + " " + $lastname;
        $sql = "
        INSERT INTO `npc` (`uuid_npc`, `firstname`, `lastname`, `region`) 
        VALUES ('".$uuid."', '".$firstname."', '".$lastname."', '".$region."');
        ";
        $req = mysqli_query($db, $sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysqli_error($db));
        echo 'Info_NPC, Votre NPC '.$full.' est enregistré et opérationnel, Bonne Utilisation ...';
    }

    // AFFICHAGE DES NPC CREER
    if ($parameter == "LISTE_NPC")
    {
        // $uuid = mysqli_real_escape_string($db, $_POST['uuid']);
        $region = mysqli_real_escape_string($db, $_POST['region']);

        $sql0 = "
            SELECT * 
            FROM `npc` 
            WHERE region = '".$region."'
        ";
        $req0 = mysqli_query($db, $sql0) or die('Erreur SQL !<br />'.$sql0.'<br />'.mysqli_error($db));
        $numrow0 = mysqli_num_rows($req0);
        $listeNPC = "";

        while ($data0 = mysqli_fetch_assoc($req0)) 
        {
            $listeNPC = $listeNPC.$data0['uuid_npc']." -> ".$data0['firstname']." ".$data0['lastname'].";";  
        }
        echo 'Info_NPC, Liste NPC, '.$numrow0.' NPCs, '.$listeNPC.',';
    }

        // ENREGISTREMENT DES OBJETS / APPARENCE / ANIMATION
    if ($parameter == "LISTE_OBJ")
    {
        $uuid = mysqli_real_escape_string($db, $_POST['uuid']);
        $datas = mysqli_real_escape_string($db, $_POST['datas']);
        $region = mysqli_real_escape_string($db, $_POST['region']);

        // supprimer liste de l'objet appelant
        $sql = "DELETE FROM `inventaire` WHERE `uuid_parent` = '".$uuid."';";
        $req = mysqli_query($db, $sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysqli_error($db));

        // ajouter la liste dans la bdd
        $listinventaire = explode(";", $datas);
        $n = count($listinventaire);

        for ($i = 0; $i <= $n; $i++) 
        {
            if ($listinventaire[$i] == "apparence")
            {
                $nb_apparence = $listinventaire[$i+1] ;
                for ($j = 1; $j <= $nb_apparence; $j++) 
                {
                    $sql = "
                        INSERT INTO `inventaire` (`uuid_parent`, `type`, `nom`, `region`) 
                        VALUES ('".$_POST['uuid']."', 'apparence', '".$listinventaire[$i+$j+1]."', '".$_POST['region']."');
                    ";
                    $req = mysqli_query($db, $sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysqli_error($db));
                }
            }
            if ($listinventaire[$i] == "animation")
            {
                $nb_animation = $listinventaire[$i+1] ;
                for ($j = 1; $j <= $nb_animation; $j++) 
                {
                    $sql = "
                        INSERT INTO `inventaire` (`uuid_parent`, `type`, `nom`, `region`) 
                        VALUES ('".$_POST['uuid']."', 'animation', '".$listinventaire[$i+$j+1]."', '".$_POST['region']."');
                    ";
                    $req = mysqli_query($db, $sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysqli_error($db));
                }
            }
        }
        echo 'Info_NPC, Inventaire, UUID Objet: '.$_POST['uuid'].', Inventaire Enregistré;,';
    }

    // APPEL INWORLD + LECTURE ORDRE + EFFACEMENT DU DERNIER ORDRE
    if ($parameter == "TIMER")
    {
        // $uuid = mysqli_real_escape_string($db, $_POST['uuid']);
        $region = mysqli_real_escape_string($db, $_POST['region']);
        $FILE_NPC_TIMER = $region.".txt";

        if (file_exists($FILE_NPC_TIMER))
        {
            $monfichier = fopen($FILE_NPC_TIMER, 'r+');
            $ligne = fgets($monfichier);
            fclose($monfichier);
            $monfichier = fopen($FILE_NPC_TIMER, 'w+');
            fseek($monfichier, 0);  // On remet le curseur au début du fichier
            fputs($monfichier, ""); // On écrit le nouveau nombre de pages vues
            fclose($monfichier);
            echo $ligne;
        }
    }
}
mysqli_close($db);
?>
