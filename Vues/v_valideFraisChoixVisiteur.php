<form name="frmChoixVisiteurMoisFiche" id="frmChoixVisiteurMoisFiche" method="post" action="index.php?uc=validerFicheFrais&action=afficherFicheFraisSelectionnee">
    <?php
    echo formSelectDepuisRecordset('Visiteur', 'lstVisiteur', 'lstVisiteur', 5, $visiteurs, false, $selected)
    . formInputText('Mois', 'txtMois', 'txtMois', affichageMois(), 10, 8, 10, true, false)
    . formBoutonSubmit('btnOk', 'btnOk', 'Ok', 15);
    ?>
</form>