<?php

/**
 * Classe d'accès aux données.

 * Utilise les services de la classe PDO
 * pour l'application GSB
 * Les attributs sont tous statiques,
 * les 4 premiers pour la connexion
 * $monPdo de type PDO
 * $monPdoGsb qui contiendra l'unique instance de la classe

 * @package default
 * @author Cheri Bibi
 * @version    1.0
 * @link       http://www.php.net/manual/fr/book.pdo.php
 */
class PdoGsb {

    //        for ($i = 0; $i < count($lesLignes); $i++) {
//            $lesLignes[$i]['dt'] = dateAnglaisVersFrancais($lesLignes[$i]['dt']);
//        }
    // private static $serveur = 'sqlsrv:server=SVRSLAM01';
    private static $serveur = 'sqlsrv:server=SVRSLAM01';
    // private static $bdd='dbname=gsbV2';
    private static $bdd = 'Database=gsb_valide_rg';
    private static $user = 'afc_rg';
    private static $mdp = 'afc_rg';
    private static $monPdo;
    private static $monPdoGsb = null;
    public static $connectionPooling = 'connectionPooling=0';

    /**
     * Constructeur privé, crée l'instance de PDO qui sera sollicitée
     * pour toutes les méthodes de la classe
     *
     * @version 1.1 Utilise self:: en lieu et place de PdoGsb::
     *
     */
    private function __construct() {
        self::$monPdo = new PDO(self::$serveur . ';' . self::$bdd . ';' . self::$connectionPooling, self::$user, self::$mdp);
        self::$monPdo->query("SET CHARACTER SET utf8");
    }

    public function _destruct() {
        self::$monPdo = null;
    }

    /**
     * Fonction statique qui crée l'unique instance de la classe

     * Appel : $instancePdoGsb = PdoGsb::getPdoGsb();

     * @return l'unique objet de la classe PdoGsb
     *
     * @version 1.1 Utilise self:: en lieu et place de PdoGsb::
     *
     */
    public static function getPdoGsb() {
        if (self::$monPdoGsb == null) {
            self::$monPdoGsb = new PdoGsb();
        }
        return self::$monPdoGsb;
    }

    /**
     * Retourne les informations d'un visiteur

     * @param $login
     * @param $mdp
     * @return l'id, le nom et le prénom sous la forme d'un tableau associatif
     */
    public function getInfosVisiteur($login, $mdp) {
        $req = "select VISITEUR.VIS_ID as id, VISITEUR.VIS_NOM as nom, VISITEUR.VIS_PRENOM as prenom from VISITEUR
		where VISITEUR.VIS_LOGIN='$login' and VISITEUR.VIS_MDP='$mdp'";
        $rs = PdoGsb::$monPdo->query($req);
        $ligne = $rs->fetch();
        return $ligne;
    }

    /**
     * Retourne les informations d'un comptable

     * @param $login
     * @param $mdp
     * @return l'id, le nom et le prénom sous la forme d'un tableau associatif
     */
    public function getInfosComptable($login, $mdp) {
        $req = "exec GET_INFORMATIONS_COMPTABLE :login,:mdp";

        $rs = PdoGsb::$monPdo->prepare($req);
        $rs->bindParam(':login', $login);
        $rs->bindParam(':mdp', $mdp);
        $rs->execute();

        return ($rs->rowCount() != 0 ? $rs->fetchAll() : null);
    }

    public function getInfosFiche($idVisiteur, $mois) {
        $req = "exec GET_INFORMATIONS_FICHE_FRAIS :idVisiteur, :mois";

        $res = PdoGsb::$monPdo->prepare($req);
        $res->bindParam(':idVisiteur', $idVisiteur, PDO::PARAM_STR);
        $res->bindParam(':mois', $mois, PDO::PARAM_STR);
        $res->execute();

        return $res->fetch(PDO::FETCH_ASSOC);
    }

    public function getLignesFF($idVisiteur, $mois) {
        $req = "exec GET_LIGNES_FRAIS_FORFAITISE :idVisiteur, :mois";

        $res = PdoGsb::$monPdo->prepare($req);
        $res->bindParam(':idVisiteur', $idVisiteur);
        $res->bindParam(':mois', $mois);
        $res->execute();

        return $res->fetchAll();
    }

    public function getLignesFHF($idVisiteur, $mois) {
        $req = "exec GET_LIGNES_FRAIS_HORS_FORFAIT :idVisiteur, :mois";

        $res = PdoGsb::$monPdo->prepare($req);
        $res->bindParam(':idVisiteur', $idVisiteur);
        $res->bindParam(':mois', $mois);
        $res->execute();

        return ($res->rowCount() != 0 ? $res->fetchAll() : null);
    }

    /**
     * Retourne le nombre de justificatif d'un visiteur pour un mois donné

     * @param $idVisiteur
     * @param $mois sous la forme aaaamm
     * @return le nombre entier de justificatifs
     */
    public function getNbjustificatifs($idVisiteur, $mois) {
        $req = "select fichefrais.nbjustificatifs as nb from  fichefrais where fichefrais.idvisiteur ='$idVisiteur' and fichefrais.mois = '$mois'";
        $res = PdoGsb::$monPdo->query($req);
        $laLigne = $res->fetch();
        return $laLigne['nb'];
    }

    /**
     * Retourne tous les id de la table FraisForfait

     * @return un tableau associatif
     */
    public function getLesIdFrais() {
        $req = "select fraisforfait.id as idfrais from fraisforfait order by fraisforfait.id";
        $res = PdoGsb::$monPdo->query($req);
        $lesLignes = $res->fetchAll();
        return $lesLignes;
    }

    /**
     * Met à jour la table ligneFraisForfait

     * Met à jour la table ligneFraisForfait pour un visiteur et
     * un mois donné en enregistrant les nouveaux montants

     * @param $idVisiteur
     * @param $mois sous la forme aaaamm
     * @param $lesFrais tableau associatif de clé idFrais et de valeur la quantité pour ce frais
     * @return un tableau associatif
     */
    public function majFraisForfait($idVisiteur, $mois, $lesFrais) {
        $lesCles = array_keys($lesFrais);
        foreach ($lesCles as $unIdFrais) {
            $qte = $lesFrais[$unIdFrais];
            $req = "update lignefraisforfait set lignefraisforfait.quantite = $qte
			where lignefraisforfait.idvisiteur = '$idVisiteur' and lignefraisforfait.mois = '$mois'
			and lignefraisforfait.idfraisforfait = '$unIdFrais'";
            PdoGsb::$monPdo->exec($req);
        }
    }

    /**
     * met à jour le nombre de justificatifs de la table ficheFrais
     * pour le mois et le visiteur concerné

     * @param $idVisiteur
     * @param $mois sous la forme aaaamm
     */
    public function majNbJustificatifs($idVisiteur, $mois, $nbJustificatifs) {
        $req = "update fichefrais set nbjustificatifs = $nbJustificatifs
		where fichefrais.idvisiteur = '$idVisiteur' and fichefrais.mois = '$mois'";
        PdoGsb::$monPdo->exec($req);
    }

    /**
     * Teste si un visiteur possède une fiche de frais pour le mois passé en argument

     * @param $idVisiteur
     * @param $mois sous la forme aaaamm
     * @return vrai ou faux
     */
    public function estPremierFraisMois($idVisiteur, $mois) {
        $ok = false;
        $req = "select count(*) as nblignesfrais from fichefrais
		where fichefrais.mois = '$mois' and fichefrais.idvisiteur = '$idVisiteur'";
        $res = PdoGsb::$monPdo->query($req);
        $laLigne = $res->fetch();
        if ($laLigne['nblignesfrais'] == 0) {
            $ok = true;
        }
        return $ok;
    }

    /**
     * Retourne le dernier mois en cours d'un visiteur

     * @param $idVisiteur
     * @return le mois sous la forme aaaamm
     */
    public function dernierMoisSaisi($idVisiteur) {
        $req = "select max(mois) as dernierMois from fichefrais where fichefrais.idvisiteur = '$idVisiteur'";
        $res = PdoGsb::$monPdo->query($req);
        $laLigne = $res->fetch();
        $dernierMois = $laLigne['dernierMois'];
        return $dernierMois;
    }

    /**
     * Crée une nouvelle fiche de frais et les lignes de frais au forfait pour un visiteur et un mois donnés

     * récupère le dernier mois en cours de traitement, met à 'CL' son champs idEtat, crée une nouvelle fiche de frais
     * avec un idEtat à 'CR' et crée les lignes de frais forfait de quantités nulles
     * @param $idVisiteur
     * @param $mois sous la forme aaaamm
     */
    public function creeNouvellesLignesFrais($idVisiteur, $mois) {
        $dernierMois = $this->dernierMoisSaisi($idVisiteur);
        $laDerniereFiche = $this->getLesInfosFicheFrais($idVisiteur, $dernierMois);
        if ($laDerniereFiche['idEtat'] == 'CR') {
            $this->majEtatFicheFrais($idVisiteur, $dernierMois, 'CL');
        }
        $req = "insert into fichefrais(idvisiteur,mois,nbJustificatifs,montantValide,dateModif,idEtat)
		values('$idVisiteur','$mois',0,0,now(),'CR')";
        PdoGsb::$monPdo->exec($req);
        $lesIdFrais = $this->getLesIdFrais();
        foreach ($lesIdFrais as $uneLigneIdFrais) {
            $unIdFrais = $uneLigneIdFrais['idfrais'];
            $req = "insert into lignefraisforfait(idvisiteur,mois,idFraisForfait,quantite)
			values('$idVisiteur','$mois','$unIdFrais',0)";
            PdoGsb::$monPdo->exec($req);
        }
    }

    /**
     * Crée un nouveau frais hors forfait pour un visiteur un mois donné
     * à partir des informations fournies en paramètre

     * @param $idVisiteur
     * @param $mois sous la forme aaaamm
     * @param $libelle : le libelle du frais
     * @param $date : la date du frais au format français jj//mm/aaaa
     * @param $montant : le montant
     */
    public function creeNouveauFraisHorsForfait($idVisiteur, $mois, $libelle, $date, $montant) {
        $dateFr = dateFrancaisVersAnglais($date);
        $req = "insert into lignefraishorsforfait
		values('','$idVisiteur','$mois','$libelle','$dateFr','$montant')";
        PdoGsb::$monPdo->exec($req);
    }

    /**
     * Supprime le frais hors forfait dont l'id est passé en argument

     * @param $idFrais
     */
    public function supprimerFraisHorsForfait($idFrais) {
        $req = "delete from lignefraishorsforfait where lignefraishorsforfait.id =$idFrais ";
        PdoGsb::$monPdo->exec($req);
    }

    /**
     * Retourne les mois pour lesquel un visiteur a une fiche de frais

     * @param $idVisiteur
     * @return un tableau associatif de clé un mois -aaaamm- et de valeurs l'année et le mois correspondant
     */
    public function getLesMoisDisponibles($idVisiteur) {
        $req = "select fichefrais.mois as mois from  fichefrais where fichefrais.idvisiteur ='$idVisiteur'
		order by fichefrais.mois desc ";
        $res = PdoGsb::$monPdo->query($req);
        $lesMois = array();
        $laLigne = $res->fetch();
        while ($laLigne != null) {
            $mois = $laLigne['mois'];
            $numAnnee = substr($mois, 0, 4);
            $numMois = substr($mois, 4, 2);
            $lesMois["$mois"] = array(
                "mois" => "$mois",
                "numAnnee" => "$numAnnee",
                "numMois" => "$numMois"
            );
            $laLigne = $res->fetch();
        }
        return $lesMois;
    }

    /**
     * Retourne les informations d'une fiche de frais d'un visiteur pour un mois donné

     * @param $idVisiteur
     * @param $mois sous la forme aaaamm
     * @return un tableau avec des champs de jointure entre une fiche de frais et la ligne d'état
     */
    public function getLesInfosFicheFrais($idVisiteur, $mois) {
        $res = PdoGsb::$monPdoGsb->prepare('exec INFORMATIONS_FICHE_FRAIS :id, :mois');
        $res->bindParam(':id', $idVisiteur);
        $res->bindParam(':mois', $mois);
        $res->setFetchMode(PDO::FETCH_ASSOC);
        $res->execute();
        $laLigne = $res->fetch();
        return $laLigne;
    }

    /**
     * Modifie l'état et la date de modification d'une fiche de frais

     * Modifie le champ idEtat et met la date de modif à aujourd'hui
     * @param $idVisiteur
     * @param $mois sous la forme aaaamm
     */
    public function majEtatFicheFrais($idVisiteur, $mois, $etat) {
        $req = "update ficheFrais set idEtat = '$etat', dateModif = now()
		where fichefrais.idvisiteur ='$idVisiteur' and fichefrais.mois = '$mois'";
        PdoGsb::$monPdo->exec($req);
    }

    /**
     *
     * Met à jour dans la base de données les quantités des lignes de frais forfaitisées
     * pour la fiche de frais dont l'id du visiteur et le mois de la fiche sont passés en paramètre.
     * Une transaction est utilisée pour garantir que toutes les mises à jour ont bien abouti, ou aucune.
     * 
     * @param string $unIdVisiteur L'id du visiteur.
     * @param string $unMois Le mois de la fiche de frais.
     * @param array $lesFraisForfaitises Un tableau à 2 dimensions contenant pour chaque frais forfaitisé
     * le numéro de ligne et la quantité.
     * @return boolean Le résultat de la mise à jour.
     */
    public function setLesQuantitesFraisForfaitises($unIdVisiteur, $unMois, $lesFraisForfaitises) {
        $res = PdoGsb::$monPdo->prepare('exec SP_LIGNE_FF_MAJ :idVisiteur, :mois, :numFrais, :quantite');

        // Ces valeurs sont connues et ne changerons pas
        $res->bindValue(':idVisiteur', $unIdVisiteur, PDO::PARAM_STR);
        $res->bindValue(':mois', $unMois, PDO::PARAM_STR);

        try {
            self::$monPdo->beginTransaction();

            foreach ($lesFraisForfaitises as $unFraisForfaitise) {
                $res->bindParam(':numFrais', $unFraisForfaitise->getNumFrais(), PDO::PARAM_INT);
                $res->bindParam(':quantite', $unFraisForfaitise->getQuantite(), PDO::PARAM_INT);
                $res->execute();
            }

            self::$monPdo->commit();
        } catch (PDOException $e) {
            echo '<p>' . $e->getMessage() . '</p>';
            self::$monPdo->rollback();
        }
    }

    /**
     *
     * Met à jour les frais hors forfait dans la base de données.
     * La mise à jour consiste à :
     * - reporter ou supprimer certaine(s) ligne(s) des frais hors forfait ;
     * - mettre à jour le nombre de justificatifs pris en compte.
     * Une transaction est utilisée pour assurer la cohérence des données.
     * 
     * @param string $unIdVisiteur L'id du visiteur.
     * @param string $unMois Le mois de la fiche de frais.
     * @param array $lesFraisHorsForfait Un tableau à 2 dimensions contenant
     * pour chaque frais hors forfait le numéro de ligne et l'action (R ou S) à effectuer.
     * @param type $nbJustificatifsPEC Le nombre de justificatifs pris en compte.
     * @return bool Le résultat de la mise à jour (TRUE : ok ; FALSE : pas ok).
     */
    public function setLesFraisHorsForfait($unIdVisiteur, $unMois, $lesNouveauxFraisHorsForfait, $nbJustificatifsPEC) {
        $res = false;
        $reqSupp = "EXEC dbo.SP_LIGNE_FHF_SUPPRIME :idVisiteur, :mois, :fraisNum";
        $reqRepo = "EXEC dbo.SP_LIGNE_FHF_REPORTE :idVisiteur, :mois, :fraisNum";
        $reqJust = "EXEC dbo.SP_FICHE_NB_JPEC_MAJ :idVisiteur, :mois, :nouvNB";

        // Suppression
        $sttmtSupp = self::$monPdo->prepare($reqSupp);
        $sttmtSupp->bindParam(':idVisiteur', $unIdVisiteur, PDO::PARAM_STR);
        $sttmtSupp->bindParam(':mois', $unMois, PDO::PARAM_STR);

        // Report
        $sttmtRepo = self::$monPdo->prepare($reqRepo);
        $sttmtRepo->bindParam(':idVisiteur', $unIdVisiteur, PDO::PARAM_STR);
        $sttmtRepo->bindParam(':mois', $unMois, PDO::PARAM_STR);

        // MAJ Nombre de justificatifs
        $sttmtJust = self::$monPdo->prepare($reqJust);
        $sttmtJust->bindParam(':idVisiteur', $unIdVisiteur, PDO::PARAM_STR);
        $sttmtJust->bindParam(':mois', $unMois, PDO::PARAM_STR);
        $sttmtJust->bindParam(':nouvNB', $nbJustificatifsPEC, PDO::PARAM_INT);

        try {
            // self::$monPdo->beginTransaction();
            self::$monPdo->query('begin transaction');
            foreach ($lesNouveauxFraisHorsForfait as $unNouveauFrais) {
                $sttmtSupp->bindParam(':fraisNum', $unNouveauFrais[0], PDO::PARAM_INT);
                $sttmtRepo->bindParam(':fraisNum', $unNouveauFrais[0], PDO::PARAM_INT);

                switch ($unNouveauFrais[1]) {
                    case 'S': // Suppression
                        $sttmtSupp->execute();
                        break;
                    case 'R': // Report
                        $sttmtRepo->execute();
                        break;
                    default:
                        break;
                }
            }

            $sttmtJust->execute();
            $res = true;
            // self::$monPdo->commit();
            self::$monPdo->query('commit');
        } catch (Exception $ex) {
            echo $ex->getMessage();
            // self::$monPdo->rollBack();
            self::$monPdo->query('rollback');
        }
        return $res;
    }

    public function getListeVisiteur() {
        $r = PdoGsb::$monPdo->prepare('exec GET_LISTE_VISITEUR');
        $r->execute();
        return $r;
    }

    /**
     * Valide une fiche frais dans la base de données
     * @param string $idVisiteur L'id du visiteur sélectionner
     * @param string $mois Le mois de la fiche du visiteur sélectionner
     * @param int $montantValide Le montant total de la fiche
     */
    public function validerFicheFrais($idVisiteur, $mois, $montantValide) {
        $res = self::$monPdo->prepare('exec SP_FICHE_FRAIS :idVisiteur, :mois, :montantValide');
        $res->bindParam(':idVisiteur', $idVisiteur, PDO::PARAM_STR);
        $res->bindParam(':mois', $mois, PDO::PARAM_STR);
        $res->bindParam(':montantValide', $montantValide, PDO::PARAM_INT);
        $res->execute();
    }

    /**
     * Récupère le nombre de fiches clôturer par rapport au mois passé selectionner
     * @param string $moisPasse Le mois passé
     */
    public function getNombreFichesCloturer($moisPasse) {
        $res = self::$monPdo->prepare('exec F_FICHE_A_CLOTURER_NB :moisPasse');
        $res->bindParam(':moisPasse', $moisPasse, PDO::PARAM_STR);
        $res->execute();
        return $res;
    }

    /**
     * Clôture les fiches du mois passé et récupère le nombre de fiches clôturer
     * @param string $moisPasse Le mois passé
     * @return int Le nombre de fiches cloturer
     */
    public function cloturerLesFiches($moisPasse) {
        $res = self::$monPdo->prepare('exec F_FICHE_A_CLOTURER_CR :moisPasse');
        $res->bindParam(':moisPasse', $moisPasse, PDO::PARAM_STR);
        $res->execute();
        return $this->getNombreFichesCloturer($moisPasse);
    }

}
?>