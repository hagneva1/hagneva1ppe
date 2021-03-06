<?php

require_once '../includes/fct.inc.php';
require_once '../includes/class.pdogsb.inc.php';
$pdo = PdoGsb::getPdoGsb();
$login=filter_input(INPUT_POST, 'id', FILTER_SANITIZE_STRING);
$mois=filter_input(INPUT_POST, 'mois', FILTER_SANITIZE_STRING);
$lesHF= $pdo->getLesFraisHorsForfait($login, $mois);
$i = 0;
Foreach($lesHF as $unHF){
    $json['lesFraisHF'][$i]=array("id" => $unHF['id'], "motif" => $unHF['libelle'],
        "date" => $unHF['date'], "montant" => $unHF['montant']
    );
    $i++;
}
$lesFrais = $pdo->getLesFraisForfait($login, $mois, 1);
$out = array("D4", "D5", "E4", "E5");
Foreach($lesFrais as $unFrais){
    if (in_array($unFrais['idfrais'], $out)) {
        $json['typeVehicule'] = $unFrais['idfrais'];
        $json['km'] = $unFrais['quantite'];
    } else {
        $json[$unFrais['idfrais']]=$unFrais['quantite'];
    }
}
$json = json_encode($json);
if ($json=="null") {
    echo 'Erreur';
} else {
    print_r($json);
}
