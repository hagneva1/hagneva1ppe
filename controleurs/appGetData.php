<?php

require_once '../includes/fct.inc.php';
require_once '../includes/class.pdogsb.inc.php';
$pdo = PdoGsb::getPdoGsb();
$json = '[{"id":"a17", "annee":2019,"etape":1,"km":1,"lesFraisHf":[{"id":9999,"jour":28,"montant":150.0,"motif":"Achat fleur"},{"id":9998,"jour":28,"montant":100.0,"motif":"Location voiture"}],"mois":3,"nuitee":1,"repas":1,"typeVehicule":"D5"}]';
//file_get_contents('php://input');
$lesFraisHF = json_decode($json, true);
$id = $lesFraisHF[0]['id'];
$lesFrais = array($lesFraisHF[0]['typeVehicule'] => $lesFraisHF[0]['km'],
    "ETP" => $lesFraisHF[0]['etape'], "NUI" => $lesFraisHF[0]['nuitee'],
    "REP" => $lesFraisHF[0]['repas']);
if ($lesFraisHF[0]['mois'] < 10) {
    $mois = $lesFraisHF[0]['annee']+'0'+$lesFraisHF[0]['mois'];
} else {
    $mois = $lesFraisHF[0]['annee']+$lesFraisHF[0]['mois'];
}
$pdo->majFraisForfait($id, $mois, $lesFrais);
$tab = $pdo->getLesFraisHorsForfait($id, $mois);
foreach ($lesFraisHF[0]['lesFraisHf'] as $unFraisHF) {
    $flag = 0;
    foreach ($tab as $ligne) {
        if ($ligne['id'] == $unFraisHF['id']) {
            $flag = 1;
        }
        if ($flag == 0){
            $pdo->creeNouveauFraisHorsForfait(
                $id, $mois, $unFraisHF['motif'],
                substr($mois, 0, 4).'-'.substr($mois, 4, 2).'-'.$unFraisHF['jour'],
                $unFraisHF['montant']);
        }
    }
}
