<?php

require_once 'Include/class.FicheFrais.inc.php';

if (!isset($_REQUEST['action'])) {
    $_REQUEST['action'] = 'validerFicheFrais';
}
$action = $_REQUEST['action'];

switch ($_REQUEST['action']) {
    case 'choixInitialVisiteur':
        $id = $_SESSION['idVisiteur'];
        $nom = $_SESSION['nom'];
        $prenom = $_SESSION['prenom'];

        $selected = null;
        $visiteurs = $pdo->getListeVisiteur();

        include "Vues/v_sommaire.php";

        echo '<div id="contenu">'
        . "<h2>Validation d'une fiche de frais visiteur</h2>"
        . "<br />";

        require "Vues/v_valideFraisChoixVisiteur.php";

        echo '</div>';

        break;
    case 'afficherFicheFraisSelectionnee':
        $_SESSION['idVisiteur'] = $_POST['lstVisiteur'];
        $_SESSION['ficheMois'] = $_POST['txtMois'];

        $nom = $_SESSION['nom'];
        $prenom = $_SESSION['prenom'];

        $selected = $_SESSION['idVisiteur'];
        $visiteurs = $pdo->getListeVisiteur();

        $ficheFrais = new FicheFrais($selected, $_SESSION['ficheMois']);
        $ficheFrais->initAvecInfosBDD();
        $libelleEtat = $ficheFrais->getLibelleEtat();
        $nbJustificatifs = $ficheFrais->getNbJustificatifs();
        $qteDeFraisForfaitises = $ficheFrais->getLesQuantitesDeFraisForfaitises();

        $etape = $qteDeFraisForfaitises['ETP'];
        $kilometre = $qteDeFraisForfaitises['KM'];
        $nuitee = $qteDeFraisForfaitises['NUI'];
        $repas = $qteDeFraisForfaitises['REP'];

        $lesInfosFraisHorsForfait = $ficheFrais->getLesInfosFraisHorsForfait();

        include "Vues/v_sommaire.php";

        echo '<div id="contenu">'
        . "<h2>Validation d'une fiche de frais visiteur</h2>"
        . "<br />";

        require "Vues/v_valideFraisChoixVisiteur.php";
        echo '<br />';
        require "Vues/v_valideFraisCorpsFiche.php";

        echo '</div>';
        break;
    case 'enregModifFF':
        $nom = $_SESSION['nom'];
        $prenom = $_SESSION['prenom'];

        $selected = $_SESSION['idVisiteur'];
        $visiteurs = $pdo->getListeVisiteur();

        // Instantiation de la fiche de frais
        // et initialisation de la fiche de frais avec les infos de la base de données sans les frais forfaitisés
        $ficheFrais = new FicheFrais($_SESSION['idVisiteur'], $_SESSION['ficheMois']);
        $ficheFrais->initAvecInfosBDDSansFF();

        $libelleEtat = $ficheFrais->getLibelleEtat();
        $nbJustificatifs = $ficheFrais->getNbJustificatifs();
        $qteDeFraisForfaitises = $ficheFrais->getLesQuantitesDeFraisForfaitises();

        // Ajout des frais forfaitisés saisis par l'utilisateur
        $ficheFrais->ajouterUnFraisForfaitise('ETP', $_POST['txtForfaitEtape']);
        $ficheFrais->ajouterUnFraisForfaitise('KM', $_POST['txtFraisKilometriques']);
        $ficheFrais->ajouterUnFraisForfaitise('NUI', $_POST['txtNuiteeHotel']);
        $ficheFrais->ajouterUnFraisForfaitise('REP', $_POST['txtRepasRestaurant']);

        // Vérifie si la mise à jour c'est bien passé
        if ($ficheFrais->mettreAJourLesFraisForfaitises()) {
            echo '<p>La mise à jour a été effectuer avec succès !</p>';
        } else {
            echo '<p style="color:red;">La mise à jour n\'a pas pu être effectuer avec succès !</p>';
        }

        // Récupération des frais forfaitisés
        $lignes = $ficheFrais->getLesFraisForfaitises();
        $etape = $lignes['ETP']->getQuantite();
        $kilometre = $lignes['KM']->getQuantite();
        $nuitee = $lignes['NUI']->getQuantite();
        $repas = $lignes['REP']->getQuantite();

        include "Vues/v_sommaire.php";

        echo '<div id="contenu">'
        . "<h2>Validation d'une fiche de frais visiteur</h2>"
        . "<br />";

        require "Vues/v_valideFraisChoixVisiteur.php";
        require "Vues/v_valideFraisCorpsFiche.php";

        echo '</div>';

        break;
    case 'enregModifFHF':
        $nom = $_SESSION['nom'];
        $prenom = $_SESSION['prenom'];

        $selected = $_SESSION['idVisiteur'];
        $visiteurs = $pdo->getListeVisiteur();

        // Instantiation de la fiche de frais
        // et initialisation de la fiche de frais avec les infos de la base de données sans les frais hors forfait
        $ficheFrais = new FicheFrais($selected, $_SESSION['ficheMois']);
        $ficheFrais->initAvecInfosBDDSansFHF();

        $libelleEtat = $ficheFrais->getLibelleEtat();
//        $nbJustificatifs = $ficheFrais->getNbJustificatifs();
        $nbJustificatifs = $_POST['txtHFNbJustificatifsPEC'];
        $ficheFrais->setNbJustificatifs($nbJustificatifs);
        $qteDeFraisForfaitises = $ficheFrais->getLesQuantitesDeFraisForfaitises();

        // Ajout des frais hors forfait saisis par l'utilisateur
        for ($i = 0; $i < count($_POST['tabInfosFHF']); $i++) {
            $ficheFrais->ajouterUnFraisHorsForfait(
                    $_POST['tabInfosFHF'][$i]['hdHFNumFrais'],
                    $_POST['tabInfosFHF'][$i]['hdHFLibelle'],
                    $_POST['tabInfosFHF'][$i]['hdHFDate'],
                    $_POST['tabInfosFHF'][$i]['hdHFMontant'],
                    $_POST['tabInfosFHF'][$i]['rbHFAction']
            );
        }

        if ($ficheFrais->controlerNbJustificatifs()) {
            $ficheFrais->mettreAJourLesFraisHorsForfait();
        } else {
            echo "Le nombre de justificatifs est incorrect.";
        }

        // Récupération des frais forfaitisés
        $lignes = $ficheFrais->getLesFraisForfaitises();
        $etape = $lignes['ETP']->getQuantite();
        $kilometre = $lignes['KM']->getQuantite();
        $nuitee = $lignes['NUI']->getQuantite();
        $repas = $lignes['REP']->getQuantite();

        // Récupération des infos des frais hors forfait
        $lesInfosFraisHorsForfait = $ficheFrais->getLesInfosFraisHorsForfait();

        include "Vues/v_sommaire.php";

        echo '<div id="contenu">'
        . "<h2>Validation d'une fiche de frais visiteur</h2>"
        . "<br />";

        require "Vues/v_valideFraisChoixVisiteur.php";
        require "Vues/v_valideFraisCorpsFiche.php";

        echo '</div>';

        break;
    case 'validerFicheFrais':
        $nom = $_SESSION['nom'];
        $prenom = $_SESSION['prenom'];

        $selected = $_SESSION['idVisiteur'];
        $visiteurs = $pdo->getListeVisiteur();

        // Instantiation de la fiche de frais
        // et initialisation de la fiche de frais avec les infos de la base de données sans les frais hors forfait
        $ficheFrais = new FicheFrais($selected, $_SESSION['ficheMois']);
        $ficheFrais->initAvecInfosBDD();

        $libelleEtat = $ficheFrais->getLibelleEtat();
        $nbJustificatifs = $ficheFrais->getNbJustificatifs();
        $qteDeFraisForfaitises = $ficheFrais->getLesQuantitesDeFraisForfaitises();

        // Récupération des frais forfaitisés
        $lignes = $ficheFrais->getLesFraisForfaitises();
        $etape = $lignes['ETP']->getQuantite();
        $kilometre = $lignes['KM']->getQuantite();
        $nuitee = $lignes['NUI']->getQuantite();
        $repas = $lignes['REP']->getQuantite();

        // Récupération des infos des frais hors forfait
        $lesInfosFraisHorsForfait = $ficheFrais->getLesInfosFraisHorsForfait();
        $ficheFrais->valider();

        include "Vues/v_sommaire.php";
        
        echo '<p>La fiche de frais a été validé</p>';
        
        echo '<div id="contenu">'
        . "<h2>Validation d'une fiche de frais visiteur</h2>"
        . "<br />";

        require "Vues/v_valideFraisChoixVisiteur.php";
        require "Vues/v_valideFraisCorpsFiche.php";

        echo '</div>';

        break;
}
?>