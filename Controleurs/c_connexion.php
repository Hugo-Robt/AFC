<?php

if (!isset($_REQUEST['action'])) {
    $_REQUEST['action'] = 'demandeConnexion';
}
$action = $_REQUEST['action'];

switch ($action) {
    case 'demandeConnexion': {
            include("vues/v_connexion.php");
            break;
        }
    case 'valideConnexion': {
            $login = $_REQUEST['login'];
            $mdp = $_REQUEST['mdp'];
            $comptables = $pdo->getInfosComptable($login, $mdp);
            if (!is_array($comptables)) {
                ajouterErreur("Login ou mot de passe incorrect");
                include("vues/v_erreurs.php");
                include("vues/v_connexion.php");
            } else {
                $id = $comptables[0]['id'];
                $nom = $comptables[0]['nom'];
                $prenom = $comptables[0]['prenom'];
                connecter($id, $nom, $prenom);
                include("vues/v_sommaire.php");
            }
            break;
        }

    case 'deconnexion': {
            // Code ajouté par moi. Sans cela les informations de sessions
            // ne sont pas supprimées lors d'une déconnexion.
            deconnecter();
            include("vues/v_connexion.php");
            break;
        }

    default : {
            include("vues/v_connexion.php");
            break;
        }
}
?>