<?php
/**
 * Fonctions pour l'application GSB
 *
 * PHP Version 7
 *
 * @category  PPE
 * @package   GSB
 * @author    Cheri Bibi - Réseau CERTA <contact@reseaucerta.org>
 * @author    José GIL <jgil@ac-nice.fr>
 * @copyright 2017 Réseau CERTA
 * @license   Réseau CERTA
 * @version   GIT: <0>
 * @link      http://www.php.net/manual/fr/book.pdo.php PHP Data Objects sur php.net
 */

/**
 * Teste si un quelconque user est connecté
 *
 * @return boolean vrai ou faux
 */
function estConnecte()
{
    return isset($_SESSION['idUser']);
}

/**
 * Enregistre dans une variable session les infos d'un user
 *
 * @param String $idUser ID du user
 * @param String $nom        Nom du user
 * @param String $prenom     Prénom du user
 *
 * @return null
 */
function connecter($idUser, $nom, $prenom, $typepop)
{
    $_SESSION['idUser'] = $idUser;
    $_SESSION['nom'] = $nom;
    $_SESSION['prenom'] = $prenom;
    $_SESSION['typepop'] = $typepop;
}

/**
 * Détruit la session active
 *
 * @return null
 */
function deconnecter()
{
    session_destroy();
}

/**
 * Transforme une date au format français jj/mm/aaaa vers le format anglais
 * aaaa-mm-jj
 *
 * @param String $maDate au format  jj/mm/aaaa
 *
 * @return DateTime au format anglais aaaa-mm-jj
 */
function dateFrancaisVersAnglais($maDate)
{
    @list($jour, $mois, $annee) = explode('/', $maDate);
    return date('Y-m-d', mktime(0, 0, 0, $mois, $jour, $annee));
}

/**
 * Transforme une date au format format anglais aaaa-mm-jj vers le format
 * français jj/mm/aaaa
 *
 * @param String $maDate au format  aaaa-mm-jj
 *
 * @return DateTime au format format français jj/mm/aaaa
 */
function dateAnglaisVersFrancais($maDate)
{
    @list($annee, $mois, $jour) = explode('-', $maDate);
    $date = $jour . '/' . $mois . '/' . $annee;
    return $date;
}

/**
 * Affiche le pdf de la fiche de frais de l'idUser pour le mois donné
 * 
 * @param string $idUser
 * @param integer $mois
 */
function buildPdf($idUser=NULL, $mois=NULL)
{
    require_once 'class.pdogsb.inc.php';
    $pdo = PdoGsb::getPdoGsb();
    $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idUser, $mois);
    $lesFraisForfait = $pdo->getLesFraisForfait($idUser, $mois);
    $lesInfosFicheFrais = $pdo->getLesInfosFicheFrais($idUser, $mois);    
    $pdf = new PDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial','B',12);
    $pdf->SetMargins(20, 20, 20);
    $spaceH = 10;
    $pdf->Image('images/logo.jpg', 80, 20);
    $pdf->setY(70);
    $pdf->SetTextColor(0, 51, 102);
    $pdf->Cell(180, 10, 'REMBOURSEMENT DE FRAIS ENGAGES', 1, 0, 'C');
    $pdf->SetTextColor(0);
    $pdf->setY($pdf->getY()+$spaceH);
    $header = array('', '', '');
    $data = array(array('Visiteur', $_SESSION['idUser'], $_SESSION['nom'] . ' ' . $_SESSION['prenom']), 
        array('Mois', substr($mois, 4, 2) . '/' . substr($mois, 0, 4), ''));
    $pdf->FancyTable($header, $data, 35, 3);
    $pdf->setY($pdf->getY()+$spaceH);
    $header = array('Frais Forfaitaires', utf8_decode('Quantité'), 'Montant unitaire', 'TOTAL');
    $data = array();
    Foreach ($lesFraisForfait as $unFraisForfait) {
        if (strlen($unFraisForfait['idfrais']) == 2) {
            $data[] = array(substr(utf8_decode($unFraisForfait['libelle']), 0, 18), $unFraisForfait['quantite'],
                $unFraisForfait['montant'], $unFraisForfait['quantite'] * $unFraisForfait['montant']);
            
        } else {
            $data[] = array(utf8_decode($unFraisForfait['libelle']), $unFraisForfait['quantite'], 
                $unFraisForfait['montant'], $unFraisForfait['quantite'] * $unFraisForfait['montant']);
        }
    }
    $pdf->FancyTable($header, $data, 35, 4, 'b');
    $pdf->setXY(35, $pdf->getY()+$spaceH);
    $pdf->SetTextColor(0, 51, 102);
    $pdf->SetFont('','B');
    $pdf->Cell(145, 10, 'Autres Frais', 0, 0, 'C');
    $pdf->SetFont('');
    $pdf->SetTextColor(0);
    $pdf->Ln();
    $header = array('Date', utf8_decode('Libellé'), 'Montant');
    $data = array();
    Foreach ($lesFraisHorsForfait as $unFraisHorsForfait) {
        if (substr($unFraisHorsForfait['libelle'], 0, 6) <> 'REFUSE') {
            $data[] = array(utf8_decode($unFraisHorsForfait['date']), utf8_decode($unFraisHorsForfait['libelle']),
                $unFraisHorsForfait['montant']);
        }
    }
    $pdf->FancyTable($header, $data, 35, 3, 'b');
    $pdf->setXY(110, $pdf->getY()+$spaceH);
    $pdf->cell(35, 7, 'TOTAL', 1);
    $pdf->cell(40, 7, $lesInfosFicheFrais['montantValide'].iconv("UTF-8", "CP1252", "€"), 1, 0, 'R');    
    $pdf->rect(20, 80, 180, $pdf->getY()+$spaceH-75);
    $pdf->setXY(130, $pdf->getY()+2*$spaceH);
    $pdf->cell(35, 7, utf8_decode('Fait à Paris, le ') . date('d-m-Y'), 0);
    $pdf->Ln();
    $pdf->setX(130);
    $pdf->cell(35, 7, "Vu l'agent comptable", 0);
    $pdf->Image('images/signature.jpg', 130);
    $pdf->Output();
}

/**
 * Retourne le mois au format aaaamm selon le jour dans le mois
 *
 * @param String $date au format  jj/mm/aaaa
 *
 * @return String Mois au format aaaamm
 */
function getMois($date)
{
    @list($jour, $mois, $annee) = explode('/', $date);
    unset($jour);
    if (strlen($mois) == 1) {
        $mois = '0' . $mois;
    }
    return $annee . $mois;
}

/* gestion des erreurs */

/**
 * Indique si une valeur est un entier positif ou nul
 *
 * @param Integer $valeur Valeur
 *
 * @return Boolean vrai ou faux
 */
function estEntierPositif($valeur)
{
    return preg_match('/[^0-9]/', $valeur) == 0;
}

/**
 * Indique si un tableau de valeurs est constitué d'entiers positifs ou nuls
 *
 * @param Array $tabEntiers Un tableau d'entier
 *
 * @return Boolean vrai ou faux
 */
function estTableauEntiers($tabEntiers)
{
    $boolReturn = true;
    foreach ($tabEntiers as $unEntier) {
        if (!estEntierPositif($unEntier)) {
            $boolReturn = false;
        }
    }
    return $boolReturn;
}

/**
 * Vérifie si une date est inférieure d'un an à la date actuelle
 *
 * @param String $dateTestee Date à tester
 *
 * @return Boolean vrai ou faux
 */
function estDateDepassee($dateTestee)
{
    $dateActuelle = date('d/m/Y');
    @list($jour, $mois, $annee) = explode('/', $dateActuelle);
    $annee--;
    $anPasse = $annee . $mois . $jour;
    @list($jourTeste, $moisTeste, $anneeTeste) = explode('/', $dateTestee);
    return ($anneeTeste . $moisTeste . $jourTeste < $anPasse);
}

/**
 * Vérifie la validité du format d'une date française jj/mm/aaaa
 *
 * @param String $date Date à tester
 *
 * @return Boolean vrai ou faux
 */
function estDateValide($date)
{
    $tabDate = explode('/', $date);
    $dateOK = true;
    if (count($tabDate) != 3) {
        $dateOK = false;
    } else {
        if (!estTableauEntiers($tabDate)) {
            $dateOK = false;
        } else {
            if (!checkdate($tabDate[1], $tabDate[0], $tabDate[2])) {
                $dateOK = false;
            }
        }
    }
    return $dateOK;
}

/**
 * Vérifie que le tableau de frais ne contient que des valeurs numériques
 *
 * @param Array $lesFrais Tableau d'entier
 *
 * @return Boolean vrai ou faux
 */
function lesQteFraisValides($lesFrais)
{
    return estTableauEntiers($lesFrais);
}

/**
 * Vérifie la validité des trois arguments : la date, le libellé du frais
 * et le montant
 *
 * Des message d'erreurs sont ajoutés au tableau des erreurs
 *
 * @param String $dateFrais Date des frais
 * @param String $libelle   Libellé des frais
 * @param Float  $montant   Montant des frais
 *
 * @return null
 */
function valideInfosFrais($dateFrais, $libelle, $montant)
{
    if ($dateFrais == '') {
        ajouterErreur('Le champ date ne doit pas être vide');
    } else {
        if (!estDatevalide($dateFrais)) {
            ajouterErreur('Date invalide');
        } else {
            if (estDateDepassee($dateFrais)) {
                ajouterErreur(
                    "date d'enregistrement du frais dépassé, plus de 1 an"
                );
            }
        }
    }
    if ($libelle == '') {
        ajouterErreur('Le champ description ne peut pas être vide');
    }
    if ($montant == '') {
        ajouterErreur('Le champ montant ne peut pas être vide');
    } elseif (!is_numeric($montant)) {
        ajouterErreur('Le champ montant doit être numérique');
    }
}

/**
 * Ajoute le libellé d'une erreur au tableau des erreurs
 *
 * @param String $msg Libellé de l'erreur
 *
 * @return null
 */
function ajouterErreur($msg)
{
    if (!isset($_REQUEST['erreurs'])) {
        $_REQUEST['erreurs'] = array();
    }
    $_REQUEST['erreurs'][] = $msg;
}

/**
 * Retoune le nombre de lignes du tableau des erreurs
 *
 * @return Integer le nombre d'erreurs
 */
function nbErreurs()
{
    if (!isset($_REQUEST['erreurs'])) {
        return 0;
    } else {
        return count($_REQUEST['erreurs']);
    }
}
