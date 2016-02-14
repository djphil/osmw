<?php
$hostnameBDD = "localhost";
$userBDD     = "root";
$passBDD     = "***";
$database    = "osmw";
$hostnameSSH = "domain.com";

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
?>