<?php
/**
 * Classe d'accès aux données.
 *
 * PHP Version 7
 *
 * @category  PPE
 * @package   GSB
 * @author    Cheri Bibi - Réseau CERTA <contact@reseaucerta.org>
 * @author    José GIL - CNED <jgil@ac-nice.fr>
 * @copyright 2017 Réseau CERTA
 * @license   Réseau CERTA
 * @version   GIT: <0>
 * @link      http://www.php.net/manual/fr/book.pdo.php PHP Data Objects sur php.net
 */

/**
 * Classe d'accès aux données.
 *
 * Utilise les services de la classe PDO
 * pour l'application GSB
 * Les attributs sont tous statiques,
 * les 4 premiers pour la connexion
 * $monPdo de type PDO
 * $monPdoGsb qui contiendra l'unique instance de la classe
 *
 * PHP Version 7
 *
 * @category  PPE
 * @package   GSB
 * @author    Cheri Bibi - Réseau CERTA <contact@reseaucerta.org>
 * @author    José GIL <jgil@ac-nice.fr>
 * @copyright 2017 Réseau CERTA
 * @license   Réseau CERTA
 * @version   Release: 1.0
 * @link      http://www.php.net/manual/fr/book.pdo.php PHP Data Objects sur php.net
 */

class PdoGsb
{
    private static $serveur = 'mysql:host=localhost';
    private static $bdd = 'dbname=gsb_frais';
    private static $user = 'userGsb';
    private static $mdp = 'secret';
    private static $monPdo;
    private static $monPdoGsb = null;

    /**
     * Constructeur privé, crée l'instance de PDO qui sera sollicitée
     * pour toutes les méthodes de la classe
     */
    private function __construct()
    {
        PdoGsb::$monPdo = new PDO(
            PdoGsb::$serveur . ';' . PdoGsb::$bdd,
            PdoGsb::$user,
            PdoGsb::$mdp
        );
        PdoGsb::$monPdo->query('SET CHARACTER SET utf8');
    }

    /**
     * Méthode destructeur appelée dès qu'il n'y a plus de référence sur un
     * objet donné, ou dans n'importe quel ordre pendant la séquence d'arrêt.
     */
    public function __destruct()
    {
        PdoGsb::$monPdo = null;
    }

    /**
     * Fonction statique qui crée l'unique instance de la classe
     * Appel : $instancePdoGsb = PdoGsb::getPdoGsb();
     *
     * @return l'unique objet de la classe PdoGsb
     */
    public static function getPdoGsb()
    {
        if (PdoGsb::$monPdoGsb == null) {
            PdoGsb::$monPdoGsb = new PdoGsb();
        }
        return PdoGsb::$monPdoGsb;
    }

    /**
     * Retourne les informations d'un users
     *
     * @param String $login Login du user
     * @param String $mdp   Mot de passe du user
     *
     * @return l'id, le nom et le prénom sous la forme d'un tableau associatif
     */
    public function getInfosUser($login, $mdp)
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'SELECT user.id AS id, user.nom AS nom, '
            . 'user.prenom AS prenom, '
            . 'user.typepop AS typepop, '
            . 'user.mdp AS mdp, '
            . 'user.cp AS cp, '
            . 'user.adresse AS adresse '
            . 'FROM user '
            . 'WHERE user.login = :unLogin'
        );
        $requetePrepare->bindParam(':unLogin', $login, PDO::PARAM_STR);
        $requetePrepare->execute();
        $unUser = $requetePrepare->fetch();
        if ($unUser['mdp'] == hash("sha256", $unUser['cp'].$mdp.$unUser['adresse'])) {
            return $unUser;
        }
    }

    /**
     * Retourne sous forme d'un tableau associatif toutes les lignes de frais
     * hors forfait concernées par les deux arguments.
     * La boucle foreach ne peut être utilisée ici car on procède
     * à une modification de la structure itérée - transformation du champ date-
     *
     * @param String $idVisiteur ID du user
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return [] tous les champs des lignes de frais hors forfait sous la forme
     * d'un tableau associatif
     */
    public function getLesFraisHorsForfait($idVisiteur, $mois)
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'SELECT * FROM lignefraishorsforfait '
            . 'WHERE lignefraishorsforfait.idvisiteur = :unIdUser '
            . 'AND lignefraishorsforfait.mois = :unMois'
        );
        $requetePrepare->bindParam(':unIdUser', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $lesLignes = $requetePrepare->fetchAll();
        for ($i = 0; $i < count($lesLignes); $i++) {
            $date = $lesLignes[$i]['date'];
            $lesLignes[$i]['date'] = dateAnglaisVersFrancais($date);
        }
        return $lesLignes;
    }

    /**
     * Retourne le nombre de justificatif d'un user pour un mois donné
     *
     * @param String $idVisiteur ID du user
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return integer le nombre entier de justificatifs
     */
    public function getNbjustificatifs($idVisiteur, $mois)
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'SELECT fichefrais.nbjustificatifs as nb FROM fichefrais '
            . 'WHERE fichefrais.idvisiteur = :unIdUser '
            . 'AND fichefrais.mois = :unMois'
        );
        $requetePrepare->bindParam(':unIdUser', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $laLigne = $requetePrepare->fetch();
        return $laLigne['nb'];
    }

    /**
     * Retourne sous forme d'un tableau associatif toutes les lignes de frais
     * au forfait concernées par les deux arguments
     *
     * @param String $idVisiteur ID du user
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return l'id, le libelle et la quantité sous la forme d'un tableau
     * associatif
     */
    public function getLesFraisForfait($idVisiteur, $mois, $param=1, $lnChaine=NULL)
    {   
        if ($param == 1) {
            if (is_null($lnChaine)) {        
                $requetePrepare = PdoGSB::$monPdo->prepare(
                    'SELECT fraisforfait.id as idfrais, '
                    . 'fraisforfait.libelle as libelle, '
                    . 'lignefraisforfait.quantite as quantite, '
                    . 'fraisforfait.montant as montant '
                    . 'FROM lignefraisforfait '
                    . 'INNER JOIN fraisforfait '
                    . 'ON fraisforfait.id = lignefraisforfait.idfraisforfait '
                    . 'WHERE lignefraisforfait.idvisiteur = :unIdUser '
                    . 'AND lignefraisforfait.mois = :unMois '
                    . 'AND lignefraisforfait.quantite is not null '
                    . 'ORDER BY lignefraisforfait.idfraisforfait'
                );
            } else {
                $requetePrepare = PdoGSB::$monPdo->prepare(
                    'SELECT fraisforfait.id as idfrais, '
                    . 'fraisforfait.libelle as libelle, '
                    . 'lignefraisforfait.quantite as quantite, '
                    . 'fraisforfait.montant as montant '
                    . 'FROM lignefraisforfait '
                    . 'INNER JOIN fraisforfait '
                    . 'ON fraisforfait.id = lignefraisforfait.idfraisforfait '
                    . 'WHERE lignefraisforfait.idvisiteur = :unIdUser '
                    . 'AND lignefraisforfait.mois = :unMois '
                    . 'AND LENGTH(fraisforfait.id) = :longueurChaine '
                    . 'AND lignefraisforfait.quantite is not null '
                    . 'ORDER BY lignefraisforfait.idfraisforfait'
                );
                $requetePrepare->bindParam(':longueurChaine', $lnChaine, PDO::PARAM_STR);
            }
        } else {
            if (is_null($lnChaine)) {
                $requetePrepare = PdoGSB::$monPdo->prepare(
                    'SELECT fraisforfait.id as idfrais, '
                    . 'fraisforfait.libelle as libelle, '
                    . 'lignefraisforfait.quantite as quantite, '
                    . 'fraisforfait.montant as montant '
                    . 'FROM lignefraisforfait '
                    . 'INNER JOIN fraisforfait '
                    . 'ON fraisforfait.id = lignefraisforfait.idfraisforfait '
                    . 'WHERE lignefraisforfait.idvisiteur = :unIdUser '
                    . 'AND lignefraisforfait.mois = :unMois '
                    . 'ORDER BY lignefraisforfait.idfraisforfait'
                    );
            } else {
                $requetePrepare = PdoGSB::$monPdo->prepare(
                    'SELECT fraisforfait.id as idfrais, '
                    . 'fraisforfait.libelle as libelle, '
                    . 'lignefraisforfait.quantite as quantite, '
                    . 'fraisforfait.montant as montant '
                    . 'FROM lignefraisforfait '
                    . 'INNER JOIN fraisforfait '
                    . 'ON fraisforfait.id = lignefraisforfait.idfraisforfait '
                    . 'WHERE lignefraisforfait.idvisiteur = :unIdUser '
                    . 'AND lignefraisforfait.mois = :unMois '
                    . 'AND LENGTH(fraisforfait.id) = :longueurChaine '
                    . 'AND lignefraisforfait.quantite is not null '
                    . 'ORDER BY lignefraisforfait.idfraisforfait'
                    );
                $requetePrepare->bindParam(':longueurChaine', $lnChaine, PDO::PARAM_STR);
            }
        }
        $requetePrepare->bindParam(':unIdUser', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        return $requetePrepare->fetchAll();
    }

    /**
     * Retourne tous les id de la table FraisForfait
     *
     * @return [] un tableau associatif
     */
    public function getLesIdFrais()
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'SELECT fraisforfait.id as idfrais '
            . 'FROM fraisforfait ORDER BY fraisforfait.id'
        );
        $requetePrepare->execute();
        return $requetePrepare->fetchAll();
    }

    /**
     * Met à jour la table ligneFraisForfait
     * Met à jour la table ligneFraisForfait pour un user et
     * un mois donné en enregistrant les nouveaux montants
     *
     * @param String $idVisiteur ID du user
     * @param String $mois       Mois sous la forme aaaamm
     * @param Array  $lesFrais   tableau associatif de clé idFrais et
     *                           de valeur la quantité pour ce frais
     *
     * @return null
     */
    public function majFraisForfait($idVisiteur, $mois, $lesFrais)
    {
        $lesCles = array_keys($lesFrais);
        foreach ($lesCles as $unIdFrais) {
            $qte = $lesFrais[$unIdFrais];
            if ($qte == ""){
                
            } else {
                $requetePrepare = PdoGSB::$monPdo->prepare(
                    'UPDATE lignefraisforfait '
                    . 'SET lignefraisforfait.quantite = :uneQte '
                    . 'WHERE lignefraisforfait.idvisiteur = :unIdUser '
                    . 'AND lignefraisforfait.mois = :unMois '
                    . 'AND lignefraisforfait.idfraisforfait = :idFrais'
                );
                $requetePrepare->bindParam(':uneQte', $qte, PDO::PARAM_INT);
                $requetePrepare->bindParam(':unIdUser', $idVisiteur, PDO::PARAM_STR);
                $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
                $requetePrepare->bindParam(':idFrais', $unIdFrais, PDO::PARAM_STR);
                $requetePrepare->execute();
            }
        }
    }
    
    /**
     * Reporte au mois suivant le frais hors forfait pour un id donné.
     * 
     * @param integer $idFrais id de la ligne de frais hors forfait
     * @param string $selectVisiteur id du visiteur
     * @param integer $selectMois mois sous la forme aaaamm
     * 
     * @return null
     */
    public function reporterFraisHorsForfait($idFrais, $selectVisiteur, $selectMois)
    {   
        $mois = $selectMois + 1;
        if ($this->estPremierFraisMois($selectVisiteur, $mois)) {
            $this->creeNouvellesLignesFrais($selectVisiteur, $mois);
        }
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'UPDATE lignefraishorsforfait '
            . 'SET lignefraishorsforfait.mois = :unMois '
            . 'WHERE lignefraishorsforfait.id = :unId '
            );
        $requetePrepare->bindParam(':unId', $idFrais, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $this->majLibelleFraisHorsForfait($idFrais, 'REPORTE');
    }

    /**
     * Met à jour le nombre de justificatifs de la table ficheFrais
     * pour le mois et le user concerné
     *
     * @param String  $idVisiteur      ID du user
     * @param String  $mois            Mois sous la forme aaaamm
     * @param Integer $nbJustificatifs Nombre de justificatifs
     *
     * @return null
     */
    public function majNbJustificatifs($idVisiteur, $mois, $nbJustificatifs)
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'UPDATE fichefrais '
            . 'SET nbjustificatifs = :unNbJustificatifs, datemodif = now() '
            . 'WHERE fichefrais.idvisiteur = :unIdUser '
            . 'AND fichefrais.mois = :unMois'
        );
        $requetePrepare->bindParam(
            ':unNbJustificatifs',
            $nbJustificatifs,
            PDO::PARAM_INT
        );
        $requetePrepare->bindParam(':unIdUser', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
    }

    /**
     * Teste si un user possède une fiche de frais pour le mois passé en argument
     *
     * @param String $idVisiteur ID du user
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return boolean vrai ou faux
     */
    public function estPremierFraisMois($idVisiteur, $mois)
    {
        $boolReturn = false;
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'SELECT fichefrais.mois FROM fichefrais '
            . 'WHERE fichefrais.mois = :unMois '
            . 'AND fichefrais.idvisiteur = :unIdUser'
        );
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unIdUser', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->execute();
        if (!$requetePrepare->fetch()) {
            $boolReturn = true;
        }
        return $boolReturn;
    }

    /**
     * Retourne le dernier mois en cours d'un user
     *
     * @param String $idVisiteur ID du user
     *
     * @return DateTime le mois sous la forme aaaamm
     */
    public function dernierMoisSaisi($idVisiteur)
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'SELECT MAX(mois) as dernierMois '
            . 'FROM fichefrais '
            . 'WHERE fichefrais.idvisiteur = :unIdUser'
        );
        $requetePrepare->bindParam(':unIdUser', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->execute();
        $laLigne = $requetePrepare->fetch();
        $dernierMois = $laLigne['dernierMois'];
        return $dernierMois;
    }

    /**
     * Crée une nouvelle fiche de frais et les lignes de frais au forfait
     * pour un user et un mois donnés
     *
     * Récupère le dernier mois en cours de traitement, met à 'CL' son champs
     * idEtat, crée une nouvelle fiche de frais avec un idEtat à 'CR' et crée
     * les lignes de frais forfait de quantités nulles
     *
     * @param String $idVisiteur ID du user
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return null
     */
    public function creeNouvellesLignesFrais($idVisiteur, $mois)
    {
        $dernierMois = $this->dernierMoisSaisi($idVisiteur);
        $laDerniereFiche = $this->getLesInfosFicheFrais($idVisiteur, $dernierMois);
        if ($laDerniereFiche['idEtat'] == 'CR') {
            $this->majEtatFicheFrais($idVisiteur, $dernierMois, 'CL');
        }
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'INSERT INTO fichefrais (idvisiteur,mois,nbjustificatifs,'
            . 'montantvalide,datemodif,idetat) '
            . "VALUES (:unIdUser,:unMois,0,0,now(),'CR')"
        );
        $requetePrepare->bindParam(':unIdUser', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $lesIdFrais = $this->getLesIdFrais();
        foreach ($lesIdFrais as $unIdFrais) {
            $requetePrepare = PdoGsb::$monPdo->prepare(
                'INSERT INTO lignefraisforfait (idvisiteur,mois,'
                . 'idfraisforfait,quantite) '
                . 'VALUES(:unIdUser, :unMois, :idFrais, NULL)'
            );
            $requetePrepare->bindParam(':unIdUser', $idVisiteur, PDO::PARAM_STR);
            $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
            $requetePrepare->bindParam(
                ':idFrais',
                $unIdFrais['idfrais'],
                PDO::PARAM_STR
            );
            $requetePrepare->execute();
        }
    }

    /**
     * Crée un nouveau frais hors forfait pour un user un mois donné
     * à partir des informations fournies en paramètre
     *
     * @param String $idVisiteur ID du user
     * @param String $mois       Mois sous la forme aaaamm
     * @param String $libelle    Libellé du frais
     * @param String $date       Date du frais au format français jj//mm/aaaa
     * @param Float  $montant    Montant du frais
     *
     * @return null
     */
    public function creeNouveauFraisHorsForfait(
        $idVisiteur,
        $mois,
        $libelle,
        $date,
        $montant
    ) {
        $dateFr = dateFrancaisVersAnglais($date);
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'INSERT INTO lignefraishorsforfait '
            . 'VALUES (null, :unIdUser,:unMois, :unLibelle, :uneDateFr,'
            . ':unMontant) '
        );
        $requetePrepare->bindParam(':unIdUser', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unLibelle', $libelle, PDO::PARAM_STR);
        $requetePrepare->bindParam(':uneDateFr', $dateFr, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMontant', $montant, PDO::PARAM_INT);
        $requetePrepare->execute();
    }

    /**
     * Supprime le frais hors forfait dont l'id est passé en argument
     *
     * @param String $idFrais ID du frais
     *
     * @return null
     */
    public function supprimerFraisHorsForfait($idFrais)
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'DELETE FROM lignefraishorsforfait '
            . 'WHERE lignefraishorsforfait.id = :unIdFrais'
        );
        $requetePrepare->bindParam(':unIdFrais', $idFrais, PDO::PARAM_STR);
        $requetePrepare->execute();
    }

    /**
     * Retourne les mois pour lesquel un user a une fiche de frais
     *
     * @param String $idVisiteur ID du user
     *
     * @return [] un tableau associatif de clé un mois -aaaamm- et de valeurs
     *         l'année et le mois correspondant
     */
    public function getLesMoisDisponibles($idVisiteur=Null)
    {
        if ($idVisiteur != Null) {
            $requetePrepare = PdoGSB::$monPdo->prepare(
                'SELECT fichefrais.mois AS mois FROM fichefrais '
                . 'WHERE fichefrais.idvisiteur = :unIdUser '
                . 'ORDER BY fichefrais.mois desc'
            );
            $requetePrepare->bindParam(':unIdUser', $idVisiteur, PDO::PARAM_STR);
        } else {
            $requetePrepare = PdoGSB::$monPdo->prepare(
                'SELECT distinct fichefrais.mois AS mois FROM fichefrais '
                . 'ORDER BY fichefrais.mois desc'
            );
        }
        $requetePrepare->execute();
        $lesMois = array();
        while ($laLigne = $requetePrepare->fetch()) {
            $mois = $laLigne['mois'];
            $numAnnee = substr($mois, 0, 4);
            $numMois = substr($mois, 4, 2);
            $lesMois[] = array(
                'mois' => $mois,
                'numAnnee' => $numAnnee,
                'numMois' => $numMois
            );
        }
        return $lesMois;
    }
    /**
     * Retourne la liste des visiteurs pr�sents dans la table user
     * 
     * @return [] un tableau associatifs de cl� id user et de valeurs le nom et le pr�nom 
     *      de l'utisateur correspondant
     */
    public function getLesVisiteurs()
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            "SELECT user.id AS id, user.nom AS nom, user.prenom As prenom FROM user "
            . "WHERE user.typepop = 'v' "
            . "ORDER BY user.nom, user.prenom"
            );
        $requetePrepare->execute();
        $lesVisiteurs = array();
        while ($laLigne = $requetePrepare->fetch()) {
            $id = $laLigne['id'];
            $nom = $laLigne['nom'];
            $prenom = $laLigne['prenom'];
            $lesVisiteurs[] = array(
                'id' => $id,
                'nom' => $nom,
                'prenom' => $prenom
            );
        }
        return $lesVisiteurs;
    }
    

    /**
     * Retourne les informations d'une fiche de frais d'un user pour un
     * mois donné
     *
     * @param String $idVisiteur ID du user
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return [] un tableau avec des champs de jointure entre une fiche de frais
     *         et la ligne d'état
     */
    public function getLesInfosFicheFrais($idVisiteur, $mois)
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'SELECT fichefrais.idetat as idEtat, '
            . 'fichefrais.datemodif as dateModif,'
            . 'fichefrais.nbjustificatifs as nbJustificatifs, '
            . 'fichefrais.montantvalide as montantValide, '
            . 'fichefrais.idetat as idEtat,'
            . 'etat.libelle as libEtat '
            . 'FROM fichefrais '
            . 'INNER JOIN etat ON fichefrais.idetat = etat.id '
            . 'WHERE fichefrais.idvisiteur = :unIdUser '
            . 'AND fichefrais.mois = :unMois'
        );
        $requetePrepare->bindParam(':unIdUser', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $laLigne = $requetePrepare->fetch();
        return $laLigne;
    }

    /**
     * Modifie l'état et la date de modification d'une fiche de frais.
     * Modifie le champ idEtat et met la date de modif à aujourd'hui.
     *
     * @param String $idVisiteur ID du user
     * @param String $mois       Mois sous la forme aaaamm
     * @param String $etat       Nouvel état de la fiche de frais
     *
     * @return null
     */
    public function majEtatFicheFrais($idVisiteur=NULL, $mois=NULL, $etat=NULL)
    {
        if (is_null($idVisiteur) && is_null($mois) && is_null($etat)) {
            $requetePrepare = PdoGSB::$monPdo->prepare(
                'UPDATE ficheFrais '
                . "SET idetat = 'RB', datemodif = now() "
                . "WHERE idetat = 'MP' "
                );      
        } else {
            $requetePrepare = PdoGSB::$monPdo->prepare(
                'UPDATE ficheFrais '
                . 'SET idetat = :unEtat, datemodif = now() '
                . 'WHERE fichefrais.idvisiteur = :unIdUser '
                . 'AND fichefrais.mois = :unMois'
            );            
            $requetePrepare->bindParam(':unIdUser', $idVisiteur, PDO::PARAM_STR);
            $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        }
        $requetePrepare->bindParam(':unEtat', $etat, PDO::PARAM_STR);
        $requetePrepare->execute();
    }
   /**
    * Valide la fiche de frais pour iduser et le mois donné
    * 
    * @param String $idVisiteur ID du user
    * @param String $mois       Mois sous la forme aaaamm
    * 
    * @return null
    */
    public function validerFicheFrais($idVisiteur, $mois) {
        $fraisForfait = $this->getLesFraisForfait($idVisiteur, $mois);
        $fraisHorsForfait = $this->getLesFraisHorsForfait($idVisiteur, $mois);
        $montant = 0;
        Foreach ($fraisForfait as $unFraisForfait) {
            $montant += $unFraisForfait['quantite'] * $unFraisForfait['montant'];
        }
        Foreach ($fraisHorsForfait as $unFraisHorsForfait) {
            if (substr($unFraisHorsForfait['libelle'], 0, 6) != 'REFUSE') {
                $montant += $unFraisHorsForfait['montant'];
            }   
        }
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'UPDATE ficheFrais '
            . "SET idetat = 'VA', montantvalide = :unMontant, datemodif = now() "
            . 'WHERE fichefrais.idvisiteur = :unIdUser '
            . 'AND fichefrais.mois = :unMois'
            );
        $requetePrepare->bindParam(':unIdUser', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMontant', $montant, PDO::PARAM_STR);
        $requetePrepare->execute();
    }
    
    /**
     * Met à jour le libelle apr un nouveau libelle du frais hors forfait 
     * pour un idfrais donné
     * @param integer $idFrais
     * @param string $libelle
     * 
     * @return null
     */
    public function majLibelleFraisHorsForfait($idFrais, $libelle)
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'Select lignefraishorsforfait.libelle as libelle '
            . 'from lignefraishorsforfait '
            . 'WHERE lignefraishorsforfait.id = :unId '
            );
        $requetePrepare->bindParam(':unId', $idFrais, PDO::PARAM_STR);
        $requetePrepare->execute();
        $laLigne = $requetePrepare->fetch();
        $newLibelle = $libelle . ': ' .$laLigne['libelle'];
        if (strlen($newLibelle) > 100) {
            $newLibelle = substr($newLibelle, 0, 99);
        }
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'UPDATE lignefraishorsforfait '
            . 'SET libelle = :unNouveauLibelle '
            . 'WHERE lignefraishorsforfait.id = :unId '
            );
        $requetePrepare->bindParam(':unNouveauLibelle', $newLibelle, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unId', $idFrais, PDO::PARAM_STR);
        $requetePrepare->execute();
    }
}
