<?php

require_once '../includes/fct.inc.php';
require_once '../includes/class.pdogsb.inc.php';
$pdo = PdoGsb::getPdoGsb();
$id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_STRING);
$pdo->supprimerFraisHorsForfait($id);