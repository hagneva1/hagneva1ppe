<?php
/**
 * Gestion de l'affichage des frais
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
switch ($action) {
case 'selectionnerMois':
    $lesMois = $pdo->getLesMoisDisponibles($idUser);
    // Afin de sélectionner par défaut le dernier mois dans la zone de liste
    // on demande toutes les clés, et on prend la première,
    // les mois étant triés décroissants
    $lesCles = array_keys($lesMois);
    $moisASelectionner = $lesCles[0];
    include 'vues/v_listeMois.php';
    break;

case 'selectionnerFiches':
    $lesVisiteurs = $pdo->getLesVisiteurs();
    $lesMois = $pdo->getLesMoisDisponibles();
    $selectMois = Null;
    $selectVisiteur = Null;
    include 'vues/v_listeFiches.php';
    break;

case 'rembourser':
    $pdo->majEtatFicheFrais();
    require 'vues/v_validationFiche.php';
    break;
    
case 'majEtatFiche':
    $selectVisiteur = filter_input(INPUT_POST, 'selectVisiteur', FILTER_SANITIZE_STRING);
    $selectMois = filter_input(INPUT_POST, 'selectMois', FILTER_SANITIZE_STRING);
    $etat = filter_input(INPUT_POST, 'btnEtat', FILTER_SANITIZE_STRING);
    $pdo->majEtatFicheFrais($selectVisiteur, $selectMois, $etat);
    $lesVisiteurs = $pdo->getLesVisiteurs();
    $lesMois = $pdo->getLesMoisDisponibles();
    include 'vues/v_listeFiches.php';
    $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($selectVisiteur, $selectMois);
    $lesFraisForfait = $pdo->getLesFraisForfait($selectVisiteur, $selectMois);
    $lesInfosFicheFrais = $pdo->getLesInfosFicheFrais($selectVisiteur, $selectMois);
    $numAnnee = substr($selectMois, 0, 4);
    $numMois = substr($selectMois, 4, 2);
    $libEtat = $lesInfosFicheFrais['libEtat'];
    $montantValide = $lesInfosFicheFrais['montantValide'];
    $nbJustificatifs = $lesInfosFicheFrais['nbJustificatifs'];
    $etatFiche = $lesInfosFicheFrais['idEtat'];
    $dateModif = $lesInfosFicheFrais['dateModif'];
    if (is_null($etatFiche)) {
        ajouterErreur("Cette fiche n'éxiste pas");
        include 'vues/v_erreurs.php';
    } else {
        require 'vues/v_etatFrais.php';
    }
    break;

case 'voirEtatFrais':
    if ($typepop == 'v') {
        $leMois = filter_input(INPUT_POST, 'lstMois', FILTER_SANITIZE_STRING);
        $lesMois = $pdo->getLesMoisDisponibles($idUser);
        $moisASelectionner = $leMois;
        include 'vues/v_listeMois.php';
        $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idUser, $leMois);
        $lesFraisForfait = $pdo->getLesFraisForfait($idUser, $leMois);
        $lesInfosFicheFrais = $pdo->getLesInfosFicheFrais($idUser, $leMois);
        $numAnnee = substr($leMois, 0, 4);
        $numMois = substr($leMois, 4, 2);
        $libEtat = $lesInfosFicheFrais['libEtat'];
        $montantValide = $lesInfosFicheFrais['montantValide'];
        $nbJustificatifs = $lesInfosFicheFrais['nbJustificatifs'];
        $dateModif = dateAnglaisVersFrancais($lesInfosFicheFrais['dateModif']);
        include 'vues/v_etatFrais.php';
    } else {
        $selectVisiteur = filter_input(INPUT_POST, 'lstVisiteurs', FILTER_SANITIZE_STRING);
        $selectMois = filter_input(INPUT_POST, 'lstMois', FILTER_SANITIZE_STRING);
        $lesVisiteurs = $pdo->getLesVisiteurs();
        $lesMois = $pdo->getLesMoisDisponibles();
        include 'vues/v_listeFiches.php';
        $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($selectVisiteur, $selectMois);
        $lesFraisForfait = $pdo->getLesFraisForfait($selectVisiteur, $selectMois);
        $lesInfosFicheFrais = $pdo->getLesInfosFicheFrais($selectVisiteur, $selectMois);
        $numAnnee = substr($selectMois, 0, 4);
        $numMois = substr($selectMois, 4, 2);
        $libEtat = $lesInfosFicheFrais['libEtat'];
        $montantValide = $lesInfosFicheFrais['montantValide'];
        $nbJustificatifs = $lesInfosFicheFrais['nbJustificatifs'];
        $etatFiche = $lesInfosFicheFrais['idEtat'];
        $dateModif = $lesInfosFicheFrais['dateModif'];
        if (is_null($etatFiche)) {
            ajouterErreur("Cette fiche n'éxiste pas");
            include 'vues/v_erreurs.php';
        } else {
            require 'vues/v_etatFrais.php';
        }
    }
    break;
}