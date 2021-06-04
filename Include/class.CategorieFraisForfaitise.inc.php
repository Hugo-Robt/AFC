<?php

require_once './Include/class.pdogsb.inc.php';

/**
 * Classe CategorieFraisForfaitise
 *
 */
class CategorieFraisForfaitise {

    private $idCategorie;
    private $libelle;
    private $montant;

    /**
     *
     * Constructeur de la classe CategorieFraisForfaitise.
     * En PHP il n'est pas possible de définir plusieurs versions d'un
     * constructeur avec des signatures différentes, comme en C# ou en Java.
     * Une astuce consiste donc à déclarer des paramètres avec une valeur par défaut.
     *
     * Ici les instanciations suivantes seront possibles :
     *   $uneCategorieFraisForfaitise = new CategorieFraisForfaitise('ETP');
     *   $uneCategorieFraisForfaitise = new CategorieFraisForfaitise('ETP', 'Forfait Etape');
     *   $uneCategorieFraisForfaitise = new CategorieFraisForfaitise('ETP', 'Forfait Etape', 110);
     *
     *
     * @param type $unIdCategorie
     * @param type $unLibelle
     * @param type $unMontant
     * @author C. Llorens <btsinfo@sfr.fr>
     *
     */
    public function __construct($unIdCategorie, $unLibelle = NULL, $unMontant = NULL) {

        if ($unLibelle === NULL || $unMontant === NULL) {
            $pdo = PdoGsb::getPdoGsb();
            $ligne = $pdo->getInfosCategorieFrais($unIdCategorie);
            $this->idCategorie = $unIdCategorie;
            $this->libelle = $ligne['CFF_LIBELLE'];
            $this->montant = $ligne['CFF_MONTANT'];
        } else {
            $this->idCategorie = $unIdCategorie;
            $this->libelle = $unLibelle;
            $this->montant = $unMontant;
        }
    }

    /*
     * Les accesseurs :
     */

    public function getId() {
        return $this->idCategorie;
    }

    public function getLibelle() {
        return $this->libelle;
    }

    public function getMontant() {
        return $this->montant;
    }

    public function getInfosCategorieFrais($idCategorieFicheFrais) {
        $req = "exec SP_CATEGORIE_FF_GET_INFOS :id";

        $rs = PdoGsb::$monPdo->prepare($req);
        $rs->bindParam(':id', $idCategorieFicheFrais);
        $rs->execute();
        
        $ligne = $rs->fetch();
        
        return $ligne;
    }
}
