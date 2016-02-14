<?php
require_once ('inc/config.php');
require_once ('inc/fonctions.php');
require_once ('inc/radmin.php');
if ($themes) {require_once ('./inc/themes.php');}

if ($_GET['a'] == 'logout')
{
    $_SESSION = array();
    session_destroy();
    session_unset();
    header('Location: index.php');
}
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="img/favicon.ico">
    <title>Open Simulator Web Manager</title>
    <link rel="stylesheet" href="css/bootstrap.min.css" type="text/css" />
    <link rel="stylesheet" media="all" type="text/css" id="css" href="<?php echo $url; ?>" />
    <link rel="stylesheet" href="css/btn3d.css" type="text/css" />
    <link rel="stylesheet" href="css/login.css" type="text/css" />
    <link rel="stylesheet" href="css/custom.css" type="text/css" />

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <script>
        $('#myTab a').click(function (e) {e.preventDefault(); $(this).tab('show');})
        $('#myTab a[href="#profile"]').tab('show')
        $('#myTab a:first').tab('show')
        $('#myTab a:last').tab('show')
        $('#myTab li:eq(2) a').tab('show')
    </script>
</head>
<body>

<div class="container">

<?php
// *********
// RECAPTCHA
// *********
if ($recaptcha && $_POST["g-recaptcha"])
{
    include 'inc/recaptcha.php';

	// The response from reCAPTCHA
	$resp = null;
	
	// The error code from reCAPTCHA, if any
	$error = null;
	$reCaptcha = new ReCaptcha($secret);
	
	// Was there a reCAPTCHA response?
	if ($_POST["g-recaptcha-response"])
	{
	    $resp = $reCaptcha->verifyResponse(
            $_SERVER["REMOTE_ADDR"],
            $_POST["g-recaptcha-response"]
		);
	}
	
	// If success
	if ($resp != null && $resp->success)
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

// *********************************************************
// IDENTIFICATION ET INITIALISATION Variable OPENSIM[SELECT]
// *********************************************************
if (isset($_POST['firstname']) && isset($_POST['lastname']) && isset($_POST['pass']))
{
	$_SESSION['login'] = $_POST['firstname'].' '. $_POST['lastname'];
    $auth = false;
	$passwordHash = sha1($_POST['pass']);

	// on se connecte a MySQL
	$db = mysql_connect($hostnameBDD, $userBDD, $passBDD);
	mysql_select_db($database,$db);

	$sql = 'SELECT * FROM users';
	$req = mysql_query($sql) or die('Erreur SQL !<p>'.$sql.'</p>'.mysql_error());

	while($data = mysql_fetch_assoc($req))
	{
		if ($data['firstname'] == $_POST['firstname'] 
        and $data['lastname'] == $_POST['lastname'] 
        and $data['password'] == $passwordHash)
		{
			$auth = true;
			$_SESSION['privilege'] = $data['privilege'];
			$_SESSION['osAutorise'] = $data['osAutorise'];
			$_SESSION['authentification']=true;
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
        // echo '<p>Bienvenue sur la page administration du site.</p>';
		// on se connecte a MySQL
		$db = mysql_connect($hostnameBDD, $userBDD, $passBDD);
		mysql_select_db($database,$db);
		$sql = 'SELECT * FROM moteurs';
		$req = mysql_query($sql) or die('Erreur SQL!<p>'.$sql.'</p>'.mysql_error());
		while($data = mysql_fetch_assoc($req))
        {
            $_SESSION['opensim_select'] = $data['id_os'];
            break;
        }
    }
	mysql_close();
}

// Affichage variable post
// foreach($_POST as $key => $val) echo '$_POST["'.$key.'"]='.$val.'<br />';
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

<?php if ($themes && isset($_SESSION['authentification'])): ?>
<!--Themes -->
<?php if ($_GET['style']) {$theme = $_GET['style'];} else $theme = "Themes"; ?>
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
    require_once ('./inc/translator.php');
	echo('<div class="pull-right">');
	include_once("./inc/flags.php");
	echo('</div>');
}
?>

<div class="clearfix"></div>

<?php
// **********************
// PAGE EN ACCES SECURISE
// **********************
// Verification sur la session authentification 
if (isset($_SESSION['authentification']))
{
	// Si le moteur selectionne a change
	if (isset($_POST['OSSelect']))
    {
        $_SESSION['opensim_select'] = trim($_POST['OSSelect']);
    }

	// DISPLAY BOOTSTRAP MENU
    // echo '<p>Bienvenue <strong>'.$_SESSION['login'].'</strong>';
	include_once './inc/navbar.php';
?>

<?php
    if ($_GET['a'])
    {
        $a = $_GET['a'];
        /* index.php v6.0 */
        if ($a == "1") {include('inc/GestSims.php');}           // # Gestion sim v6.0
        if ($a == "2") {include('inc/GestSaveRestore.php');}    // # Gestion backup sim v6.0
        if ($a == "3") {include('inc/GestTerrain.php');}        // # Gestion Terrain v6.0
        if ($a == "4") {include('inc/GestInventaire.php');}     // # Exporter un inventaire v6.0
        if ($a == "5") {include('inc/GestOpensim.php');}        // admin // # Edition des fichiers de conf Opensim propre au moteur V5
        if ($a == "6") {include('inc/GestRegion.php');}         // admin // # Gestion des Regions par moteur
        if ($a == "7") {include('inc/GestLog.php');}            // # Gestion du Log v6.0
        // if ($a == "8") {include('inc/GestAdminServ.php');}   // admin // # Gestion du serveur
        if ($a == "9") {include('inc/contact.php');}            // # Helpdesk Utilisateur v4.0
        if ($a == "10") {include('inc/GestDirectory.php');}     // # Gestion des Fichiers v6.0
        // if ($a == "11") {include('inc/GestLoad.php');}       // # Chargement de OAR v6.0
        // if ($a == "12") {include('inc/GestIdentite.php');}   // admin // # Connection a Admin Grille OSMW V4
        if ($a == "13") {include('inc/help.php');}              // # Aide V4
        if ($a == "14") {include('inc/about.php');}             // # Les remerciements v6.0
        if ($a == "15") {include('inc/GestUsers.php');}         // admin // # Gestion des utilisateurs v6.0
        // if ($a == "16") {include('inc/GestBackup.php');}     // admin // # Gestion des sauvegardes v6.0
        if ($a == "17") {include('inc/GestMoteur.php');}        // admin // # Gestion des moteurs v6.0
        if ($a == "18") {include('inc/GestConfig.php');}        // admin // # Configuration de OSMW v6.0
        // if ($a == "19") {include('inc/GestRemoteAdmin.php');}// admin //	# Commande remote admin personalise --- a faire
        // if ($a == "20") {include('inc/GestTransfert.php');}  // admin // # Permet le transfert de fichier 
        if ($a == "21") {include('inc/GestHypergrid.php');}     // # Gestion des liens Hypergrid
        if ($a == "22") {include('inc/map.php');}               // # Map

        if ($a == "logout")
        {
            session_start();
            $_SESSION = array();
            session_unset();
            header('Location: index.php');
        }
	}

    else
	{
        // *******************
        // AFFICHAGE PRINCIPAL
        // *******************
        echo '<p class="pull-right"><span class="label label-danger">Espace Securise Niveau '.$_SESSION['privilege'].'</span></p>';
        echo '<h1>Home</h1>';
        echo '<div class="clearfix"></div>';

        // *** Formulaire de choix du moteur a selectionne ***
        // *** Si NIV 1 - Verification Moteur Autorise *******
        if($_SESSION['osAutorise'] != '')
        {
            $osAutorise = explode(";", $_SESSION['osAutorise']);
            //echo count($osAutorise);
            //echo $_SESSION['osAutorise'];
            for($i = 0; $i < count($osAutorise); $i++)
            {
                if (INI_Conf_Moteur($_SESSION['opensim_select'], "osAutorise") == $osAutorise[$i]) {$moteursOK = "OK";}
            }
        }
        
        // ****************************
        // PARCOURS DE TOUS LES MOTEURS
        // ****************************
        // Formulaire de choix du moteur a selectionne
        // on se connecte a MySQL
        $db = mysql_connect($hostnameBDD, $userBDD, $passBDD);
        mysql_select_db($database,$db);
        
        $sql = 'SELECT * FROM moteurs';
        $req = mysql_query($sql) or die('Erreur SQL !<p>'.$sql.'</p>'.mysql_error());
        echo '<a class="btn btn-primary pull-right" href="secondlife://'.$_SESSION['opensim_select'].'/128/128/25"><i class="glyphicon glyphicon-plane"></i> Teleport</a>';

        echo '<p>Simulateur selectionne';
        echo ' <strong class="label label-info">'.$_SESSION['opensim_select'].' '.INI_Conf_Moteur($_SESSION['opensim_select'], "version").'</strong>';
        echo '</p>';

        // echo '<h4>Selectionner un Simulateur</h4>';
        echo '<form class="form-group" method="post" action="">';
        echo '<div class="form-inline">';
        echo '<label for="OSSelect"></label>Select Simulator ';
        echo '<select class="form-control" name="OSSelect">';

        while($data = mysql_fetch_assoc($req))
        {
            // if ($data['osAutorise'] != '') {echo $data['osAutorise'];}
            // else {$osAutorise = explode(";", $data['osAutorise']); echo count($osAutorise);}
            $sel = "";
            
            if ($data['id_os'] == $_SESSION['opensim_select'])
            {
                $sel = "selected";
            }
            echo '<option value="'.$data['id_os'].'" '.$sel.'>'.$data['name'].' '.$data['version'].'</option>';
        }
        
        echo '</select>';
        echo ' <button type="submit" class="btn btn-success"><i class="glyphicon glyphicon-ok"></i> Choisir</button>';
        echo '</div>';
        echo '</form>';
        mysql_close();
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
		// **********
		// TABS USERS
		// **********
		echo '<ul id="myTab" class="nav nav-tabs">';
		echo '<li class="active"><a href="#user" data-toggle="tab">Section Utilisateur</a></li>';
		echo '<li ><a href="#admin" data-toggle="tab">Section Administrateur</a></li></ul>';
        
        echo '<br />';
        
		echo '<div class="panel panel-default">
		          <div class="panel-heading">
    				  <h3 class="panel-title">Choisir une option ci-dessous:</h3>
				  </div>
				  <div class="panel-body">
				      <div class="tab-content">';
		echo '<div class="tab-pane fade in active" id="user">';

		echo '<p><a class="btn btn-default btn-block" href="?a=1">Gestion des Regions</a></p>';
		echo '<p><a class="btn btn-default btn-block" href="?a=10">Gestion des Sauvegardes</a></p>';
		echo '<p><a class="btn btn-default btn-block" href="?a=7">Gestion des Logs</a></p>';
		echo '<p><a class="btn btn-default btn-block" href="?a=2">Sauvegarder une Region</a></p>';
		echo '<p><a class="btn btn-default btn-block" href="?a=3">Sauvegarder un Terrain</a></p>';
        echo '<p><a class="btn btn-default btn-block" href="?a=4">Sauvegarder un Inventaire</a></p>';
        echo '<p><a class="btn btn-default btn-block" href="?a=21">Raccourcis Hypergrid</a></p>';
        echo '<p><a class="btn btn-default btn-block" href="?a=22">Afficher la Map</a></p>';
        // echo '<p><a class="btn btn-default btn-block" href="?a=9">Contacter un Administrateur</a></p>';
		// echo '<p><a class="btn btn-default btn-block" href="?a=11">Charger une Sauvegarde.</a></p>';
		
		echo '</div>';
        
        // ***********
		// TABS ADMINS
        // ***********
		if ($_SESSION['privilege'] >= 3)
		{
            echo '<div class="tab-pane fade in" id="admin">';
		    echo '<p><a class="btn btn-default btn-block" href="?a=18">Configuration du Manager</a></p>';
		    // echo '<p><a class="btn btn-default btn-block" href="?a=8">Gestion du Serveur</a></p>';
		    echo '<p><a class="btn btn-default btn-block" href="?a=17">Gestion des Simulateurs</a></p>';
		    echo '<p><a class="btn btn-default btn-block" href="?a=6">Gestion des Regions</a></p>';
		    echo '<p><a class="btn btn-default btn-block" href="?a=5">Gestion des Fichiers</a></p>';
		    echo '<p><a class="btn btn-default btn-block" href="?a=15">Gestion des Utilisateurs</a>';
            // echo '<p><a class="btn btn-default btn-block" href="?a=12">Connectivite du Serveur OSMW</a></p>';
            // echo '<p><a class="btn btn-default btn-block" href="?a=16">Sauvegardes config Opensim ( * en cours de dev )</a></p>';
		    // echo '<p><a class="btn btn-default btn-block" href="?a=20">Transfert des Sauvegardes</a></p>';
		    // echo '<p><a class="btn btn-default btn-block" href="?a=19">Gestion XMLRPC</a></p>';
			echo '</div>';
		}
		
		// echo '<a class="btn btn-default btn-block" href="?a=logout">D&eacute;connexion.</a>';
		echo '</div>';
		echo '</div>';
		echo '</div>';
		// echo '<p><a class="btn btn-default btn-block" href="?a=14">About OpenSim Web Manager</a></p>';
    }
}

else
{
    session_start();
    $_SESSION = array();
    session_destroy();
    session_unset();
?>

<div class="text-center">
    <h1 class="title">COLLAB <span>3D</span></h1>
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
            Deconnexion reussie, a bientot ...
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
            Deconnexion reussie, a bientot ...</strong>
        </div>
    <?php endif; ?>

    <img style="height:128px;" class="img-thumbnail img-circle center-block" alt="Logo Server" src="img/logo.png">
    <!--<h2 class="form-signin-heading text-center"></h2>-->
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

    <button class="btn btn-lg btn-default btn-block" type="submit">
        <span class="glyphicon glyphicon-log-in" aria-hidden="true"></span> Authentification
    </button>
</form>
<?php } ?>

<div class="clearfix"></div>

<footer class="footer">
    <p class="text-center">Open Simulator Web Manager <?php echo date(Y); ?> <?php echo INI_Conf(VersionOSMW, VersionOSMW); ?> by djphil</p>
</footer>

</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/pdf.js"></script>

<!-- FADE ALERT -->
<script>
    window.setTimeout(function() {$(".alert-anim").fadeTo(500, 0).slideUp(500, function() {$(this).remove();});}, 3000);
</script>
<script>$(function () {$('[data-toggle="tooltip"]').tooltip();});</script>
<script>$(document).ready(function(){$('[data-toggle="popover"]').popover();});</script>
<script>$(document).ready(function(){$('.fade-in').hide().fadeIn();});</script>
<!--<script>.modal.in .modal-dialog {transform: none;}</script>-->

<!-- PDF MODAL -->
<script>
$(function(){    
    $('.view-pdf').on('click',function(){
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
