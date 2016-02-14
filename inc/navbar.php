<nav class="navbar navbar-default">
<div class="container-fluid">
    <div class="navbar-header">
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="index.php">
            <i class="glyphicon glyphicon-education"></i>
            <strong>OSMW</strong>
        </a>
    </div>

    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
        <ul class="nav navbar-nav">
            <li <?php if (!isset($_GET['a'])) {echo 'class="active"';} ?>>
                <a href="index.php" data-toggle="tooltip" data-placement="bott"><i class="glyphicon glyphicon-home"></i> Accueil</a>
            </li>
            <li <?php if ($_GET['a'] == 1) {echo 'class="active"';} ?>>
                <a href="index.php?a=1"><i class="glyphicon glyphicon-th-large"></i> Regions</a>
            </li>
            
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                    <i class="glyphicon glyphicon-hdd"></i> Backups <span class="caret"></span>
                </a>
                <ul class="dropdown-menu">
                    <li <?php if ($_GET['a'] == 2) {echo 'class="active"';} ?>>
                        <a href="index.php?a=2"><i class="glyphicon glyphicon-th-large"></i> Regions</a>
                    </li>
                    <li <?php if ($_GET['a'] == 3) {echo 'class="active"';} ?>>
                        <a href="index.php?a=3"><i class="glyphicon glyphicon-th"></i> Terrains</a>
                    </li>
                    <li <?php if ($_GET['a'] == 4) {echo 'class="active"';} ?>>
                        <a href="index.php?a=4"><i class="glyphicon glyphicon-user"></i> Inventaire</a>
                    </li>
                </ul>
            </li>

            <li <?php if ($_GET['a'] == 10) {echo 'class="active"';} ?>>
                <a href="index.php?a=10"><i class="glyphicon glyphicon-download-alt"></i> Fichiers</a>
            </li>

            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                    Infos <span class="caret"></span>
                </a>
                <ul class="dropdown-menu">
                    <li <?php if ($_GET['a'] == 7) {echo 'class="active"';} ?>>
                        <a href="index.php?a=7"> <i class="glyphicon glyphicon-file"></i> Logs</a>
                    </li>
                    <li <?php if ($_GET['a'] == 13) {echo 'class="active"';} ?>>
                        <a href="index.php?a=13"><i class="glyphicon glyphicon-question-sign"></i> Manual</a>
                    </li>
                    <li <?php if ($_GET['a'] == 14) {echo 'class="active"';} ?>>
                        <a href="index.php?a=14"><i class="glyphicon glyphicon-info-sign"></i> About</a>
                    </li>
                    <li <?php if ($_GET['a'] == 9) {echo 'class="active"';} ?>>
                        <a href="index.php?a=9"><i class="glyphicon glyphicon-info-sign"></i> Contact</a>
                    </li>
                </ul>
            </li>
        </ul>

        <ul class="nav navbar-nav navbar-right">
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                    <i class="glyphicon glyphicon-user"></i> Bienvenue <strong><?php echo $_SESSION['login']; ?></strong> <span class="caret"></span>
                </a>
                <ul class="dropdown-menu">
                    <li><a href="index.php?a=logout"><i class="glyphicon glyphicon-log-out"></i> Deconnexion</a></li>
                </ul>
            </li>
        </ul>
    </div>
</div>
</nav>
