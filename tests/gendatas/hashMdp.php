<?php
/**
 * Génération d'un jeu d'essai
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

require './fonctions.php';

$pdo = new PDO('mysql:host=localhost;dbname=gsb_frais', 'root', '');
$pdo->query('SET CHARACTER SET utf8');

set_time_limit(0);
hashMdpVisiteur($pdo);
echo 'les MDP ont été mis à jour';