<form name="frmValiderFicheFrais" id="frmValiderFicheFrais" method="post" action="index.php?uc=validerFicheFrais&action=validerFicheFrais"
      onsubmit="return confirm('Voulez-vous réellement valider la fiche de frais ?');">

    <?php
        echo formBoutonSubmit('btnValiderFiche', 'btnValiderFiche', 'Valider la fiche de frais', 160);
    ?>
</form>