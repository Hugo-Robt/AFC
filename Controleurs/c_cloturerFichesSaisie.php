<?php

if (!isset($_REQUEST['action'])) {
    $_REQUEST['action'] = 'confirmationCloture';
}

$action = $_REQUEST['action'];

switch ($_REQUEST['action']) {
    case 'confirmationCloture':
        $id = $_SESSION['idVisiteur'];
        $nom = $_SESSION['nom'];
        $prenom = $_SESSION['prenom'];

        $message = '';
        include('vues/v_message.php');

        include("vues/v_sommaire.php");

        echo '<div id="contenu">';
        include("vues/v_messageOuiNon.php");
        echo '</div>';

        break;
    case 'traiterReponseFiches':
        $id = $_SESSION['idVisiteur'];
        $nom = $_SESSION['nom'];
        $prenom = $_SESSION['prenom'];

        if ($_POST['btnOui']) {
            $message = 'Les fiches ont été clôturées avec succès !';
            $pdo->cloturerLesFiches(affichageMois());
            
            include('vues/v_message.php');

            include("vues/v_sommaire.php");

            echo '<div id="contenu">';
            include("vues/v_messageOuiNon.php");
            echo '</div>';
        } else {
            include("vues/v_sommaire.php");
        }

        break;
}
?>