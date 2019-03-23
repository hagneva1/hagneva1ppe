<?php
/**
 * Gestion de la connexion
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
if (!$uc) {
    $uc = 'demandeconnexion';
}

switch ($action) {
case 'demandeConnexion':
    include 'vues/v_connexion.php';
    break;
case 'valideConnexion':
    $login = filter_input(INPUT_POST, 'login', FILTER_SANITIZE_STRING);
    $mdp = filter_input(INPUT_POST, 'mdp', FILTER_SANITIZE_STRING);
    $typepop = filter_input(INPUT_POST, 'typepop', FILTER_SANITIZE_STRING);
    $user = $pdo->getInfosUser($login, $mdp);
    if (!is_array($user)) {
        ajouterErreur('Login ou mot de passe incorrect');
        include 'vues/v_erreurs.php';
        include 'vues/v_connexion.php';
    } else {
        $id = $user['id'];
        $nom = $user['nom'];
        $prenom = $user['prenom'];
        $typepop = $user['typepop'];
        connecter($id, $nom, $prenom, $typepop);
        header('Location: index.php');
    }
    break;
case 'appConnect':
    $login = filter_input(INPUT_POST, 'login', FILTER_SANITIZE_STRING);
    $mdp = filter_input(INPUT_POST, 'mdp', FILTER_SANITIZE_STRING);
    $user = $pdo->getInfosUser($login, $mdp);
    if (!is_array($user)) {
        echo 'Erreur';
    } else {
        $id = $user['id'];
        $nom = $user['nom'];
        $prenom = $user['prenom'];
        $typepop = $user['typepop'];
        if ($typepop == 'c') {
            echo 'Comptable';
        } else {
            $flag[]=$id;
            print(json_encode($flag));
        }
    }
    break;
default:
    include 'vues/v_connexion.php';
    break;
}
