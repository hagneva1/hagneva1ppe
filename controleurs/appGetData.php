<?php

require_once '../includes/fct.inc.php';
require_once '../includes/class.pdogsb.inc.php';
$pdo = PdoGsb::getPdoGsb();
$json = file_get_contents('php://input');
$lesFraisHF = json_decode($json);
//$lesFrais = array($typeVehicule => $km, "ETP" => $etp, "NUI" => $nui, "REP" => $rep);
//$pdo->majFraisForfait($id, $mois, $lesFrais);
print_r($lesFraisHF);
