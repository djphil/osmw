<?php
echo '<p class="pull-right"><span class="label label-danger">Espace Securise Niveau '.$_SESSION['privilege'].'</span></p>';
echo '<h1>Formulaire de Contact</h1>';
echo '<div class="clearfix"></div>';

if (isset($_POST['OSSelect'])) {$_SESSION['opensim_select'] = trim($_POST['OSSelect']);}
	
echo '<p>Simulateur selectionné ';
echo '<strong class="label label-info">'.$_SESSION['opensim_select'].' '.INI_Conf_Moteur($_SESSION['opensim_select'], "version").'</strong>';
echo '</p>';

if (isset($_SESSION['authentification']))
{
    /* CONFIGURATION */
    $form_action = 'index.php?a=9';
    $message_envoye = "<i class='glyphicon glyphicon-ok'></i> Message envoye avec succes ...";
    $message_non_envoye = "<i class='glyphicon glyphicon-remove'></i> Echec d'envoi du message, veuillez reessayer ...";
    $message_formulaire_invalide = "<i class='glyphicon glyphicon-remove'></i> Erreur dans le formulaire, veuillez reessayer ...";
    $error = false;

    $nom = (isset($_POST['nom'])) ? Rec($_POST['nom']) : '';
    $email = (isset($_POST['email'])) ? Rec($_POST['email']) : '';
    $objet = (isset($_POST['objet'])) ? Rec($_POST['objet']) : '';
    $message = (isset($_POST['message'])) ? Rec($_POST['message']) : '';

	if (isset($_POST['envoi']))
	{
		$email = (IsEmail($email)) ? $email : ''; 
		$error = (IsEmail($email)) ? false : true;

		if (($nom != '') && ($email != '') && ($objet != '') && ($message != ''))
		{
			$headers = 'From: '.$nom.' <'.$email.'>' . "\r\n";
            if ($_POST['sendcopy'] == true) {$cible = INI_Conf(0, "destinataire").', '.$email;}
			else {$cible = INI_Conf(0, "destinataire");}

			$message = html_entity_decode($message);
			$message = str_replace('&#039;', "'", $message);
			$message = str_replace('&#8217;', "'", $message);
			$message = str_replace('<br>', '', $message);
			$message = str_replace('<br />', '', $message);
			$message = $message.' > Serveur Concerne: '.$hostnameSSH.' > Simulateur Selectionne: '.$_SESSION['opensim_select'].' '.INI_Conf_Moteur($_SESSION['opensim_select'], "version");
            if (mail($cible, $objet, $message, $headers)) {echo '<div class="alert alert-success alert-anim">'.$message_envoye.'</div>';}
            else {echo '<div class="alert alert-danger alert-anim">'.$message_non_envoye.'</div>';}
		}

		else
		{
			echo '<div class="alert alert-danger alert-anim">'.$message_formulaire_invalide.'</div>';
            echo '<a class="btn btn-primary" href="index.php?a=9"><i class="glyphicon glyphicon-envelope"></i> Retour au formulaire</a>';
			$error = true;
		}
	}

	if ((!$error) || (!isset($_POST['envoi'])))
	{	
		echo "\n".'<form class="form-group" id="contact" method="post" action="'.$form_action.'">'."\n";
		echo '  <h4>Vos coordonnées</h4>'."\n";
		echo '      <div class="form-group">'."\n";
		echo '          <label for="nom">Nom:</label>'."\n";
		echo '          <input class="form-control" type="text" id="nom" name="nom" value="'.stripslashes($nom).'" tabindex="1" />'."\n";
		echo '      </div>'."\n";
		echo '      <div class="form-group">'."\n";
		echo '          <label for="email">Email:</label>'."\n";
		echo '          <input class="form-control" type="text" id="email" name="email" value="'.stripslashes($email).'" tabindex="2" />'."\n";
		echo '      </div>'."\n";

		echo '  <h4>Votre message</h4>'."\n";
		echo '      <div class="form-group">'."\n";
		echo '          <label for="objet">Sujet:</label>'."\n";
		echo '          <input class="form-control" type="text" id="objet" name="objet" value="'.stripslashes($objet).'" tabindex="3" />'."\n";
		echo '      </div>'."\n";
		echo '      <div class="form-group">'."\n";
		echo '          <label for="message">Message:</label>'."\n";
		echo '          <textarea class="form-control" id="message" name="message" tabindex="4" rows="5" >'.stripslashes($message).'</textarea>'."\n";
		echo '      </div>'."\n";
        echo '      <div class="checkbox">'."\n";
        echo '          <label><input type="checkbox" name="sendcopy" value="true" id="Remember"> Send me a copy of this mail</label>'."\n";
        echo '      </div>'."\n";

		echo '  <button class="btn btn-success" type="submit" name="envoi" value="Envoyer"><i class="glyphicon glyphicon-envelope"></i> Envoyer le message</button>'."\n";
		echo '</form>'."\n";
	}
}
else {header('Location: index.php');}	
?>
