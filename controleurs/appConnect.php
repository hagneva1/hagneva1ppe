<?php

require_once 'includes/fct.inc.php';
require_once 'includes/class.pdogsb.inc.php';
$pdo = PdoGsb::getPdoGsb();
/*$login = filter_input(INPUT_POST, 'login', FILTER_SANITIZE_STRING);
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
}*/
echo 'Hello World !';