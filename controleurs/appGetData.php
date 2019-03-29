<?php

require_once '../includes/fct.inc.php';
require_once '../includes/class.pdogsb.inc.php';
$pdo = PdoGsb::getPdoGsb();
$json = file_get_contents('php://input');
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
//$pdo->majFraisForfait($id, $mois, $lesFrais);
$tab = $pdo->getLesFraisHorsForfait($id, $mois);
foreach ($lesFraisHF[0]['lesFraisHf'] as $unFraisHF) {
    $flag = 0;
    foreach ($tab as $ligne) {
        if ($ligne['id'] == $unFraisHF['id']) {
            $flag = 1;
        }
        if ($flag == 0){
            /*$pdo->creeNouveauFraisHorsForfait(
                $id, $mois, $unFraisHF['motif'],
                substr($mois, 0, 4).'-'.substr($mois, 4, 2).'-'.$unFraisHF['jour'],
                $unFraisHF['montant']);*/
        }
    }
}
echo $lesFraisHF['typeVehicule'];
