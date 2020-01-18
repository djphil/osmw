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
        <label for="base" class="col-sm-4 control-label">dns name</label>
            <div class="col-sm-4">
                <input class="form-control" type="text" name="dns" maxlength="40" placeholder="domaine.com" />
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-offset-4 col-sm-4">
                <button class="btn btn-success btn-block" type="submit" name="submit" value="Installer">Installer</button>
            </div>
        </div>
    </form>
    <?php endif ?>

    <?php
    if (isset($_POST['delete']))
    {
        unlink('install.php');
        header('Location: ./');
    }
    ?>

    <?php
    if (isset($_POST['etape']) AND $_POST['etape'] == 1)
    {
        $file = 'inc/config.php';

        if (file_exists($file) AND filesize($file ) > 0)
        {
            echo '<div class="alert alert-danger">Fichier de configuration existant, installation interompue ...</div>';
            echo '<div class="col-sm-offset-4 col-sm-4">';
            echo '<p><button class="btn btn-primary btn-block" type="submit" onclick="history.back()">Retour</button></p>';
            echo '</div>';
            goto end;
        }

        $hote = trim($_POST['hote']);
        $login = trim($_POST['login']);
        $pass = trim($_POST['mdp']);
        $base = trim($_POST['base']);
        $dns = trim($_POST['dns']);

        $con = mysqli_connect($hote, $login, $pass, $base);
        if (mysqli_connect_errno()) {echo "Failed to connect to MySQL: ".mysqli_connect_error();}

        if (!$con)
        {
            echo '<div class="alert alert-danger">Mauvais paramètres de connexion, installation interompue ...</div>';
            echo '<div class="col-sm-offset-4 col-sm-4">';
            echo '<p><button class="btn btn-primary btn-block" type="submit" onclick="history.back()">Retour</button></p>';
            echo '</div>';
            goto end;
        }

        if (!mysqli_select_db($con, $base))
        {
            echo '<div class="alert alert-danger">Mauvais nom de base de données, installation interompue ...</div>';
            echo '<div class="col-sm-offset-4 col-sm-4">';
            echo '<p><button class="btn btn-primary btn-block" type="submit" onclick="history.back()">Retour</button></p>';
            echo '</div>';
            goto end;
        }
        
        $texte = '
<?php
$hostnameBDD = "'.$hote.'";
$userBDD = "'.$login.'";
$passBDD = "'.$pass.'";
$database = "'.$base.'";
$hostnameSSH = "'.$dns.'";

/* Noms des fichiers INI  */
$FichierINIOpensim = "OpenSim.ini";
$FichierINIRegions = "Regions.ini";

/* Themes */
$theme = true;
$themes = [
    "default",
    "amelia",
    "cerulean",
    "cosmo",
    "cyborg",
    "darkly",
    "flatly",
    "freelancer",
    "journal",
    "lumen",
    "paper",
    "readable",
    "sandstone",
    "simplex",
    "slate",
    "spacelab",
    "superhero",
    "united",
    "yety"
];

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

        if (!$ouvrir = fopen($file, 'w'))
        {
            echo '<div class="alert alert-danger">Impossible d\'ouvrir le fichier : <strong>'.$file.'</strong>, installation interompue ...</div>';
            echo '<div class="col-sm-offset-4 col-sm-4">';
            echo '<p><button class="btn btn-primary btn-block" type="submit" onclick="history.back()">Retour</button></p>';
            echo '</div>';
            goto end;
        }

        if (fwrite($ouvrir, $texte) == FALSE)
        {
            echo '<div class="alert alert-danger">Impossible d\'écrire dans le fichier : <strong>'.$file.'</strong>, installation interompue ...</div>';
            echo '<div class="col-sm-offset-4 col-sm-4">';
            echo '<p><button class="btn btn-primary btn-block" type="submit" onclick="history.back()">Retour</button></p>';
            echo '</div>';
            goto end;
        }

        echo '<div class="alert alert-success">Création du fichier de configuration effectuée avec succès ...</div>';
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
                exit('ERROR: '.$req);
            }
        }

        $requetes = '';
        $sql = file('docs/sql/database_NPC.sql');

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
                exit('ERROR : '.$req);
            }
        }

        echo '<div class="alert alert-success">Installation effectuée avec succès ...</div>';
        echo '<div class="alert alert-warning">Veuillez supprimer le fichier <strong>install.php</strong> du server ...</div>';
        echo '<form class="form-group" action="" method="post">';
        echo '<input type="hidden" name="delete" value="1" />';
        echo '<div class="form-group">';
        echo '<button class="btn btn-danger" type="submit" name="submit" >Effacer le fichier install.php</button>';
        echo '</div>';
        echo '</form>';
    }
    end:
    ?>
    <div class="clearfix"></div>

    <footer class="footer">
        <p>OpenSimulator Manager Web v6.0 by djphil (CC-BY-NC-SA 4.0)</p>
    </footer>
</div>

</body>
</html>
