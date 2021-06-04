<h2>Frais hors forfait</h2>

<form name="frmFraisHorsForfait" id="frmFraisHorsForfait" method="post" action="index.php?uc=validerFicheFrais&action=enregModifFHF"
      onsubmit="return confirm('Voulez-vous réellement enregistrer les modifications apportées aux frais hors forfait ?');">
    <?php
    if (!empty($lesInfosFraisHorsForfait)) {
    ?>
    <table>
        <tr>
            <th>Date</th>
            <th>Libellé</th>
            <th>Montant</th>
            <th>Ok</th>
            <th>Reporter</th>
            <th>Supprimer</th>
        </tr>
        <?php
            for ($i = 0; $i < count($lesInfosFraisHorsForfait); $i++) {
                ?>
                <tr>
                    <td><?php echo formInputText('', 'txtHFDate' . $i, 'txtHFDate' . $i, $lesInfosFraisHorsForfait[$i]['date'], 12, 255, 0, true, false); ?></td>
                    <td><?php echo formInputText('', 'txtHFLibelle' . $i, 'txtHFLibelle' . $i, $lesInfosFraisHorsForfait[$i]['libelle'], 50, 255, 0, true, false); ?></td>
                    <td><?php echo formInputText('', 'txtHFMontant' . $i, 'txtHFMontant' . $i, $lesInfosFraisHorsForfait[$i]['montant'], 10, 255, 0, true, false); ?></td>


                    <td><?php echo formInputRadio(false, '', 'tabInfosFHF[' . $i .'][rbHFAction]', '', 'O', true, 70, false); ?></td>
                    <td><?php echo formInputRadio(false, '', 'tabInfosFHF[' . $i .'][rbHFAction]', '', 'R', false, 80, false); ?></td>
                    <td><?php echo formInputRadio(false, '', 'tabInfosFHF[' . $i .'][rbHFAction]', '', 'S', false, 90, false); ?></td>

                    <?php echo formInputHidden('tabInfosFHF[' . $i . '][hdHFNumFrais]', '', $lesInfosFraisHorsForfait[$i]['numFrais']); ?>
                    <?php echo formInputHidden('tabInfosFHF[' . $i . '][hdHFDate]', '', $lesInfosFraisHorsForfait[$i]['date']); ?>
                    <?php echo formInputHidden('tabInfosFHF[' . $i . '][hdHFLibelle]', '', $lesInfosFraisHorsForfait[$i]['libelle']); ?>
                    <?php echo formInputHidden('tabInfosFHF[' . $i . '][hdHFMontant]', '', $lesInfosFraisHorsForfait[$i]['montant']); ?>
                </tr>
                <?php
            }
        ?>
    </table>
    <br />
    <?php
    echo formInputText('Nb de justificatifs pris en compte', 'txtHFNbJustificatifsPEC', 'txtHFNbJustificatifsPEC', $nbJustificatifs, 4, 255, 130, false, false) . '<br /><br />'
    . formBoutonSubmit('btnEnregistrerModifFHF', 'btnEnregistrerModifFHF', 'Enregistrer les modifications des lignes hors forfait', 140)
    . formBoutonReset('btnReinitialiserFHF', 'btnReinitialiserFHF', 'Réinitialiser', 150);
    } else {
        echo '<p>Pas de frais hors forfait</p>';
    }
    ?>
</form>