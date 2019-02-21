<?php
/**
 * Vue Validation Fiche
 *
 * PHP Version 7
 *
 * @category  PPE
 * @package   GSB
 * @author    RÃ©seau CERTA <contact@reseaucerta.org>
 * @author    JosÃ© GIL <jgil@ac-nice.fr>
 * @copyright 2017 RÃ©seau CERTA
 * @license   RÃ©seau CERTA
 * @version   GIT: <0>
 * @link      http://www.reseaucerta.org Contexte Â« Laboratoire GSB Â»
 */
if ($uc=='gererFrais') {
?>
    <div class="alert alert-success" role="alert">
        <?php
        echo 'La fiche a été correctement validée : ';
        ?>
        <a href="index.php?uc=accueil"> Retour à l'accueil </a>
    </div>
<?php 
} elseif ($uc=='etatFrais') {
?>  
	<div class="alert alert-success" role="alert">
    <?php
    echo 'Les fiches mises en paiement ont bien été remboursées : ';
    ?>
        <a href="index.php?uc=accueil"> Retour à l'accueil </a>
    </div>
<?php 
}?>