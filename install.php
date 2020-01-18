<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>OpenSimulator Manager Web v6.0 by djphil (CC-BY-NC-SA 4.0)</title>
    <meta name="description" content="">
    <meta name="author" content="Philippe Lemaire (djphil)">
    <link rel="icon" href="img/favicon.ico">
    <link rel="author" href="inc/humans.txt">
    <link rel="icon" href="img/favicon.ico">
    <link rel="stylesheet" href="css/bootstrap.min.css">
</head>
<body>
<div class="container text-center">
    <h1>OpenSimulator Manager Web v6.0</h1>
    <?php if (!isset($_POST['etape'])): ?>
    <form class="form-horizontal" action="" method="post">
        <input type="hidden" name="etape" value="1" />

        <div class="form-group">
        <label for="hote" class="col-sm-4 control-label">db host</label>
            <div class="col-sm-4">
                <input class="form-control" type="text" name="hote" maxlength="40" placeholder="localhost" />
            </div>
        </div>

        <div class="form-group">
        <label for="login" class="col-sm-4 control-label">db user</label>
            <div class="col-sm-4">
                <input class="form-control" type="text" name="login" maxlength="40" placeholder="root" />
            </div>
        </div>

        <div class="form-group">
        <label for="mdp" class="col-sm-4 control-label">db pass</label>
            <div class="col-sm-4">
                <input class="form-control" type="password" name="mdp" maxlength="40" placeholder="password" />
            </div>
        </div>

        <div class="form-group">
        <label for="base" class="col-sm-4 control-label">db name</label>
            <div class="col-sm-4">
                <input class="form-control" type="text" name="base" maxlength="40" placeholder="osmw" />
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-offset-4 col-sm-4">
                <button class="btn btn-success btn-block" type="submit" name="submit" value="Installer">Installer</button>
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
        define('RETOUR', '<input class="btn btn-primary" type="button" value="Retour au formulaire" onclick="history.back()">');

        $fichier = 'inc/config.php';

        if (file_exists($fichier) AND filesize($fichier ) > 0)
        {
            exit('<div class="alert alert-danger">Fichier de configuration existant, installation interompue ...</div>'. RETOUR);
        }

        $hote   = trim($_POST['hote']);
        $login  = trim($_POST['login']);
        $pass   = trim($_POST['mdp']);
        $base   = trim($_POST['base']);

        if (!mysql_connect($hote, $login, $pass))
        {
            exit('<div class="alert alert-danger">Mauvais parametres de connexion, installation interompue ...</div>'. RETOUR);
        }

        if (!mysql_select_db($base))
        {
            exit('<div class="alert alert-danger">Mauvais nom de base, installation interompue ...</div>'. RETOUR);
        }

        $texte = '
<?php
$hostnameBDD = "'.$hote.'";
$userBDD = "'.$login.'";
$passBDD = "'.$pass.'";
$database = "'.$base.'";
$hostnameSSH = "domaine.com";

/* Noms des fichiers INI  */
$FichierINIOpensim = "OpenSim.ini";
$FichierINIRegions = "Regions.ini";

/* Themes */
$themes = true;

/* Languages */
$translator = true;
$languages = [
    "fr" => "French",
    "en" => "English",
    "de" => "German",
    "es" => "Spanish",
    "it" => "Italian",
    "nl" => "Dutch",
    "pt" => "Portuguese",
    "fi" => "Finnish",
    "gr" => "Greek",
    "slo" => "Slovenski"
];

$github_url = "https://github.com/djphil/osmw";
$github_txt = "Fork me on GitHub";

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
        $sql = file('docs/sql/database.sql');
        
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
    ?>
    <div class="clearfix"></div>

    <footer class="footer">
        <p>OpenSimulator Manager Web v6.0 by djphil (CC-BY-NC-SA 4.0)</p>
    </footer>
</div>

</body>
</html>
