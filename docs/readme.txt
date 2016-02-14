@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ 
Configuration requise pour OSWebManager:
	-- SSH / Mono / Apache / Mysql / PHPMyAdmin* / Lib php SSH2  (*pas nécessaire si autre moyen)
Fonctionnement:
	-- OSMW envoi des commandes au simulateur via le fichier "Screensend" dans un screen Unix
	-- Le simulateur est lancé par le fichier batch "RunOpensim.sh" qui contient le nom du screen (moteur)
	-- Certains fichiers doivent avoir les droits 777 pour pouvoir etre modifier par OSMW
	-- Les fichiers fournis doivent etre installés dans chaque simulateurs (moteurs)
	-- ATTENTION aux droits d'accés aux fichiers 
		--> Régions.ini (droits écriture) / OpensimDefaults.ini , etc.. qui doivent etre accessible
Gestion des Utilisateurs:
	=> 5 Niveaux d'accés sont autorisés
	-- Administrateurs 
	-- Gestionnaires de sauvegardes
	-- Invités / Compte privé par moteur
	-- 1 compte root
@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@

******************************************
********* Suivi de versions **********
******************************************
*** V 4 Beta *** En cours
-- Mise à jours des SESSION
-- Systeme d'installation intégrés **
-- ...
-------------------------------------------------------
*** V 3.2 Final ***
-- Gestion des sauvegardes de la config des moteurs Opensim et pour chaque sim
-- Transfert des fichiers de sauvagardes vers un serveur FTP exterieur
-- Detection des fichiers de config moteurs
-------------------------------------------------------
*** V 3.0 *** MISE A JOUR MAJEUR ***
-- OSMW à sa propre base de donnée *** Nouveauté
-- Les Fichiers de config , conf moteurs et users sont en BDD ( prb de sécurité !)
-- Compte Utilisateur filtré au niveau des moteurs (choix du moteur) *** Nouveauté
-- Verifier/ Modifier/ configurer vos INIs, opensim, grid, ... *** Nouveauté
-- Connectivité AdmOSMW (Referencement sur le site Fgagod.net) 
-------------------------------------------------------
*** V 2.0 ***
-- Optimisations du code
-------------------------------------------------------
*** V 1.1 ***
-- Refonte complete de l'interface
-- Système d'installation simplifié
-- Gestion des moteurs OpenSim, des utilisateurs et de la config en .INI
-- ...
--------------------------------------------------------
*** V 1.0 ***
-- Ajout de la gestion multi-Utilisateurs dans OSMW
--------------------------------------------------------
*** V0.9.11 ***
-- Authentification multi-users via fichier texte  (pas encore intégrer à OSMW)
--------------------------------------------------------
*** V0.7.11 ***
-- Ajouts de Fonctionnaltées;
	-- Cartographie ajouté
	-- TOUS demarrer et arreter d'une seule fois
	-- Une serie de tests pour voir si tous fonctionne bien
	-- Ce fichier LOL
-- Optimisations du code
--------------------------------------------------------
*** V0.6.11 ***
-- Premiere version de OSWebManager