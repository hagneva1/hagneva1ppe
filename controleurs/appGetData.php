<?php

require_once '../includes/fct.inc.php';
require_once '../includes/class.pdogsb.inc.php';
$pdo = PdoGsb::getPdoGsb();
$id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_STRING);
$mois = filter_input(INPUT_POST, 'mois', FILTER_SANITIZE_STRING);
$typeVehicule = filter_input(INPUT_POST, 'typeVeh', FILTER_SANITIZE_STRING);
$km = filter_input(INPUT_POST, 'km', FILTER_SANITIZE_STRING);
$etp = filter_input(INPUT_POST, 'ETP', FILTER_SANITIZE_STRING);
$nui = filter_input(INPUT_POST, 'NUI', FILTER_SANITIZE_STRING);
$rep = filter_input(INPUT_POST, 'REP', FILTER_SANITIZE_STRING);
$json = filter_input(INPUT_POST, 'lesFraisHF', FILTER_SANITIZE_STRING);
$lesFraisHF = json_decode($json);
$lesFrais = array($typeVehicule => $km, "ETP" => $etp, "NUI" => $nui, "REP" => $rep);
//$pdo->majFraisForfait($id, $mois, $lesFrais);
print_r($json);
print_r($lesFraisHF);
