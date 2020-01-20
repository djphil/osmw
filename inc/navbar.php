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
            <i class="glyphicon glyphicon-fire"></i> <strong>OSMW <?php echo INI_Conf('VersionOSMW', 'VersionOSMW'); ?></strong>
        </a>
    </div>

    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
        <ul class="nav navbar-nav">
            <li <?php if (!isset($_GET['a'])) {echo 'class="active"';} ?>>
                <a href="index.php" data-toggle="tooltip" data-placement="bott">
                    <i class="glyphicon glyphicon-home"></i> <?php echo $txt_menu_home ; ?>
                </a>
            </li>
            <li <?php if (isset($_GET['a']) && $_GET['a'] == 1) {echo 'class="active"';} ?>>
                <a href="index.php?a=1"><i class="glyphicon glyphicon-th-large"></i> <?php echo $txt_menu_managment ; ?></a>
            </li>

            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                    <i class="glyphicon glyphicon-hdd"></i> <?php echo $txt_menu_backups ; ?> <span class="caret"></span>
                </a>
                <ul class="dropdown-menu">
                    <li <?php if (isset($_GET['a']) && $_GET['a'] == 2) {echo 'class="active"';} ?>>
                        <a href="index.php?a=2"><i class="glyphicon glyphicon-th-large"></i> <?php echo $txt_menu_regions ; ?></a>
                    </li>
                    <li <?php if (isset($_GET['a']) && $_GET['a'] == 3) {echo 'class="active"';} ?>>
                        <a href="index.php?a=3"><i class="glyphicon glyphicon-th"></i> <?php echo $txt_menu_terrains ; ?></a>
                    </li>
                    <li <?php if (isset($_GET['a']) && $_GET['a'] == 4) {echo 'class="active"';} ?>>
                        <a href="index.php?a=4"><i class="glyphicon glyphicon-user"></i> <?php echo $txt_menu_inventory ; ?></a>
                    </li>
                </ul>
            </li>

            <li <?php if (isset($_GET['a']) && $_GET['a'] == 10) {echo 'class="active"';} ?>>
                <a href="index.php?a=10"><i class="glyphicon glyphicon-download-alt"></i> <?php echo $txt_menu_files ; ?></a>
            </li>

            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                    <i class="glyphicon glyphicon-info-sign"></i> <?php echo $txt_menu_infos ; ?> <span class="caret"></span>
                </a>
                <ul class="dropdown-menu">
                    <li <?php if (isset($_GET['a']) && $_GET['a'] == 14) {echo 'class="active"';} ?>>
                        <a href="index.php?a=14"><i class="glyphicon glyphicon-info-sign"></i> <?php echo $txt_menu_about ; ?></a>
                    </li>
                    <li <?php if (isset($_GET['a']) && $_GET['a'] == 24) {echo 'class="active"';} ?>>
                        <a href="index.php?a=24"><i class="glyphicon glyphicon-info-sign"></i> <?php echo $txt_menu_tos ; ?></a>
                    </li>
                    <li <?php if (isset($_GET['a']) && $_GET['a'] == 9) {echo 'class="active"';} ?>>
                        <a href="index.php?a=9"><i class="glyphicon glyphicon-envelope"></i> <?php echo $txt_menu_contact ; ?></a>
                    </li>
                    <li <?php if (isset($_GET['a']) && $_GET['a'] == 7) {echo 'class="active"';} ?>>
                        <a href="index.php?a=7"> <i class="glyphicon glyphicon-file"></i> <?php echo $txt_menu_logs ; ?></a>
                    </li>
                </ul>
            </li>
        </ul>

        <ul class="nav navbar-nav navbar-right">
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                    <i class="glyphicon glyphicon-user"></i> <?php echo $txt_welcome ; ?> <strong><?php echo $_SESSION['login']; ?></strong> <span class="caret"></span>
                </a>
                <ul class="dropdown-menu">
                    <?php if ($_SESSION['privilege'] >= 3): ?>
                    <li><a href="?a=18"><i class="glyphicon glyphicon-cog"></i> Configuration du Manager</a></li>
                    <li><a href="?a=17"><i class="glyphicon glyphicon-cog"></i> Gestion des Simulateurs</a></li>
                    <li><a href="?a=6"><i class="glyphicon glyphicon-cog"></i> Gestion des Régions</a></li>
                    <li><a href="?a=5"><i class="glyphicon glyphicon-cog"></i> Gestion des Fichiers</a></li>
                    <li><a href="?a=15"><i class="glyphicon glyphicon-cog"></i> Gestion des Utilisateurs</a></li>
                    <li><a href="?a=23"><i class="glyphicon glyphicon-cog"></i> Gestion des Robots</a></li>
                    <li class="divider"></li>
                    <?php endif; ?>
                    <li><a href="index.php?a=logout"><i class="glyphicon glyphicon-log-out"></i> <?php echo $txt_menu_logout ; ?></a></li>
                </ul>
            </li>
        </ul>
    </div>
</div>
</nav>
