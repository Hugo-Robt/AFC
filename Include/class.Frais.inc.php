<?php

/**
 * Classe Frais
 *
 */
abstract class Frais {
    protected $idVisiteur;
    protected $moisFicheFrais;
    protected $numFrais;

    /**
     * Constructeur de la classe.
     *
     *  Rappel : en PHP le constructeur est toujours nommé
     *          __construct().
     *
     */
    public function __construct($unIdVisiteur, $unMoisFicheFrais, $unNumFrais) {
        $this->idVisiteur = $unIdVisiteur;
        $this->moisFicheFrais = $unMoisFicheFrais;
        $this->numFrais = $unNumFrais;
    }

    /**
     * Retourne l'id du visiteur.
     *
     * @return string L'id du visiteur.
     */
    public function getIdVisiteur() {
        return $this->idVisiteur;
    }

    /**
     * Retourne le mois de la fiche de frais.
     *
     * @return string Le mois de la fiche.
     */
    public function getMoisFiche() {
        return $this->moisFicheFrais;
    }

    /**
     * Retourne le numéro du frais (de la ligne).
     *
     * @return int Le numéro du frais.
     */
    public function getNumFrais() {
        return $this->numFrais;
    }

    abstract public function getMontant();
}

final class FraisForfaitise extends Frais {
    private $quantite;
    private $laCategorieFraisForfaitise;
    
    /**
     * Constructeur de la class FraisForfaitise
     * @param string $unIdVisiteur
     * @param string $unMoisFicheFrais
     * @param int $unNumFrais
     * @param int $quantite
     * @param CategorieFraisForfaitise $categorie
     */
    public function __construct($unIdVisiteur, $unMoisFicheFrais, $unNumFrais, $quantite, $categorie) {
        parent::__construct($unIdVisiteur, $unMoisFicheFrais, $unNumFrais);
        $this->quantite = $quantite;
        $this->laCategorieFraisForfaitise = $categorie;
    }

    public function getQuantite() {
        return $this->quantite;
    }

    public function getLaCategorieFraisForfaitise() {
        return $this->laCategorieFraisForfaitise;
    }
    
    public function getMontant() {
        return $this->laCategorieFraisForfaitise->getMontant() * $this->getQuantite();
    }
}

final class FraisHorsForfait extends Frais {
    private $libelle;
    private $date;
    private $montant;
    private $action;

    /**
     * Constructeur de class FraisHorsForfait
     * @param string $unIdVisiteur
     * @param string $unMoisFicheFrais
     * @param int $unNumFrais
     * @param string $libelle
     * @param data $date
     * @param float $montant
     * @param string $action
     */
    public function __construct($unIdVisiteur, $unMoisFicheFrais, $unNumFrais, $libelle, $date, $montant, $action = 'O') {
        parent::__construct($unIdVisiteur, $unMoisFicheFrais, $unNumFrais);
        $this->libelle = $libelle;
        $this->date = $date;
        $this->montant = $montant;
        $this->action = $action;
    }

    public function getLibelle() {
        return $this->libelle;
    }

    public function getDate() {
        return $this->date;
    }

    public function getMontant() {
        return $this->montant;
    }
    
    public function getAction() {
        return $this->action;
    }
}
