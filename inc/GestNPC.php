<?php
define("NULL_KEY", "00000000-0000-0000-0000-000000000000");
$regionUUID = !empty($_SESSION['uuid_region_npc']) ? $_SESSION['uuid_region_npc'] : NULL_KEY;
$sql = "
    SELECT * FROM osnpc_terminals 
    WHERE uuid = '".$regionUUID."'
";
$result = mysqli_query($db, $sql);

if (mysqli_num_rows($result) > 0)
{
    while($row = mysqli_fetch_assoc($result))
    {
        $object_uuid = $row['uuid'];
        $region_name = $row['region'];
        $server_url = $row['server_url'];
        $server_uuid = $row['server_uuid'];
        // echo "object_uuid: " . $object_uuid. " region_name: " . $region_name. "<br />";
    }
}

if (isset($_SESSION['authentification']) && $_SESSION['privilege'] >= 3)
{
    echo '<p class="pull-right">';
    echo '<span class="label label-danger">Espace Securise Niveau '.$_SESSION['privilege'].'</span>';
    echo '</p>';
    echo '<h1>Gestion NPC</h1>';
    echo '<div class="clearfix"></div>';

    $select_npc = !empty($_POST["select_npc"]) ? $_POST["select_npc"] : NULL_KEY;
	$sqlA = "SELECT * FROM `osnpc_terminals` WHERE uuid='".$regionUUID."'" ;
	$reqA = mysqli_query($db, $sqlA) or die('Erreur SQL !<br />'.$sqlA.'<br />'.mysqli_error($db));
	$dataA = mysqli_fetch_assoc($reqA);

    // $object_uuid = $dataA['uuid'];
    $regionName = $dataA['region'];
	$FILE_NPC = $regionName.".txt";	

    if (isset($_POST["parameter"]) && $_POST["parameter"] == "WEB")
    {
        echo '<div class="alert alert-success alert-anim" role="alert">';
        echo "Commande envoyée, merci de patienter avant d'envoyer une nouvelle commande ...";
        echo '</div>';

        if (isset($_POST["eclat"]))	
        {
            if ($_POST["eclat"] == 'eclat1') {EcritureFichier($FILE_NPC, "Gestion_NPC, REZ1, section2, section3, section4, ".$_POST['regionUUID']);}
            if ($_POST["eclat"] == 'eclat2') {EcritureFichier($FILE_NPC, "Gestion_NPC, REZ2, section2, section3, section4, ".$_POST['regionUUID']);}
            if ($_POST["eclat"] == 'eclat3') {EcritureFichier($FILE_NPC, "Gestion_NPC, REZ3, section2, section3, section4, ".$_POST['regionUUID']);}
        }

        if (isset($_POST["CREATE_NPC"]))
        {
            EcritureFichier($FILE_NPC, "Gestion_NPC,CREATE, ".$_POST["select_apparence"].", ".$_POST["firstname_NPC"].";".$_POST["lastname_NPC"].", ".$_POST["coordX"].";".$_POST["coordY"].";".$_POST["coordZ"].", ".$_POST["regionUUID"]);
        }

        if (isset($_POST["STOP_NPC"]))
        {
            EcritureFichier($FILE_NPC,"Gestion_NPC, STOP_ALL, section2, section3, section4, ".$_POST["regionUUID"]);
            $sql ="DELETE FROM `npc` WHERE region='".$_POST["regionName"]."'";
            $req = mysqli_query($db, $sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysqli_error($db));
        }

        if (isset($_POST["REMOVE_NPC"]))
        {
            EcritureFichier($FILE_NPC,"Gestion_NPC,REMOVE_NPC,".$select_npc.",section3,section4,".$_POST["regionUUID"]);
            $sql ="DELETE FROM `npc` WHERE uuid_npc='".$select_npc."'";
            $req = mysqli_query($db, $sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error($db));
        }		

        if (isset($_POST["SAY"]))	{
            $message = str_replace(" ", "_", $_POST["message"]);
            EcritureFichier($FILE_NPC,"Gestion_NPC,SAY,".$select_npc.",".$message.",section4,".$_POST["regionUUID"]);
        }

        if (isset($_POST["SIT"]))
        {
            EcritureFichier($FILE_NPC,"Gestion_NPC,SIT,".$select_npc.",".$_POST["uuid_objet"].",section4,".$_POST["regionUUID"]);
        }

        if (isset($_POST["STAND"]))
        {
            EcritureFichier($FILE_NPC,"Gestion_NPC,STAND,".$_POST["select_sit"].",section3,section4,".$_POST["regionUUID"]);
        }

        if (isset($_POST["ANIMATE"]))
        {
            EcritureFichier($FILE_NPC,"Gestion_NPC,".$_POST["ANIMATE"].",".$select_npc.",".$_POST["select_animation"].",section4,".$_POST["regionUUID"]);
        }

        if (isset($_POST["APPARENCE"]) && $_POST["APPARENCE"] == "APPARENCE_LOAD")
        {
            EcritureFichier($FILE_NPC,"Gestion_NPC,".$_POST["APPARENCE"].",".$select_npc.",".$_POST["select_apparence"].",section4,".$_POST["regionUUID"]);
        }

        if (isset($_POST["APPARENCE"]) && $_POST["APPARENCE"] == "APPARENCE_SAVE")
        {
            EcritureFichier($FILE_NPC,"Gestion_NPC,".$_POST["APPARENCE"].",".$select_npc.",".$_POST["notecard_apparence"].",section4,".$_POST["regionUUID"]);
        }

        if (isset($_POST["MAJ_LISTE"]))
        {
            EcritureFichier($FILE_NPC,"Gestion_NPC,LISTING,section2,section3,section4,".$_POST["regionUUID"]);
        }

        if (isset($_POST["RAZ_LISTE_OBJ"]))
        {
            $sql0 = "DELETE FROM `npc` WHERE region='".$_POST["regionName"]."'" ;
            $req0 = mysqli_query($db, $sql0) or die('Erreur SQL !<br />'.$sql0.'<br />'.mysqli_error($db));
            $sql0 = "DELETE FROM `inventaire` WHERE region='".$_POST["regionName"]."'" ;
            $req0 = mysqli_query($db, $sql0) or die('Erreur SQL !<br />'.$sql0.'<br />'.mysqli_error($db));
            $sql0 = "DELETE FROM `osnpc_terminals` WHERE region='".$_POST["regionName"]."'" ;
            $req0 = mysqli_query($db, $sql0) or die('Erreur SQL !<br />'.$sql0.'<br />'.mysqli_error($db));		
        }
    }

    // Si le region NPC selectionne a change
    if (isset($_POST["region"])){$_SESSION['uuid_region_npc'] = trim($_POST["region"]);}

    $sql0 = "SELECT * FROM `osnpc_terminals`" ;
    $req0 = mysqli_query($db, $sql0) or die('Erreur SQL !<br />'.$sql0.'<br />'.mysqli_error($db));
    $numrow0 = mysqli_num_rows($req0);

    echo '<div class="alert alert-info">';
    echo 'Nombre total de regions avec une boite de NPC\'s: <span class="badge badge-default">'.$numrow0.'</span>';
    echo '<div class="pull-right">Npc Box Selected: <span class="label label-default">'.$regionName.'</span></div>';
    echo '</div>';

    // SELECT BOX
    echo '<form class="form-group" method="post" action="">';	
    echo '<input type="hidden" name="parameter" value="WEB">';
    echo '<div class="form-inline">';
    echo '<label for="region">Select BOX:</label> ';
    echo '<select class="form-control" name="region">';
    while($data = mysqli_fetch_assoc($req0))
    {
        echo '<option value="'.$data["uuid"].'">'.$data["region"].'</option> ';
    }
    echo'</select>';
    echo' <button type="submit" class="btn btn-success"><i class="glyphicon glyphicon-ok"></i> Select</button> ';
    echo '<button type="submit" class="btn btn-info"><i class="glyphicon glyphicon-ok"></i> Ping</button> ';
    echo' <button type="submit" class="btn btn-primary"><i class="glyphicon glyphicon-refresh"></i> Refresh</button> ';
    
    echo '</div>';
    echo'</form>';

    echo LectureFichier($FILE_NPC);	

    echo '<form method="POST" action="">';
    echo '<input type="hidden" name="parameter" value="WEB">';
    echo '<input type="hidden" name="regionName" value="'.$regionName.'">';
    echo '<input type="hidden" name="regionUUID" value="'.$regionUUID.'">';

    echo' <div class="panel panel-default">';
    echo '<div class="panel-heading">Creation NPC</div>';
    echo '<div class="panel-body">';
    echo '<table class="table table-condensed">';
    echo '<tr>';  
    echo '<td><input type="text" class="form-control" name="coordX" placeholder="Position X ..." value=""></td>';
    echo '<td><input type="text" class="form-control" name="coordY" placeholder="Position Y ..." value=""></td>';
    echo '<td><input type="text" class="form-control" name="coordZ" placeholder="Position Z ..." value=""></td>';
    echo '</tr>';
    
    echo '<tr>';
    echo '<td><input type="text" class="form-control" name="firstname_NPC" placeholder="Prenom"></td>';
    echo '<td><input type="text" class="form-control" name="lastname_NPC" placeholder="Nom"></td>';
    echo '<td>';
    echo '<button type="submit" class="btn btn-success btn-block" name="CREATE_NPC" value="CREER_NPC"><i class="glyphicon glyphicon-ok"></i> Créer NPC</button>';
    echo '</td>';
    echo '</tr>';
    echo '</table>';
    echo '</div>';
    echo '</div>';

    echo '<div class="panel panel-default">';
    echo '<div class="panel-heading">Actions NPC</div>';
    echo '<div class="panel-body">';

    echo '<div class="form-inline">';
    echo '<label for="select_npc">Select NPC:</label> ';
    echo '<select class="form-control" name="select_npc">';
    $sql0 = "SELECT * FROM `npc` WHERE region='".$regionName."'" ;
    $req0 = mysqli_query($db, $sql0) or die('Erreur SQL !<br>'.$sql0.'<br>'.mysqli_error($db));

    while ($data0 = mysqli_fetch_assoc($req0)) 
    {
        echo '<option value="'.$data0["uuid_npc"].'">'.$data0["firstname"].' '.$data0["lastname"].'</option> ';
    }
    echo'</select>';
    echo' <button type="submit" class="btn btn-danger" name="REMOVE_NPC" value="REMOVE_NPC"><i class="glyphicon glyphicon-remove-circle"></i> Supprimer NPC</button>';
    echo '</div>';

    echo '<br />';
    echo '<div class="form-inline">';
    echo '<label for="select_apparence">Apparence:</label> ';
    echo '<select class="form-control" name="select_apparence">';
    $sql0 = "SELECT * FROM `inventaire` WHERE (type='apparence' AND uuid_parent='".$regionUUID."')" ;
    $req0 = mysqli_query($db, $sql0) or die('Erreur SQL !<br>'.$sql0.'<br>'.mysqli_error($db));

    while ($data0 = mysqli_fetch_assoc($req0)) 
    {
        echo '<option value="'.$data0["nom"].'">'.$data0["nom"].'</option> ';
    }
    echo'</select>';
    echo' <button type="submit" class="btn btn-success" name="APPARENCE" value="APPARENCE_LOAD"><i class="glyphicon glyphicon-ok"></i> Choisir</button>';
    echo '</div>';

    echo '<br />';
    echo '<div class="btn-group " role="group" aria-label="...">';
    echo '<div class="input-group col-xs-50">';
    echo '<input type="text" class="form-control" name="message" placeholder="Faire parler NPC">';
    echo '<span class="input-group-btn">';
    echo '<button type="submit" class="btn btn-default" value="Alerte General" name="SAY" ><i class="glyphicon glyphicon-bullhorn"></i> Faire parler le NPC</button>';
    echo '</span>';
    echo '</div>';
    echo '</div>';

    echo '<br /><br />';
    echo '<div class="btn-group " role="group" aria-label="...">';
    echo '<div class="input-group col-xs-50">';
    echo '<input type="text" class="form-control" name="uuid_objet" placeholder="UUID de l\'objet to sit ">';
    echo '<span class="input-group-btn">';
    echo '<button type="submit" class="btn btn-default" name="SIT" value="SIT" ><i class="glyphicon glyphicon-play"></i> Faire assoir le NPC</button>';
    echo '<button type="submit" class="btn btn-default" name="STAND" value="STAND" ><i class="glyphicon glyphicon-eject"></i> Faire se lever le NPC</button>';
    echo '</span>';
    echo '</div>';
    echo '</div>';

    echo '<br /><br />';
    echo '<div class="form-inline">';
    echo '<label for="select_npc">Animer le NPC:</label> ';
    echo '<select class="form-control" name="select_animation">';
    $sql0 = "SELECT * FROM `inventaire` WHERE (type='animation' AND uuid_parent='".$regionUUID."')" ;
    $req0 = mysqli_query($db, $sql0) or die('Erreur SQL !<br>'.$sql0.'<br>'.mysqli_error($db));

    while ($data0 = mysqli_fetch_assoc($req0)) 
    {
        echo '<option value="'.$data0["nom"].'">'.$data0["nom"].'</option> ';
    }
    echo'</select>';
    echo' <button type="submit" class="btn btn-default" name="ANIMATE" value="ANIMATE_START"><i class="glyphicon glyphicon-play"></i> Play</button>';
    echo' <button type="submit" class="btn btn-default" name="ANIMATE" value="ANIMATE_STOP"><i class="glyphicon glyphicon-stop"></i> Stop</button>';
    echo '</div>';

    echo '<br />';
    echo '<div class="btn-group" role="group" aria-label="...">';
    echo '<div class="input-group col-xs-50">';
    echo '<input type="text" class="form-control" name="notecard_apparence" placeholder="Libelle de l\'apparence à sauvegarder (sans espace) ...">';
    echo '<span class="input-group-btn">';
    echo '<button type="submit" class="btn btn-default" name="APPARENCE" value="APPARENCE_SAVE" >';
    echo '<i class="glyphicon glyphicon-floppy-saved"></i> Sauver Apparence NPC</button>';
    echo '</span>';
    echo '</div>';
    echo '</div>';

    echo '</div>';
    echo '</div>';

    echo' <div class="panel panel-default">';
    echo '<div class="panel-heading">Gestion BOX</div>';
    echo '<div class="panel-body">';
    echo '<p>Inworld script: ';
    echo '<a class="btn btn-default" href="docs/lsl/Osmw Npc Terminal v0.1.lsl" target=_blank>Osmw Npc Terminal v0.1.lsl</a>';
    echo '</p>';
    echo '<p>Actions: ';
    echo '<button type="submit" class="btn btn-danger" name="MAJ_LISTE" value="MAJ_LISTE"><i class="glyphicon glyphicon-wrench"></i> Lire Box</button> ';
    echo '<button type="submit" class="btn btn-danger" name="STOP_NPC" value="STOP_NPC"><i class="glyphicon glyphicon-trash"></i> Effacer NPC</button> ';
    echo '<button type="submit" class="btn btn-danger" name="RAZ_LISTE_OBJ" value="RAZ_LISTE_OBJ"><i class="glyphicon glyphicon-trash"></i> Effacer Box</button> ';
    echo '</p>';
    echo '<p><span title="Rez un objet depuis l\'inventaire de la box NPC">Extras:</span> ';
    echo '<button type="submit" class="btn btn-default" name="eclat" value="eclat1"><i class="glyphicon glyphicon-ok"></i> Particule 1 </button> ';
    echo '<button type="submit" class="btn btn-default" name="eclat" value="eclat2"><i class="glyphicon glyphicon-ok"></i> Particule 2 </button> ';
    echo '<button type="submit" class="btn btn-default" name="eclat" value="eclat3"><i class="glyphicon glyphicon-ok"></i> Particule 3 </button> ';
    echo '</p>';
    echo '</div>';
    echo '</div>';
    echo '</form>';
    mysqli_close($db);
}

else
{
    mysqli_close($db);
    header('Location: index.php');
}
?>
