<?php
if (isset($_SESSION['authentification']))
{
    echo '<p class="pull-right"><span class="label label-danger">Espace Securise Niveau '.$_SESSION['privilege'].'</span></p>';
    echo '<h1>A propos</h1>';
    echo '<div class="clearfix"></div>';

    echo '<div class="panel panel-default">';
    echo '<div class="panel-heading">';
    echo '<h3 class="panel-title"><i class="glyphicon glyphicon-info-sign"></i> OpenSim Manager Web</h3>';
    echo '</div>';

    echo '<div class="panel-body">';
    echo '<div class="pull-right">';
    include_once("inc/paypal.php");
    echo '</div>';
    echo "<p>L'objectif de ce projet est de gérer des <strong>Simulateurs OpenSim</strong> sous <strong>Windows</strong>.</p>";

    echo 'Projet entièrement refactorisé en 2015 et maintenu à jour en '.date("Y").' par djphil.<br />';
    echo "Merci a tous les contributeurs et utilisateurs pour ce produit Open Source.<br />";
    echo '<span class="small">(Projet initialement développé en 2010 par Nino85 Whitman).</span>';
    echo '</div>';

    echo '<div class="panel-footer text-center">';
    echo '<div class="row">';
    echo '<div class="col-xs-6">';
    echo '<a class="btn btn-default view-pdf form-control" href="docs/HELP_OSMW-fr.pdf">View PDF Français (Obsolète)</a>';
    echo '</div>';
    echo '<div class="col-xs-6">';
    echo '<a class="btn btn-default view-pdf form-control" href="docs/HELP_OSMW-en.pdf">View PDF English (Obsolete)</a>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
}
else {header('Location: index.php');}
?>
