<?php
// verification sur la session authentification 
if (isset($_SESSION['authentification']))
{
    echo '<p class="pull-right"><span class="label label-danger">Espace Securise Niveau '.$_SESSION['privilege'].'</span></p>';
    echo '<h1>A propos</h1>';
    echo '<div class="clearfix"></div>';

	echo '<div class="panel panel-info">';
	echo '<div class="panel-heading">';
	echo '<h3 class="panel-title"><i class="glyphicon glyphicon-info-sign"></i> OpenSim Manager Web</h3>';
	echo '</div>';

	echo '<div class="panel-body">';
	echo "<p>L'objectif de ce projet est de permettre la gestion de Simulateurs <strong>OpenSim</strong> installés sur un serveur <strong>Windows</strong></p>";
    echo "<p>Merci a tous les Contributeurs et Utilisateurs pour ce produit Open Source</p>";
	echo '</div>';

    echo '<div class="panel-footer">';
    echo 'Projet initialement développé par <a href="mailto:contact@fgagod.net">Nino85 Whitman</a> en 2010';
    echo ' et entièrement refactorisé par <a href="index.php?a=9">djphil</a> en 2015';
    echo '</div>';
	echo '</div>';

	echo '<div class="panel panel-warning">';
	echo '<div class="panel-heading">';
	echo '<h3 class="panel-title"><i class="glyphicon glyphicon-piggy-bank"></i> Pour aider au developpement de OpenSim Web Manager</h3>';
	echo '</div>';
	echo '<div class="panel-body">';
    include_once "./inc/paypal.php";
	// echo '<a href="./inc/paypal.php" target="_blank"><img src="img/paypal.gif"></a>';
    echo '</div>';
    echo '</div>';
    // echo phpinfo();
}
else {header('Location: index.php');}
?>