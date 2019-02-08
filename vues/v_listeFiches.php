<?php
/**
 * Vue Liste des mois
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
?>
<div class="row">
   <div class="col-md-3">
    	<h3>Choisir la fiche : </h3>
   </div>
   	<form action="index.php?uc=gererFrais&action=voirValidationFrais" 
          method="post" role="form" class="form_horizontal" name="selectionFiche">
        <div class="form-group">
        	<div class="col-md-4">
                <select id="lstVisiteurs" name="lstVisiteurs" class="form-control">
                    <?php 
                    if ($selectVisiteur == Null) { ?>
                    	<option selected value=Null>
                    <?php echo "" ?> </option>
                    <?php 
                    }
                    foreach ($lesVisiteurs as $unVisiteur) {
                        $id = $unVisiteur['id'];
                        $nom = $unVisiteur['nom'];
                        $prenom = $unVisiteur['prenom'];  
                        if ($selectVisiteur == $id) {
                    ?>
                    		<option selected value="<?php echo $id ?>">
                			<?php echo $nom . ' ' . $prenom ?> </option>
                		<?php
                        } else {
                	   ?>
                            <option value="<?php echo $id ?>">
                            <?php echo $nom . ' ' . $prenom?> </option>
                	<?php
                        }
                    }
                    ?>    
            	</select> 
            </div>
     		<div class="col-md-4">
                <select id="lstMois" name="lstMois" class="form-control" 
                	onchange="selectionFiche.submit();">
                	<?php 
                	if ($selectMois == Null) {?>
                		<option selected value=Null>
                    	<?php echo "" ?> </option>
                    <?php
                    } 
                    foreach ($lesMois as $unMois) {
                        $mois = $unMois['mois'];
                        $numAnnee = $unMois['numAnnee'];
                        $numMois = $unMois['numMois'];
                        if ($selectMois == $mois) { ?>
                    		<option selected value="<?php echo $mois ?>">
                			<?php echo $numMois . '/' . $numAnnee ?> </option>
                    	<?php 
                        } else { ?>
                            <option value="<?php echo $mois ?>">
                             <?php echo $numMois . '/' . $numAnnee?> </option>
                	<?php   }
                    }
                    ?>  
                </select>
           </div>                
    	</div>
    </form>
</div>