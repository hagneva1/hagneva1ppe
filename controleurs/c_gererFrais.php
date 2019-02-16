<?php
/**
 * Gestion des frais
 *
 * PHP Version 7
 *
 * @category  PPE
 * @package   GSB
 * @author    Réseau CERTA <contact@reseaucerta.org>
 * @author    José GIL <jgil@ac-nice.fr>
 * @copyright 2017 Réseau CERTA
 * @license   Réseau CERTA
 * @version   GIT: <0>
 * @link      http://www.reseaucerta.org Contexte « Laboratoire GSB »
 */
$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);
$idUser = $_SESSION['idUser'];
$typepop = $_SESSION['typepop'];
$mois = getMois(date('d/m/Y'));
$numAnnee = substr($mois, 0, 4);
$numMois = substr($mois, 4, 2);
switch ($action) {
    
    case 'saisirFrais':
        if ($pdo->estPremierFraisMois($idUser, $mois)) {
            $pdo->creeNouvellesLignesFrais($idUser, $mois);
        }
        $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idUser, $mois);
        $lesFraisForfait = $pdo->getLesFraisForfait($idUser, $mois);
        require 'vues/v_listeFraisForfait.php';
        require 'vues/v_listeFraisHorsForfait.php';
        break;
        
    case 'validerMajFraisForfait':
        $lesFrais = filter_input(INPUT_POST, 'lesFrais', FILTER_DEFAULT, FILTER_FORCE_ARRAY);
        if ($typepop == 'v') {
            
            if (lesQteFraisValides($lesFrais)) {
                $pdo->majFraisForfait($idUser, $mois, $lesFrais);
            }
            else {
                ajouterErreur('Les valeurs des frais doivent être numériques');
                include 'vues/v_erreurs.php';
            }
            $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idUser, $mois);
            $lesFraisForfait = $pdo->getLesFraisForfait($idUser, $mois);
        } else {
            $selectVisiteur = filter_input(INPUT_POST, 'selectVisiteur', FILTER_SANITIZE_STRING);
            $selectMois = filter_input(INPUT_POST, 'selectMois', FILTER_SANITIZE_STRING);
            if (lesQteFraisValides($lesFrais)) {
                $pdo->majFraisForfait($selectVisiteur, $selectMois, $lesFrais);          
            }
            else {
                ajouterErreur('Les valeurs des frais doivent être numériques');
                include 'vues/v_erreurs.php';
            }
            $lesVisiteurs = $pdo->getLesVisiteurs();
            $lesMois = $pdo->getLesMoisDisponibles();
            include 'vues/v_listeFiches.php';
            $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($selectVisiteur, $selectMois);
            $lesFraisForfait = $pdo->getLesFraisForfait($selectVisiteur, $selectMois);
            $lesInfosFicheFrais = $pdo->getLesInfosFicheFrais($selectVisiteur, $selectMois);
            $nbJustificatifs = $lesInfosFicheFrais['nbJustificatifs'];
            $etatFiche = $lesInfosFicheFrais['idEtat'];
            $dateModif = $lesInfosFicheFrais['dateModif'];
        } 
        require 'vues/v_listeFraisForfait.php';
        require 'vues/v_listeFraisHorsForfait.php';
        break;
        
    case 'validerCreationFrais':
        $dateFrais = filter_input(INPUT_POST, 'dateFrais', FILTER_SANITIZE_STRING);
        $libelle = filter_input(INPUT_POST, 'libelle', FILTER_SANITIZE_STRING);
        $montant = filter_input(INPUT_POST, 'montant', FILTER_VALIDATE_FLOAT);
        valideInfosFrais($dateFrais, $libelle, $montant);
        if (nbErreurs() != 0) {
            include 'vues/v_erreurs.php';
        } else {
            $pdo->creeNouveauFraisHorsForfait(
                $idUser,
                $mois,
                $libelle,
                $dateFrais,
                $montant
            );
        $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idUser, $mois);
        $lesFraisForfait = $pdo->getLesFraisForfait($idUser, $mois);
        require 'vues/v_listeFraisForfait.php';
        require 'vues/v_listeFraisHorsForfait.php';
        }
        break;
        
    case 'supprimerFrais':
        $idFrais = filter_input(INPUT_GET, 'idFrais', FILTER_SANITIZE_STRING);
        $pdo->supprimerFraisHorsForfait($idFrais);
        $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idUser, $mois);
        $lesFraisForfait = $pdo->getLesFraisForfait($idUser, $mois);
        require 'vues/v_listeFraisForfait.php';
        require 'vues/v_listeFraisHorsForfait.php';
        break;
        
    case 'selectionnerFiches': 
        $lesVisiteurs = $pdo->getLesVisiteurs();
        $lesMois = $pdo->getLesMoisDisponibles();
        $selectMois = Null;
        $selectVisiteur = Null;
        include 'vues/v_listeFiches.php';            
        break;
        
    case 'validerFicheFrais':
        $idVisiteur = filter_input(INPUT_POST, 'selectVisiteur', FILTER_SANITIZE_STRING);
        $selectMois = filter_input(INPUT_POST, 'selectMois', FILTER_SANITIZE_STRING);
        $etat = 'VA';
        $pdo->validerFicheFrais($idVisiteur, $selectMois);
        include 'vues/v_validationFiche.php';
        break;
    
        
    case 'reporterFraisHorsForfait':
        $selectVisiteur = filter_input(INPUT_POST, 'selectVisiteur', FILTER_SANITIZE_STRING);
        $selectMois = filter_input(INPUT_POST, 'selectMois', FILTER_SANITIZE_STRING);
        $idFraisHorsForfait =  filter_input(INPUT_POST, 'idFraisHorsForfait', FILTER_SANITIZE_STRING);
        $pdo->reporterFraisHorsForfait($idFraisHorsForfait, $selectVisiteur, $selectMois);
        $lesVisiteurs = $pdo->getLesVisiteurs();
        $lesMois = $pdo->getLesMoisDisponibles();
        include 'vues/v_listeFiches.php';
        $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($selectVisiteur, $selectMois);
        $lesFraisForfait = $pdo->getLesFraisForfait($selectVisiteur, $selectMois);
        $lesInfosFicheFrais = $pdo->getLesInfosFicheFrais($selectVisiteur, $selectMois);
        $nbJustificatifs = $lesInfosFicheFrais['nbJustificatifs'];
        $etatFiche = $lesInfosFicheFrais['idEtat'];
        $dateModif = $lesInfosFicheFrais['dateModif'];
        require 'vues/v_listeFraisForfait.php';
        require 'vues/v_listeFraisHorsForfait.php';
        break;
    
    case 'refuserFraisHorsForfait':
        $selectVisiteur = filter_input(INPUT_POST, 'selectVisiteur', FILTER_SANITIZE_STRING);
        $selectMois = filter_input(INPUT_POST, 'selectMois', FILTER_SANITIZE_STRING);
        $idFraisHorsForfait =  filter_input(INPUT_POST, 'idFraisHorsForfait', FILTER_SANITIZE_STRING);
        $pdo->majLibelleFraisHorsForfait($idFraisHorsForfait, 'REFUSE');
        $lesVisiteurs = $pdo->getLesVisiteurs();
        $lesMois = $pdo->getLesMoisDisponibles();
        include 'vues/v_listeFiches.php';
        $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($selectVisiteur, $selectMois);
        $lesFraisForfait = $pdo->getLesFraisForfait($selectVisiteur, $selectMois);
        $lesInfosFicheFrais = $pdo->getLesInfosFicheFrais($selectVisiteur, $selectMois);
        $nbJustificatifs = $lesInfosFicheFrais['nbJustificatifs'];
        $etatFiche = $lesInfosFicheFrais['idEtat'];
        $dateModif = $lesInfosFicheFrais['dateModif'];
        require 'vues/v_listeFraisForfait.php';
        require 'vues/v_listeFraisHorsForfait.php';
        break;
    
    case 'voirValidationFrais':
        $selectVisiteur = filter_input(INPUT_POST, 'lstVisiteurs', FILTER_SANITIZE_STRING);
        $selectMois = filter_input(INPUT_POST, 'lstMois', FILTER_SANITIZE_STRING);
        $lesVisiteurs = $pdo->getLesVisiteurs();
        $lesMois = $pdo->getLesMoisDisponibles();
        include 'vues/v_listeFiches.php';
        $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($selectVisiteur, $selectMois);
        $lesFraisForfait = $pdo->getLesFraisForfait($selectVisiteur, $selectMois);
        $lesInfosFicheFrais = $pdo->getLesInfosFicheFrais($selectVisiteur, $selectMois);
        $nbJustificatifs = $lesInfosFicheFrais['nbJustificatifs'];
        $etatFiche = $lesInfosFicheFrais['idEtat'];
        $dateModif = $lesInfosFicheFrais['dateModif'];
        switch ($etatFiche) {
            case "CL":
                require 'vues/v_listeFraisForfait.php';
                require 'vues/v_listeFraisHorsForfait.php';
                break;
            case "CR":
                ajouterErreur('Cette fiche est en cours de saisie depuis le ' . dateAnglaisVersFrancais($dateModif));
                include 'vues/v_erreurs.php';
                break;
            case "RB":
                ajouterErreur('Le remboursement de la fiche a été effectué le ' . dateAnglaisVersFrancais($dateModif));
                include 'vues/v_erreurs.php';
                break;
            case "VA":
                ajouterErreur('Cette fiche a été validée le ' . dateAnglaisVersFrancais($dateModif) . ', le paiement est en cours');
                include 'vues/v_erreurs.php';
                break;
            default: 
               ajouterErreur("Cette fiche n'éxiste pas");
               include 'vues/v_erreurs.php';
               break;
        }
        break;
}

