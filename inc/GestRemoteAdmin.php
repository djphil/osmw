<?php 
// include_once 'inc/functions.php';
// include_once 'inc/xmlrpc.php';

if (isset($_SESSION['authentification']) && $_SESSION['privilege'] >= 3)
{
    echo '<p class="pull-right"><span class="label label-danger">Espace Securise Niveau '.$_SESSION['privilege'].'</span></p>';
    echo '<h1>Gestion Remote Admin</h1>';
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

    $channel="d8e93046-dcfc-4a65-81ab-25554eee893f";

    if (isset($_POST['cmd']) && $_POST['cmd'] == "Envoyer")
    {
        $myRemoteAdmin = new RemoteAdmin('localhost', 12000, '***'); // unknown
        $params = array(
            'Channel' => 'string', 
            'IntValue' => 'int', 
            'StringValue' => 'string',
            'Channel' => $channel, 
            'IntValue' => (int)$POST_int,
            'StringValue' => $POST_string
        );
        // $myRemoteAdmin->SendCommand('llRemoteData', $params);
        $myRemoteAdmin->SendCommand('admin_console_command', $params);

        if ($retour === FALSE)
        {
            echo '<p>Erreur Remote Admin ...</p>';
        }
    }

    echo '<form class="form-group" method="post" action="">';
    echo '<table class="table table-hover">';
    echo '<tr>';
    echo '<th>Action</th>';
    echo '<th>Value</th>';
    echo '</tr>';

    echo '<tr>';
    echo '<td><h5>(Listen) int Value:</h5></td>';
    echo '<td><input class="form-control" type="text" name="intvalue"></td>';
    echo '</tr>';

    echo '<tr>';
    echo '<td><h5>(Commande) string Value:</h5></td>';
    echo '<td><input class="form-control" type="text" name="stringvalue"></td>';
    echo '</tr>';

    echo '</table>';
    echo '<button class="btn btn-success" type="submit" name="cmd" value="Envoyer">';
    echo '<i class="glyphicon glyphicon-ok"></i> Envoyer';
    echo '</button>';
    echo '</form>';
}
else {header('Location: index.php');}
?>
