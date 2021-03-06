<?php
/**
 * Gestion de la génération du pdf
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

require('fpdf/fpdf.php');
$leMois = filter_input(INPUT_POST, 'selectMois', FILTER_SANITIZE_STRING);
$idUser = $_SESSION['idUser'];
buildPdf($idUser, $leMois);