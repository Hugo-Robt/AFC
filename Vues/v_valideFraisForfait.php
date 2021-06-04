<h2>Frais au forfait</h2>

<form name="frmFraisForfait" id="frmFraisForfait" method="post"
      onsubmit="return confirm('Voulez-vous réellement enregistrer les modifications apportées aux frais forfaitisés ?');" action="index.php?uc=validerFicheFrais&action=enregModifFF">
    <table>
        <tr>
            <th>Forfait étape</th>
            <th>Frais kilométriques</th>
            <th>Nuitée hôtel</th>
            <th colspan='2'>Repas restaurant</th>
        </tr>
        <tr>
            <td><?php echo formInputText('', 'txtForfaitEtape', 'txtForfaitEtape', $etape, 1, 100, 25, false, false); ?></td>
            <td><?php echo formInputText('', 'txtFraisKilometriques', 'txtFraisKilometriques', $kilometre, 1, 100, 30, false, false); ?></td>
            <td><?php echo formInputText('', 'txtNuiteeHotel', 'txtNuiteeHotel', $nuitee, 1, 100, 35, false, false); ?></td>
            <td><?php echo formInputText('', 'txtRepasRestaurant', 'txtForfaitEtape', $repas, 1, 100, 40, false, false); ?></td>
            <td>
                <?php
                echo formBoutonReset('btnEnregistrer', 'btnEnregistrer', 'Enregistrer', 50)
                . formBoutonReset('btnReset', 'btnReset', 'Réinitialiser', 55);
                ?>
            </td>
        </tr>
    </table>
</form>