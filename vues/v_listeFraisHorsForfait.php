<?php
/**
 * Vue Liste des frais hors forfait
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
    <hr>
    <div class="row">
        <div class="panel panel-info">
            <div class="panel-heading">Descriptif des éléments hors forfait</div>
            <table class="table table-bordered table-responsive">
                <thead>
                    <tr>
                        <th class="date">Date</th>
                        <th class="libelle">Libellé</th>  
                        <th class="montant">Montant</th>  
                        <th class="action">&nbsp;</th> 
                    </tr>
                </thead>  
                <tbody>
                <?php
                foreach ($lesFraisHorsForfait as $unFraisHorsForfait) {
                    $libelle = htmlspecialchars($unFraisHorsForfait['libelle']);
                    $date = $unFraisHorsForfait['date'];
                    $montant = $unFraisHorsForfait['montant'];
                    $id = $unFraisHorsForfait['id']; ?>           
                    <tr>
                        <td> <?php echo $date ?></td>
                        <td> <?php echo $libelle ?></td>
                        <td><?php echo $montant ?></td>
                        <td><a href="index.php?uc=gererFrais&action=supprimerFrais&idFrais=<?php echo $id ?>" 
                               onclick="return confirm('Voulez-vous vraiment supprimer ce frais?');">Supprimer ce frais</a></td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>  
            </table>
        </div>
    </div>
    <div class="row">
        <h3>Nouvel élément hors forfait</h3>
        <div class="col-md-4">
            <form action="index.php?uc=gererFrais&action=validerCreationFrais" 
                  method="post" role="form">
                <div class="form-group">
                    <label for="txtDateHF">Date (jj/mm/aaaa): </label>
                    <input type="text" id="txtDateHF" name="dateFrais" 
                           class="form-control" id="text">
                </div>
                <div class="form-group">
                    <label for="txtLibelleHF">Libellé</label>             
                    <input type="text" id="txtLibelleHF" name="libelle" 
                           class="form-control" id="text">
                </div> 
                <div class="form-group">
                    <label for="txtMontantHF">Montant : </label>
                    <div class="input-group">
                        <span class="input-group-addon">€</span>
                        <input type="text" id="txtMontantHF" name="montant" 
                               class="form-control" value="">
                    </div>
                </div>
                <button class="btn btn-success" type="submit">Ajouter</button>
                <button class="btn btn-danger" type="reset">Effacer</button>
            </form>
        </div>
    </div>
<?php
} else {
?>
	<hr>
    <div class="row">
        <div class="panel panel-secondary">
            <div class="panel-heading">Descriptif des éléments hors forfait</div>
            <table class="table table-bordered table-responsive">
                <thead>
                    <tr>
                        <th class="date">Date</th>
                        <th class="libelle">Libellé</th>  
                        <th class="montant">Montant</th>  
                        <th class="action">&nbsp;</th> 
                    </tr>
                </thead>  
                <tbody>
                <?php
                foreach ($lesFraisHorsForfait as $unFraisHorsForfait) {
                    $libelle = htmlspecialchars($unFraisHorsForfait['libelle']);
                    $date = $unFraisHorsForfait['date'];
                    $montant = $unFraisHorsForfait['montant'];
                    $id = $unFraisHorsForfait['id']; ?>
                    <form method="post" 
                  	action="index.php?uc=gererFrais&action=reporterFraisHorsForfait" 
                  	role="form">  
                  	<input type="hidden" id="selectVisiteur" name="selectVisiteur" value="<?php echo $selectVisiteur?>">
                  	<input type="hidden" id="selectMois" name="selectMois" value="<?php echo $selectMois?>">  
                  	<input type="hidden" id="idFraisHorsForfait" name="idFraisHorsForfait" value="<?php echo $id?>">                    	       
                    <tr>
                        <td> <input type="text" id="dateFraisHF" 
                                   name="dateFraisHF"
                                   size="5" maxlength="10" 
                                   value="<?php echo $date ?>" 
                                   disabled="disabled" 
                                   class="form-control">
                        </td>
                        <td> <input type="text" id="libelleFraisHF" 
                                   name="libelleFraisHF"
                                   size="20" maxlength="50" 
                                   value="<?php echo $libelle ?>" 
                                   disabled="disabled" 
                                   class="form-control">
                        </td>
                        <td><input type="text" id="montantFraisHF" 
                                   name="montantFraisHF"
                                   size="5" maxlength="5" 
                                   value="<?php echo $montant ?>" 
                                   disabled="disabled" 
                                   class="form-control">
                        </td>
                        <td><button class="btn btn-warning" type="submit">Reporter</button>
                    		<button class="btn btn-danger" type="submit" 
                    			formaction="index.php?uc=gererFrais&action=refuserFraisHorsForfait" >Refusé</button>
                    	</td>
                    </tr>
                    </form>
                    <?php
                }
                ?>
                </tbody>  
            </table>
        </div>
          	<table class="table-form">
          		<tbody>
              		<tr>
              		<form method="post" 
                  	action="index.php?uc=gererFrais&action=validerFicheFrais" 
                  	role="form">         		  
                  		<td><label for="nbJustificatifs">Nombre de justificatifs : </label></td>      
                      	<td><input type="text" id="nbJustificatifs" name="nbJustificatifs" size="5" maxlength="3"
                      		value="<?php echo $nbJustificatifs;?>" class="form-control" disabled="disabled">
                      		<input type="hidden" id="selectVisiteur" name="selectVisiteur" value=<?php echo $selectVisiteur;?> >
                      		<input type="hidden" id="selectMois" name="selectMois" value=<?php echo $selectMois;?> >
                  		</td>
                      	<td><button class="btn btn-success" type="submit">Valider la fiche</button></td>
              		</form>
                    </tr>
                </tbody>
           </table> 
    </div>
<?php }