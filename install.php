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
        <label for="dbhost" class="col-sm-4 control-label">db host</label>
            <div class="col-sm-4">
                <input class="form-control" type="text" name="dbhost" maxlength="40" placeholder="localhost" required />
            </div>
        </div>

        <div class="form-group">
        <label for="dbuser" class="col-sm-4 control-label">db user</label>
            <div class="col-sm-4">
                <input class="form-control" type="text" name="dbuser" maxlength="40" placeholder="root" required />
            </div>
        </div>

        <div class="form-group">
        <label for="dbpass" class="col-sm-4 control-label">db pass</label>
            <div class="col-sm-4">
                <input class="form-control" type="password" name="dbpass" maxlength="40" placeholder="password" required />
            </div>
        </div>

        <div class="form-group">
        <label for="dbname" class="col-sm-4 control-label">db name</label>
            <div class="col-sm-4">
                <input class="form-control" type="text" name="dbname" maxlength="40" placeholder="osmw" required />
            </div>
        </div>

        <div class="form-group">
        <label for="sshdns" class="col-sm-4 control-label">dns name</label>
            <div class="col-sm-4">
                <input class="form-control" type="text" name="sshdns" maxlength="40" placeholder="domaine.com" required />
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

        $dbhost = trim($_POST['dbhost']);
        $dbuser = trim($_POST['dbuser']);
        $dbpass = trim($_POST['dbpass']);
        $dbname = trim($_POST['dbname']);
        $sshdns = trim($_POST['sshdns']);

        $db = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
        if (mysqli_connect_errno()) {echo "Failed to connect to MySQL: ".mysqli_connect_error();}

        if (!$db)
        {
            echo '<div class="alert alert-danger">Mauvais paramètres de connexion, installation interompue ...</div>';
            echo '<div class="col-sm-offset-4 col-sm-4">';
            echo '<p><button class="btn btn-primary btn-block" type="submit" onclick="history.back()">Retour</button></p>';
            echo '</div>';
            goto end;
        }

        if (!mysqli_select_db($db, $dbname))
        {
            echo '<div class="alert alert-danger">Mauvais nom de base de données, installation interompue ...</div>';
            echo '<div class="col-sm-offset-4 col-sm-4">';
            echo '<p><button class="btn btn-primary btn-block" type="submit" onclick="history.back()">Retour</button></p>';
            echo '</div>';
            goto end;
        }
        
        $texte = '<?php
$hostnameBDD = "'.$dbhost.'";
$userBDD = "'.$dbuser.'";
$passBDD = "'.$dbpass.'";
$database = "'.$dbname.'";
$hostnameSSH = "'.$sshdns.'";

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

        echo '<div class="alert alert-success">Création du fichier <b>config.php</b> effectuée avec succès ...</div>';
        fclose($ouvrir);

        $request = '';
        $sql = file('docs/sql/osmw.sql');
        
        foreach($sql as $lecture)
        {
            if (substr(trim($lecture), 0, 2) != '--')
            {
                $request .= $lecture;
            }
        }

        $reqs = explode(';', $request);
        
        foreach($reqs as $req)
        {
            if (!mysqli_query($db, $req) AND trim($req) != '')
            {
                exit('ERROR: '.$req);
            }
        }

        echo '<div class="alert alert-success">Installation effectuée avec succès ...</div>';
        echo '<div class="alert alert-warning">Veuillez supprimer le fichier <b>install.php</b> du server ...</div>';
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
