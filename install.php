<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="img/favicon.ico">
    <title>Open Simulator Web Manager</title>
    <link rel="stylesheet" href="css/bootstrap.min.css" type="text/css" />
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>
<div class="container">
<h1>Open Simulator Web Manager v5.0</h1>

<?php if (!isset($_POST['etape'])): ?>
<form class="form-horizontal" action="" method="post">
    <input type="hidden" name="etape" value="1" />

    <div class="form-group">
    <label for="hote" class="col-sm-2 control-label">Database Host :</label>
        <div class="col-sm-4">
            <input class="form-control" type="text" name="hote" maxlength="40" />
        </div>
    </div>

    <div class="form-group">
    <label for="login" class="col-sm-2 control-label">Database User :</label>
        <div class="col-sm-4">
            <input class="form-control" type="text" name="login" maxlength="40" />
        </div>
    </div>

    <div class="form-group">
    <label for="mdp" class="col-sm-2 control-label">Database Password :</label>
        <div class="col-sm-4">
            <input class="form-control" type="password" name="mdp" maxlength="40" />
        </div>
    </div>

    <div class="form-group">
    <label for="base" class="col-sm-2 control-label">Database Name :</label>
        <div class="col-sm-4">
            <input class="form-control" type="text" name="base" maxlength="40" />
        </div>
    </div>

    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <button class="btn btn-success" type="submit" name="submit" value="Installer">Installer</button>
        </div>
    </div>

</form>
<?php endif ?>

<?php if (isset($_POST['delete']))
{
    unlink('install.php');
    header('Location: ./');
}
?>

<?php
if (isset($_POST['etape']) AND $_POST['etape'] == 1)
{
    // on crée une constante dont on se servira plus tard
    define('RETOUR', '<input class="btn btn-primary" type="button" value="Retour au formulaire" onclick="history.back()">');

    $fichier = './inc/config.php';

    if (file_exists($fichier) AND filesize($fichier ) > 0)
    {
        // si le fichier existe et qu'il n'est pas vide alors
        exit('<div class="alert alert-danger">Fichier de configuration existant, installation interompue ...</div>'. RETOUR);
    }

    // on crée nos variables, et au passage on retire les éventuels espaces	
    $hote   = trim($_POST['hote']);
    $login  = trim($_POST['login']);
    $pass   = trim($_POST['mdp']);
    $base   = trim($_POST['base']);

    // on vérifie la connectivité avec le serveur avant d'aller plus loin
    if (!mysql_connect($hote, $login, $pass))
    {
        exit('<div class="alert alert-danger">Mauvais parametres de connexion, installation interompue ...</div>'. RETOUR);
    }

    // on vérifie la connectivité avec la base avant d'aller plus loin
    if (!mysql_select_db($base))
    {
        exit('<div class="alert alert-danger">Mauvais nom de base, installation interompue ...</div>'. RETOUR);
    }

    // le texte que l'on va mettre dans le config.php
    $texte = '
<?php
$hostnameBDD   = "'. $hote .'  // IP de votre serveur Bdd";
$userBDD  = "'. $login .'       // nom utilisateur";
$passBDD    = "'. $mdp .'     // mot de passe ";
$database   = "'. $base .' // nom de votre base de donnees";
$hostnameSSH = "domaine.com";

mysql_connect($hostnameBDD, $userBDD, $passBDD);
mysql_select_db($database);

/* Noms des fichiers INI  */
$FichierINIRegions = "Regions.ini";          // ou RegionConfig.ini
$FichierINIOpensim = "OpenSimDefaults.ini";  // ou OpenSim.ini

/* Themes */
$themes = true;

/* Languages */
$translator = true;
$languages=array("fr" => "French",
    "en" => "English",
    "de" => "German",
    "es" => "Spanish",
    "it" => "Italian",
    "nl" => "Dutch",
    "pt" => "Portuguese",
    "fi" => "Finnish",
    "gr" => "Greek",
    "slo" => "Slovenski");

/* Google ReCaptcha */
$recaptcha = false;
$siteKey   = "***";
$secret    = "***";
$lang      = "fr";
?>';

    if (!$ouvrir = fopen($fichier, 'w'))
    {
        exit('<div class="alert alert-danger">Impossible d\'ouvrir le fichier : <strong>'. $fichier .'</strong>, installation interompue ...</div>'. RETOUR);
    }

    if (fwrite($ouvrir, $texte) == FALSE)
    {
        exit('<div class="alert alert-danger">Impossible d\'écrire dans le fichier : <strong>'. $fichier .'</strong>, installation interompue ...</div>'. RETOUR);
    }

    echo '<div class="alert alert-success">Creation du fichier de configuration effectuee avec success ...</div>';
    fclose($ouvrir);

    $requetes = '';
    $sql = file('./docs/sql/database.sql');
    
    foreach($sql as $lecture)
    {
        if (substr(trim($lecture), 0, 2) != '--')
        {
            $requetes .= $lecture;
        }
    }

    $reqs = split(';', $requetes);
    
    foreach($reqs as $req)
    {
        if (!mysql_query($req) AND trim($req) != '')
        {
            exit('ERREUR : '.$req);
        }
    }

    echo '<div class="alert alert-success">Installation des tables dans la base de donnees effectuee avec success ...</div>';
    echo '<div class="alert alert-warning">Veuillez supprimer le fichier <strong>install.php</strong> du server ...</div>';
    echo '<form class="form-group" action="" method="post">';
    echo '<input type="hidden" name="delete" value="1" />';
    echo '<div class="form-group">';
    echo '<button class="btn btn-danger" type="submit" name="submit" >Effacer le fichier install.php</button>';
    echo '</div>';
    echo '</form>';
}
// else exit('<a class="btn btn-default" href="index.php">Open Simulator Web Manager</a>');
?>
<div class="clearfix"></div>

<footer class="footer">
    <p>Open Simulator Web Manager <?php echo date(Y); ?> v5.0 by djphil</p>
</footer>
</div>

</body>
</html>
