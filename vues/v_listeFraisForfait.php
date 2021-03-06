<?php
/**
 * Vue Liste des frais au forfait
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
if ($_SESSION['typepop'] == 'v') {
?>
    <script>
        function yesnoCheck(that) {
        	document.getElementById("E4").style.display = "none";
        	document.getElementById("E5").style.display = "none";
        	document.getElementById("D4").style.display = "none";
        	document.getElementById("D5").style.display = "none";
            document.getElementById(that.value).style.display = "block";
        }
    </script>
    <div class="row">    
        <h2>Renseigner ma fiche de frais du mois 
            <?php echo $numMois . '-' . $numAnnee ?>
        </h2>
        <h3>Eléments forfaitisés</h3>
        <div class="col-md-4">
            <form method="post" 
                  action="index.php?uc=gererFrais&action=validerMajFraisForfait"
                  role="form">
                <fieldset>
                	<div class="form-group"> 
                		<label for="lstVehicule">Type de motorisation</label>          		     		
                		<select id="lstVehicule" name="lstVehicule" class="form-control" onchange="yesnoCheck(this)">
            			<option selected value="error"></option>
            			<?php
        			     foreach ($lesFraisForfait as $unFrais) {
            			    $idFrais = $unFrais['idfrais'];
            			    if (strlen($idFrais) == 2) {
        			             $libelle = substr($unFrais['libelle'], 21);
        			             if ($idVehicule == $idFrais) {
		                 ?>			<option selected value="<?php echo $idFrais?>"><?php echo $libelle?></option>
        			             <?php 
        			             } else {   
        			             ?>       			             
        			    			<option value="<?php echo $idFrais?>"><?php echo $libelle?></option>
        			    <?php
        			             }
            			    }
            			}
                			?>  
            			</select>              		
            		</div>
                    <?php
                    foreach ($lesFraisForfait as $unFrais) {
                        $idFrais = $unFrais['idfrais'];
                        $libelle = htmlspecialchars($unFrais['libelle']);
                        $quantite = $unFrais['quantite']; 
                        if (strlen($idFrais) == 2) { 
                            if ($idFrais == $idVehicule) {
                    ?>
                                <div class="form-group" id="<?php echo $idFrais?>" style="display: block;">
                                    <label for="idFrais"><?php echo $libelle ?></label>
                                    <input type="text" id="idFrais" 
                                           name="lesFrais[<?php echo $idFrais ?>]"
                                           size="10" maxlength="5" 
                                           value="<?php echo $quantite ?>" 
                                           class="form-control">
                            	</div>
                        <?php
                            } else {
                        ?>
                            	<div class="form-group" id="<?php echo $idFrais?>" style="display: none;">
                                    <label for="idFrais"><?php echo $libelle ?></label>
                                    <input type="text" id="idFrais" 
                                           name="lesFrais[<?php echo $idFrais ?>]"
                                           size="10" maxlength="5" 
                                           value="<?php echo $quantite ?>" 
                                           class="form-control">
                                </div>
                        <?php 
                            }
                        } else {?>
                            <div class="form-group">
                                <label for="idFrais"><?php echo $libelle ?></label>
                                <input type="text" id="idFrais" 
                                       name="lesFrais[<?php echo $idFrais ?>]"
                                       size="10" maxlength="5" 
                                       value="<?php echo $quantite ?>" 
                                       class="form-control">
                            </div>
                        <?php
                        }
                    }
                    ?>
                    <button class="btn btn-success" type="submit">Ajouter</button>
                    <button class="btn btn-danger" type="reset">Effacer</button>
                </fieldset>
            </form>
        </div>
    </div>
<?php
} else {?>
	<div class="row">    
        <h2 class="h2-or">Valider la fiche de frais</h2>
        <h3>Eléments forfaitisés</h3>
        <div class="col-md-4">
            <form method="post" 
                  action="index.php?uc=gererFrais&action=validerMajFraisForfait"  
                  role="form">
                <fieldset>       
                    <?php
                    foreach ($lesFraisForfait as $unFrais) {
                        $idFrais = $unFrais['idfrais'];
                        $libelle = htmlspecialchars($unFrais['libelle']);
                        $quantite = $unFrais['quantite']; ?>
                        <div class="form-group">
                            <label for="idFrais"><?php echo $libelle ?></label>
                            <input type="text" id="idFrais" 
                                   name="lesFrais[<?php echo $idFrais ?>]"
                                   size="10" maxlength="5" 
                                   value="<?php echo $quantite ?>" 
                                   class="form-control">
                           <input type="hidden" id="selectVisiteur" name="selectVisiteur" value=<?php echo $selectVisiteur;?>>
                           <input type="hidden" id="selectMois" name="selectMois" value=<?php echo $selectMois;?>>
                        </div>
                        <?php
                    }
                    ?>
                    <button class="btn btn-success" type="submit">Corriger</button>
                    <button class="btn btn-danger" type="reset">Réinitialiser</button>
                </fieldset>
            </form>
        </div>
    </div>
<?php }
