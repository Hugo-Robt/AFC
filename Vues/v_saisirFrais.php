<div id="contenu">
    <h2>Validation d'une fiche de frais visiteur</h2>
    <br />

    <form name="frmChoixVisiteurMoisFiche" id="frmChoixVisiteurMoisFiche" method="post" action="choisirVisiteurMoisFiche">
        <?php
        echo formSelectDepuisRecordset('Visiteur', 'lstVisiteur', 'lstVisiteur', 5, $visiteurs, false)
        . formInputText('Mois', 'txtMois', 'txtMois', '', 10, 8, 10, true, false)
        . formBoutonSubmit('btnOk', 'btnOk', 'Ok', 15);
        ?>
    </form>

    <div class="encadre">
        <p>
            <?php
            echo formInputText('Etat de la fiche de frais', 'txtEtatFicheFrais', 'txtEtatFicheFrais', '', 10, 50, 20, true, false);
            ?>
        </p>

        <h2>Frais au forfait</h2>

        <form name="frmFraisForfait" id="frmFraisForfait" method="post" action="enregModifFF.php"
              onsubmit="return confirm('Voulez-vous réellement enregistrer les modifications apportées aux frais forfaitisés ?');">
            <table>
                <tr>
                    <th>Forfait étape</th>
                    <th>Frais kilométriques</th>
                    <th>Nuitée hôtel</th>
                    <th colspan='2'>Repas restaurant</th>
                </tr>
                <tr>
                    <td><?php echo formInputText('', 'txtForfaitEtape', 'txtForfaitEtape', '', 1, 100, 25, false, false); ?></td>
                    <td><?php echo formInputText('', 'txtFraisKilometriques', 'txtFraisKilometriques', '', 1, 100, 30, false, false); ?></td>
                    <td><?php echo formInputText('', 'txtNuiteeHotel', 'txtNuiteeHotel', '', 1, 100, 35, false, false); ?></td>
                    <td><?php echo formInputText('', 'txtRepasRestaurant', 'txtForfaitEtape', '', 1, 100, 40, false, false); ?></td>
                    <td>
                        <?php
                        echo formBoutonReset('btnEnregistrer', 'btnEnregistrer', 'Enregistrer', 50)
                        . formBoutonReset('btnReset', 'btnReset', 'Réinitialiser', 55);
                        ?>
                    </td>
                </tr>
                <tr>
                    <td><?php echo formInputText('', 'txtForfaitEtape', 'txtForfaitEtape', '', 1, 100, 25, false, false); ?></td>
                    <td><?php echo formInputText('', 'txtFraisKilometriques', 'txtFraisKilometriques', '', 1, 100, 30, false, false); ?></td>
                    <td><?php echo formInputText('', 'txtNuiteeHotel', 'txtNuiteeHotel', '', 1, 100, 35, false, false); ?></td>
                    <td><?php echo formInputText('', 'txtRepasRestaurant', 'txtForfaitEtape', '', 1, 100, 40, false, false); ?></td>
                    <td>
                        <?php
                        echo formBoutonReset('btnEnregistrer', 'btnEnregistrer', 'Enregistrer', 50)
                        . formBoutonReset('btnReset', 'btnReset', 'Réinitialiser', 55);
                        ?>
                    </td>
                </tr>
            </table>
        </form>

        <h2>Frais Hors forfait</h2>
    </div>
</div>