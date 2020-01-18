<?php
session_start();
header('Content-Type: text/html; charset=utf-8');
ini_set('magic_quotes_gpc', 0);
ini_set('display_errors', 1);
error_reporting(E_ALL);

include_once('inc/configcheck.php'); 
include_once('inc/config.php');
include_once('inc/mysqli.php');
include_once('inc/fonctions.php');
include_once('inc/radmin.php');
if ($themes) {include_once ('inc/themes.php');}

$a = !empty($_GET['a']) ? $_GET['a'] : 1;
if ($a == 'logout')
{
    $_SESSION = array();
    session_destroy();
    session_unset();
    header('Location: index.php');
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>OpenSimulator Manager Web v6.0</title>
    <meta name="description" content="">
    <meta name="author" content="Philippe Lemaire (djphil)">
    <link rel="icon" href="img/favicon.ico">
    <link rel="author" href="inc/humans.txt" />
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" media="all" id="css" href="<?php echo $url; ?>" />
    <?php if (strpos($url, 'default.css')): ?>
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <?php endif; ?>
    <link rel="stylesheet" href="css/gh-fork-ribbon.min.css">
    <link rel="stylesheet" href="css/btn3d.css">
    <link rel="stylesheet" href="css/osmw.css">
</head>
<body>

<div class="container">
<div class="github-fork-ribbon-wrapper left">
    <div class="github-fork-ribbon">
        <a href="<?php echo $github_url; ?>" target="_blank"><?php echo $github_txt; ?></a>
    </div>
</div>

<?php
if ($recaptcha && !empty($_POST["g-recaptcha"]))
{
    include 'inc/recaptcha.php';
    $response = null;
    $error = null;
    $reCaptcha = new ReCaptcha($secret);

    if ($_POST["g-recaptcha-response"])
    {
        $response = $reCaptcha->verifyResponse(
            $_SERVER["REMOTE_ADDR"],
            $_POST["g-recaptcha-response"]
        );
    }

    if ($response != null && $response->success)
    {
        // echo '<div id="alert" class="alert alert-success alert-dismissible" role="alert">Recaptcha success ...</div>';
    }

    else
    {
        echo '<div id="alert" class="alert alert-danger alert-dismissible" role="alert">Recaptcha failed!</div>';
        $_SESSION = array();
        session_unset();
    }
}

if (isset($_POST['firstname']) && isset($_POST['lastname']) && isset($_POST['pass']))
{
    $_SESSION['login'] = $_POST['firstname'].' '. $_POST['lastname'];
    $auth = false;
    $passwordHash = sha1($_POST['pass']);

    $sql = 'SELECT * FROM users';
    $req = mysqli_query($db, $sql) or die('Erreur SQL !<p>'.$sql.'</p>'.mysqli_error($db));

    while($data = mysqli_fetch_assoc($req))
    {
        if ($data['firstname'] == $_POST['firstname'] and $data['lastname'] == $_POST['lastname'] and $data['password'] == $passwordHash)
        {
            $auth = true;
            $_SESSION['privilege'] = $data['privilege'];
            $_SESSION['osAutorise'] = $data['osAutorise'];
            $_SESSION['authentification'] = true;
            break;
        }
    }

    if ($auth == false)
    {
        echo '<div class="alert alert-danger alert-anim">Vous ne pouvez pas acceder a cette page</div>';
        header('Location: index.php?erreur=login');
    }

    else
    {
        $db = mysqli_connect($hostnameBDD, $userBDD, $passBDD, $database);
        if (mysqli_connect_errno()) {echo "Failed to connect to MySQL: " . mysqli_connect_error();}

        $sql = 'SELECT * FROM moteurs';
        $req = mysqli_query($db, $sql) or die('Erreur SQL!<p>'.$sql.'</p>'.mysqli_error($db));

        while($data = mysqli_fetch_assoc($req))
        {
            $_SESSION['opensim_select'] = $data['id_os'];
            break;
        }
    }
    // mysqli_close($db);
}

if (isset($_SESSION['privilege']))
{
    $btnN1 = "disabled"; 
    $btnN2 = "disabled"; 
    $btnN3 = "disabled";
    if ($_SESSION['privilege'] == 4) {$btnN1 = ""; $btnN2 = ""; $btnN3 = "";}   // Niv 4 Super Administrateur
    if ($_SESSION['privilege'] == 3) {$btnN1 = ""; $btnN2 = ""; $btnN3 = "";}   // Niv 3 Administrateur
    if ($_SESSION['privilege'] == 2) {$btnN1 = ""; $btnN2 = "";}                // Niv 2 Gestionnaire (sauvegarde)
    if ($_SESSION['privilege'] == 1) {$btnN1 = "";}                             // Niv 1 Utilisateurs
}
?>

<!--Themes -->
<div class="options">
<?php if ($themes && isset($_SESSION['authentification'])): ?>
<?php if (isset($_GET['style']) && !empty($_GET['style'])) {$theme = $_GET['style'];} else $theme = "Themes"; ?>
<div class="btn-group">
    <button type="button" class="btn btn-primary btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="glyphicon glyphicon-leaf"></i> <?php echo $theme; ?>
        <span class="caret"></span>
    </button>
    <ul class="dropdown-menu">
        <li><a href="index.php?style=default"><i class="glyphicon glyphicon-leaf"></i> default</a></i>
        <li><a href="index.php?style=amelia"><i class="glyphicon glyphicon-leaf"></i> amelia</a></i>
        <li><a href="index.php?style=cerulean"><i class="glyphicon glyphicon-leaf"></i> cerulean</a></i>
        <li><a href="index.php?style=cosmo"><i class="glyphicon glyphicon-leaf"></i> cosmo</a></i>
        <li><a href="index.php?style=cyborg"><i class="glyphicon glyphicon-leaf"></i> cyborg</a></i>
        <li><a href="index.php?style=darkly"><i class="glyphicon glyphicon-leaf"></i> darkly</a></i>
        <li><a href="index.php?style=flatly"><i class="glyphicon glyphicon-leaf"></i> flatly</a></i>
        <li><a href="index.php?style=freelancer"><i class="glyphicon glyphicon-leaf"></i> freelancer</a></i>
        <li><a href="index.php?style=journal"><i class="glyphicon glyphicon-leaf"></i> journal</a></i>
        <li><a href="index.php?style=lumen"><i class="glyphicon glyphicon-leaf"></i> lumen</a></i>
        <li><a href="index.php?style=paper"><i class="glyphicon glyphicon-leaf"></i> paper</a></i>
        <li><a href="index.php?style=readable"><i class="glyphicon glyphicon-leaf"></i> readable</a></i>		
        <li><a href="index.php?style=sandstone"><i class="glyphicon glyphicon-leaf"></i> sandstone</a></i>
        <li><a href="index.php?style=simplex"><i class="glyphicon glyphicon-leaf"></i> simplex</a></i>
        <li><a href="index.php?style=slate"><i class="glyphicon glyphicon-leaf"></i> slate</a></i>
        <li><a href="index.php?style=spacelab"><i class="glyphicon glyphicon-leaf"></i> spacelab</a></i>
        <li><a href="index.php?style=superhero"><i class="glyphicon glyphicon-leaf"></i> superhero</a></i>
        <li><a href="index.php?style=united"><i class="glyphicon glyphicon-leaf"></i> united</a></i>
        <li><a href="index.php?style=yety"><i class="glyphicon glyphicon-leaf"></i> yety</a></i>
    </ul>
</div>
<?php endif; ?>

<?php
if ($translator && isset($_SESSION['authentification']))
{
    require_once ('inc/translator.php');
    echo('<div class="pull-right">');
    include_once("inc/flags.php");
    echo('</div>');
}
?>
</div>

<div class="clearfix"></div>

<?php
if (isset($_SESSION['authentification']))
{
    if (isset($_POST['OSSelect']))
    {
        $_SESSION['opensim_select'] = trim($_POST['OSSelect']);
    }

    include_once('inc/navbar.php');
    
    if (isset($_GET['a']) && !empty($_GET['a']))
    {
        $a = $_GET['a'];
        if ($a == "1") {include('inc/GestSims.php');}               // # Gestion sim v6.0
        if ($a == "2") {include('inc/GestBackup.php');}             // # Gestion backups v6.0
        if ($a == "3") {include('inc/GestTerrain.php');}            // # Gestion Terrain v6.0
        if ($a == "4") {include('inc/GestInventaire.php');}         // # Exporter un inventaire v6.0
        if ($a == "5") {include('inc/GestOpensim.php');}            // admin // # Edition des fichiers de conf Opensim propre au moteur V5
        if ($a == "6") {include('inc/GestRegion.php');}             // admin // # Gestion des Regions par moteur
        if ($a == "7") {include('inc/GestLog.php');}                // # Gestion du Log v6.0
        // if ($a == "8") {include('inc/GestAdminServ.php');}       // admin // # Gestion du serveur (Linux Only)
        if ($a == "9") {include('inc/GestContact.php');}            // # Helpdesk Utilisateur V4
        if ($a == "10") {include('inc/GestDirectory.php');}         // # Gestion des Fichiers v6.0
        // if ($a == "11") {include('inc/GestLoad.php');}           // # Chargement de OAR v6.0
        // if ($a == "12") {include('inc/GestIdentite.php');}       // admin // # Connection a Admin Grille OSMW V4
        // if ($a == "13") {include('inc/GEstHelp.php');}           // # Aide V4 (Obsolet)
        if ($a == "14") {include('inc/GestAbout.php');}             // # Les remerciements v6.0
        if ($a == "15") {include('inc/GestUsers.php');}             // admin // # Gestion des utilisateurs v6.0
        // if ($a == "16") {include('inc/GestBackup.php');}         // admin // # Gestion des sauvegardes v6.0
        if ($a == "17") {include('inc/GestMoteur.php');}            // admin // # Gestion des moteurs v6.0
        if ($a == "18") {include('inc/GestConfig.php');}            // admin // # Configuration de OSMW v6.0
        // if ($a == "19") {include('inc/GestRemoteAdmin.php');}    // admin //	# Commande remote admin personalise (TODO)
        // if ($a == "20") {include('inc/GestTransfert.php');}      // admin // # Permet le transfert de fichier 
        if ($a == "21") {include('inc/GestHypergrid.php');}         // # Gestion des liens Hypergrid
        if ($a == "22") {include('inc/GestMap.php');}               // # Basic Worldmap
        if ($a == "23") {include('inc/GestNPC.php');}               // # Gestion des NPC's 'Alpha)
    }

    else
    {
        echo '<p class="pull-right"><span class="label label-danger">Espace Sécurisé Niveau '.$_SESSION['privilege'].'</span></p>';
        echo '<h1>Home</h1>';
        echo '<div class="clearfix"></div>';

        if ($_SESSION['osAutorise'] != '')
        {
            $osAutorise = explode(";", $_SESSION['osAutorise']);
            for($i = 0; $i < count($osAutorise); $i++)
            {
                if (INI_Conf_Moteur($_SESSION['opensim_select'], "osAutorise") == $osAutorise[$i]) {$moteursOK = "OK";}
            }
        }

        $sql = 'SELECT * FROM moteurs';
        $req = mysqli_query($db, $sql) or die('Erreur SQL !<p>'.$sql.'</p>'.mysqli_error($db));
        echo '<a class="btn btn-primary pull-right" href="secondlife://'.$_SESSION['opensim_select'].'/128/128/25"><i class="glyphicon glyphicon-plane"></i> Teleport</a>';

        echo '<p>Simulateur selectionne';
        echo ' <strong class="label label-info">'.$_SESSION['opensim_select'].' '.INI_Conf_Moteur($_SESSION['opensim_select'], "version").'</strong>';
        echo '</p>';

        echo '<form class="form-group" method="post" action="">';
        echo '<div class="form-inline">';
        echo '<label for="OSSelect"></label>Select Simulator ';
        echo '<select class="form-control" name="OSSelect">';
        echo '<option selected disabled>Select a simulator ...</option>';

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
        ?>

        <?php if(isset($_SESSION['flash'])): ?>
            <?php foreach($_SESSION['flash'] as $type => $message): ?>
                <div class="alert alert-<?php echo $type; ?> alert-anim">
                    <?php echo $message; ?>
                </div>
            <?php endforeach; ?>
            <?php unset($_SESSION['flash']); ?>
        <?php endif; ?>

        <div class="list-group">
            <a class="list-group-item" href="?a=1">Gestion des Régions</a>
            <a class="list-group-item" href="?a=10">Gestion des Sauvegardes</a>
            <a class="list-group-item" href="?a=7">Gestion des Logs</a>
            <a class="list-group-item" href="?a=2">Sauvegarder une Région</a>
            <a class="list-group-item" href="?a=3">Sauvegarder un Terrain</a>
            <a class="list-group-item" href="?a=4">Sauvegarder un Inventaire</a>
            <a class="list-group-item" href="?a=21">Raccourcis Hypergrid</a>
            <a class="list-group-item" href="?a=22">Afficher la Map</a>
        </div>

        <?php 
        mysqli_close($db);
    }
}

else { ?>

<div class="text-center">
    <h1 class="title">OSMW <span><?php echo INI_Conf('VersionOSMW', 'VersionOSMW'); ?></span></h1>
</div>

<form class="form-signin" action="index.php" method="post" name="connect">
    <?php if (isset($_GET['erreur']) && ($_GET['erreur'] == "login")): ?>
    <div class="alert alert-danger alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        Echec d'authentification: <strong>login ou mot de passe incorrect ...</strong>
    </div>
    <?php endif; ?>

    <?php if (isset($_GET['erreur']) && ($_GET['erreur'] == "delog")): ?>
    <div class="alert alert-success alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        Déconnexion réussie, à bientôt ...
    </div>
    <?php endif; ?>

    <?php if (isset($_GET['erreur']) && ($_GET['erreur'] == "intru")): ?>
    <!-- Affiche l'erreur -->
    <div class="alert alert-danger alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        Echec d'authentification: Aucune session ouverte ou droits insuffisants pour afficher cette page ...</strong>
    </div>
    <div class="alert alert-success alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        Déconnexion réussie, à bientôt ...</strong>
    </div>
    <?php endif; ?>

    <img style="height:128px;" class="img-thumbnail img-circle center-block" alt="Logo Server" src="img/logo.png">
    <br />
    <label for="firstname" class="sr-only">Firstname</label>
        <input type="text" id="firstname" name="firstname" class="form-control" placeholder="First Name" required autofocus>
    <label for="lastname" class="sr-only">Lastname</label>
        <input type="text" id="lastname" name="lastname" class="form-control" placeholder="Last Name" required>
    <label for="pass" class="sr-only">Password</label>
        <input type="password" id="pass" name="pass" class="form-control" placeholder="Password" required>

    <?php
    if ($recaptcha)
    {
        echo '<div class="g-recaptcha" data-sitekey="'.$siteKey.'"></div>';
        echo '<script type="text/javascript" src="https://www.google.com/recaptcha/api.js?hl='.$lang.'"></script>';
    }
    ?>

    <div class="checkbox">
        <label><input type="checkbox" name="remember" value="1" id="Remember"> Remember me</label>
    </div>

    <button class="btn btn-primary btn-block" type="submit">
        <span class="glyphicon glyphicon-log-in" aria-hidden="true"></span> Authentification
    </button>
</form>
<?php } ?>

<div class="clearfix"></div>

<footer class="footer">
    <p class="text-center">
        OpenSimulator Manager Web <?php echo date('Y'); ?> v<?php echo INI_Conf('VersionOSMW', 'VersionOSMW'); ?> by djphil (CC-BY-NC-SA 4.0)
    </p>
</footer>
</div>

<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/pdf.js"></script>

<!-- FADE ALERT -->
<script>window.setTimeout(function() {$(".alert-anim").fadeTo(500, 0).slideUp(500, function() {$(this).remove();});}, 3000);</script>
<script>$(document).ready(function(){$('[data-toggle="popover"]').popover();});</script>
<script>$(document).ready(function(){$('.fade-in').hide().fadeIn();});</script>
<script>$(function () {$('[data-toggle="tooltip"]').tooltip();});</script>
<!--<script>.modal.in .modal-dialog {transform: none;}</script>-->

<!-- PDF MODAL -->
<script>
$(function(){    
    $('.view-pdf').on('click',function() {
        var pdf_link = $(this).attr('href');
        var iframe = '<div class="iframe-container"><iframe src="'+pdf_link+'"></iframe></div>'
        $.createModal({
            title:'Aide',
            message: iframe,
            closeButton:true,
            scrollable:false
        });
        return false;
    });    
})
</script>

</body>
</html>
