<?php

require_once './Include/class.pdogsb.inc.php';
require_once './Include/fct.inc.php';
require_once './Include/class.Frais.inc.php';

final class FicheFrais {

    private $idVisiteur;
    private $moisFiche;
    private $nbJustificatifs = 0;
    private $montantValide = 0;
    private $dateDerniereModif;
    private $idEtat;
    private $libelleEtat;
    static private $pdo;

    /**
     * On utilise 2 collections pour stocker les frais :
     * plus efficace car on doit extraire soit les FF soit les FHF.
     * Avec une seule collection on serait toujours obligé de parcourir et
     * de tester le type de tous les frais avant de les extraires.
     *
     */
    private $lesFraisForfaitises = []; // Un tableau asociatif de la forme : <idCategorie>, <objet FraisForfaitise>
    private $lesFraisHorsForfait = [];

    /**
     * Un tableau des numéros de ligne des frais forfaitisés.
     * Les lignes de frais forfaitisés sont numérotées en fonction de leur catégorie.
     * Le tableau est static ce qui évite de le déclarer dans chaque instance de
     * FicheFrais.
     *
     */
    static private $tabNumLigneFraisForfaitise = ['ETP' => 1,
        'KM' => 2,
        'NUI' => 3,
        'REP' => 4];

    function __construct($idVisiteur, $moisFicheFrais) {
        $this->idVisiteur = $idVisiteur;
        $this->moisFiche = $moisFicheFrais;
        self::$pdo = PdoGsb::getPdoGsb();
    }

    /**
     * Initialise une fiche de frais avec les infos de la base de données.
     */
    public function initAvecInfosBDD() {
        $ligne = self::$pdo->getInfosFiche($this->idVisiteur, $this->moisFiche);

        $this->initInfosFicheSansLesFrais($ligne['FICHE_NB_JUSTIFICATIFS'], $ligne['FICHE_MONTANT_VALIDE'], $ligne['FICHE_DATE_DERNIERE_MODIF'], $ligne['EFF_ID'], $ligne['EFF_LIBELLE']);
        $this->initLesFraisForfaitises();
        $this->initLesFraisHorsForfait();
    }

    /**
     * Initialise une fiche de frais avec les infos de la base de données sans les frais forfaitisés.
     */
    public function initAvecInfosBDDSansFF() {
        $ligne = self::$pdo->getInfosFiche($this->idVisiteur, $this->moisFiche);

        $this->initInfosFicheSansLesFrais($ligne['FICHE_NB_JUSTIFICATIFS'], $ligne['FICHE_MONTANT_VALIDE'], $ligne['FICHE_DATE_DERNIERE_MODIF'], $this->idEtat, $this->libelleEtat);
        $this->initLesFraisHorsForfait();
    }

    /**
     * Initialise une fiche de frais avec les infos de la base de données sans les frais hors forfait.
     */
    public function initAvecInfosBDDSansFHF() {
        $ligne = self::$pdo->getInfosFiche($this->idVisiteur, $this->moisFiche);

        $this->initInfosFicheSansLesFrais($ligne['FICHE_NB_JUSTIFICATIFS'], $ligne['FICHE_MONTANT_VALIDE'], $ligne['FICHE_DATE_DERNIERE_MODIF'], $this->idEtat, $this->libelleEtat);
        $this->initLesFraisForfaitises();
    }

    /**
     * Initialise les informations d'une fiche sans les frais.
     * @param int $nbJustificatifs
     * @param float $montantValide
     * @param data $dateDerniereModif
     * @param string $idEtat
     * @param string $libelleEtat
     */
    public function initInfosFicheSansLesFrais($nbJustificatifs, $montantValide, $dateDerniereModif, $idEtat, $libelleEtat) {
        $this->nbJustificatifs = $nbJustificatifs;
        $this->montantValide = $montantValide;
        $this->dateDerniereModif = $dateDerniereModif;
        $this->idEtat = $idEtat;
        $this->libelleEtat = $libelleEtat;
    }

    /**
     * Initialise de la collection des frais forfaitisé.
     */
    public function initLesFraisForfaitises() {
        $lignes = self::$pdo->getLignesFF($this->idVisiteur, $this->moisFiche);
        $categorie = null;

        foreach ($lignes as $uneLigne) {
            $categorie = new CategorieFraisForfaitise(trim($uneLigne['CFF_ID']), $uneLigne['CFF_LIBELLE'], $uneLigne['CFF_MONTANT']);
            $this->lesFraisForfaitises[trim($uneLigne['CFF_ID'])] = new FraisForfaitise($this->idVisiteur, $this->moisFiche, $uneLigne['FRAIS_NUM'], $uneLigne['LFF_QTE'], $categorie);
        }
    }

    /**
     * Initialise de la collection des frais hors forfait.
     */
    public function initLesFraisHorsForfait() {
        $lignes = self::$pdo->getLignesFHF($this->idVisiteur, $this->moisFiche);

        if ($lignes != null) {
            foreach ($lignes as $uneLigne) {
                $this->lesFraisHorsForfait[] = new FraisHorsForfait($this->idVisiteur, $this->moisFiche, $uneLigne['FRAIS_NUM'], $uneLigne['LFHF_LIBELLE'], $uneLigne['LFHF_DATE'], $uneLigne['LFHF_MONTANT']);
            }
        }
    }

    /**
     *
     * Ajoute à la fiche de frais un frais forfaitisé (une ligne) dont
     * l'id de la catégorie et la quantité sont passés en paramètre.
     * Le numéro de la ligne est automatiquement calculé à partir de l'id de
     * sa catégorie.
     *
     * @param string $idCategorie L'ide de la catégorie du frais forfaitisé.
     * @param int $quantite Le nombre d'unité(s).
     */
    public function ajouterUnFraisForfaitise($idCategorie, $quantite) {
        $this->lesFraisForfaitises[$idCategorie] = new FraisForfaitise($this->idVisiteur, $this->moisFiche, $this->getNumLigneFraisForfaitise($idCategorie), $quantite, $idCategorie);
    }

    /**
     *
     * Ajoute à la fiche de frais un frais forfaitisé (une ligne) dont
     * l'id de la catégorie et la quantité sont passés en paramètre.
     * Le numéro de la ligne est automatiquement calculé à partir de l'id de
     * sa catégorie.
     *
     * @param int $numFrais Le numéro de la ligne de frais hors forfait.
     * @param string $libelle Le libellé du frais.
     * @param string $date La date du frais, sous la forme AAAA-MM-JJ.
     * @param float $montant Le montant du frais.
     * @param string $action L'action à réaliser éventuellement sur le frais.
     */
    public function ajouterUnFraisHorsForfait($numFrais, $libelle, $date, $montant, $action) {
        $this->lesFraisHorsForfait[] = new FraisHorsForfait($this->idVisiteur, $this->moisFiche, $numFrais, $libelle, $date, $montant, $action);
    }

    /**
     *
     * Retourne la collection des frais forfaitisés de la fiche de frais.
     *
     * @return array La collections des frais forfaitisés.
     */
    public function getLesFraisForfaitises() {
        return $this->lesFraisForfaitises;
    }

    public function getLibelleEtat() {
        return $this->libelleEtat;
    }

    public function getNbJustificatifs() {
        return $this->nbJustificatifs;
    }

    /**
     *
     * Retourne un tableau contenant les quantités pour chaque ligne de frais
     * forfaitisé de la fiche de frais.
     *
     * @return array Le tableau demandé.
     */
    public function getLesQuantitesDeFraisForfaitises() {
        $quantites = [];

        foreach ($this->lesFraisForfaitises as $key => $obj) {
            $quantites[$key] = $obj->getQuantite();
        }

        return $quantites;
    }

    /**
     *
     * Retourne la collection des frais forfaitisés de la fiche de frais.
     *
     * @return array la collections des frais forfaitisés.
     */
    public function getLesFraisHorsForfait() {
        return $this->lesFraisHorsForfait;
    }

    /**
     *
     * Retourne un tableau associatif d'informations sur les frais forfaitisés
     * de la fiche de frais :
     * - le numéro du frais (numFrais),
     * - son libellé (libelle),
     * - sa date (date),
     * - son montant (montant).
     * - son action (action).
     * 
     * @return array Le tableau demandé.
     */
    public function getLesInfosFraisHorsForfait() {
        $infosFraisHorsForfait = [];

        // TODO: ICI
        foreach ($this->lesFraisHorsForfait as $fraisHorsForfait) {
            $infosFraisHorsForfait[] = array('numFrais' => $fraisHorsForfait->getNumFrais(), 'libelle' => $fraisHorsForfait->getLibelle(), 'date' => $fraisHorsForfait->getDate(), 'montant' => $fraisHorsForfait->getMontant(), 'action' => $fraisHorsForfait->getAction());
        }

        return $infosFraisHorsForfait;
    }

    /**
     *
     * Retourne le numéro de ligne d'un frais forfaitisé dont l'identifiant de
     * la catégorie est passé en paramètre.
     * Chaque fiche de frais comporte systématiquement 4 lignes de frais forfaitisés.
     * Chaque ligne de frais forfaitisé correspond à une catégorie de frais forfaitisé.
     * Les lignes de frais forfaitisés d'une fiche sont numérotées de 1 à 4.
     * Ce numéro dépend de la catégorie de frais forfaitisé :
     * - ETP : 1,
     * - KM  : 2,
     * - NUI : 3,
     * - REP : 4.
     *
     * @param string $idCategorieFraisForfaitise L'identifiant de la catégorie de frais forfaitisé.
     * @return int Le numéro de ligne du frais.
     *
     */
    private function getNumLigneFraisForfaitise($idCategorieFraisForfaitise) {
        return self::$tabNumLigneFraisForfaitise[$idCategorieFraisForfaitise];
    }

    /**
     *
     * Contrôle que les quantités de frais forfaitisés passées en paramètre
     * dans un tableau sont bien des numériques entiers et positifs.
     * Cette méthode s'appuie sur la fonction lesQteFraisValides().
     *
     * @return booléen Le résultat du contrôle.
     */
    public function controlerQtesFraisForfaitises() {
        return lesQteFraisValides($this->getLesQuantitesDeFraisForfaitises());
    }

    /**
     *
     * Met à jour dans la base de données les quantités des lignes de frais forfaitisées.
     *
     * @return bool Le résultat de la mise à jour.
     *
     */
    public function mettreAJourLesFraisForfaitises() {
        return self::$pdo->setLesQuantitesFraisForfaitises($this->idVisiteur, $this->moisFiche, $this->lesFraisForfaitises);
    }

    public function mettreAJourLesFraisHorsForfait() {
        $lesNouveauxFraisHorsForfait = [];
        
        foreach ($this->lesFraisHorsForfait as $unFraisHorsForfait) {
            $lesNouveauxFraisHorsForfait[] = array($unFraisHorsForfait->getNumFrais(), $unFraisHorsForfait->getAction());
        }
        
        return self::$pdo->setLesFraisHorsForfait($this->idVisiteur, $this->moisFiche, $lesNouveauxFraisHorsForfait, $this->nbJustificatifs);
    }

    /**
     * Écris un nouveau nombre de justificatifs.
     * @param string $nouveauNbJustificatifs Le nouveau nombre de justificatifs
     */
    public function setNbJustificatifs($nouveauNbJustificatifs) {
        $this->nbJustificatifs = $nouveauNbJustificatifs;
    }

    /**
     * Control si le nombre de justificatifs est un nombre positive
     * @return bool Si c'est vrai donc c'est un entier, si c'est faux ce n'est pas un entier
     */
    public function controlerNbJustificatifs() {
        return ($this->nbJustificatifs > 0 ? true : false);
    }

    /**
     * Calcule le montant total des frais validés
     * @return float Montant total des frais validés
     */
    public function calculerLeMontantValide() {
        $lesFrais = array_merge($this->lesFraisForfaitises, $this->lesFraisHorsForfait);

        foreach ($lesFrais as $unFrais) {
            $this->montantValide += $unFrais->getMontant();
        }

        return $this->montantValide;
    }

    /**
     * Valide une fiche de frais
     */
    public function valider() {
        $nouveauMontantValide = $this->calculerLeMontantValide();
        self::$pdo->validerFicheFrais($this->idVisiteur, $this->moisFiche, $nouveauMontantValide);
    }
}
