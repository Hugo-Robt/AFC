<?php

if (!isset($_REQUEST['action'])) {
    $_REQUEST['action'] = 'saisirFrais';
}

$action = $_REQUEST['action'];

switch ($_REQUEST['action']) {
    case 'saisirFrais':
        $id = $_SESSION['idVisiteur'];
        $nom = $_SESSION['nom'];
        $prenom = $_SESSION['prenom'];

        $visiteurs = $pdo->getListeVisiteur();

        include("vues/v_sommaire.php");
        include('vues/v_saisirFrais.php');
        break;
}
?>