<!-- Division pour le sommaire -->
<div id="menuGauche">
    <div id="infosUtil">
        <h2>
            Agent comptable :<br />
            <?php echo $prenom . " " . $nom . "\n"; ?>
        </h2>
    </div>
    <ul id="menuList">
        <li>
            <a href="index.php?uc=validerFicheFrais&action=choixInitialVisiteur" title="Valider des fiches de frais">Valider des fiches de frais</a>
        </li>
        <li>
            <a href="index.php?uc=cloturerSaisieFichesFrais&action=confirmationCloture" title="Clôturer la saisie des fiches de frais">Clôturer la saisie des fiches de frais</a>
        </li>
        <li class="smenu">
            <a href="index.php?uc=connexion&action=deconnexion" title="Se déconnecter">Déconnexion</a>
        </li>
    </ul>
</div>
