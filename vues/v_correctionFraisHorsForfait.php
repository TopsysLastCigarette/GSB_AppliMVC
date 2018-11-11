<?php
/**
* Vue Correction des frais hors forfait
*
* PHP Version 7
*
* @category  PPE
* @package   GSB
* @author    Joël Deligne <monsters.freaks@gmail.com>
* @version   GIT: <0>
* @link      http://www.reseaucerta.org Contexte « Laboratoire GSB »
*/
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
                <?php
                foreach ($lesFraisHorsForfait as $unFraisHorsForfait) {
                    $libelle = htmlspecialchars($unFraisHorsForfait['libelle']);
                    $date = $unFraisHorsForfait['date'];
                    $montant = $unFraisHorsForfait['montant'];
                    $id = $unFraisHorsForfait['id']; ?>
                    <form method="post"
                      action="index.php?uc=validationFrais&action=voirEtatFrais&maj=correctionFraisHorsForfait"
                      role="form">
                        <tr>
                            <td><input type="text" id="date"
                                name="leFrais[date]"
                                value="<?php echo $date ?>" size="12"></td>
                            <td><input type="text" id="libelle"
                                       name="leFrais[libelle]"
                                       value="<?php echo $libelle ?>" size="30"></td>
                            <td><input type="text" id="montant"
                                       name="leFrais[montant]"
                                       value="<?php echo $montant ?>"></td>
                            <input type="hidden" name=leFrais[id] value="<?php echo $id ?>">
                            <input name="lstVisiteurs" value ="<?php echo $idVisiteur ?>" type="hidden">
                            <input name="lstMois" value ="<?php echo $leMois ?>" type="hidden">
                            <td><button class="btn btn-danger" type="submit" name="actionButton" value="supprimer"
                                        onclick="return confirm('Voulez-vous vraiment supprimer ce frais?');">
                                    Supprimer
                                </button>
                                <button class="btn btn-success" type="submit" name="actionButton" value="corriger">
                                        Corriger
                                </button>
                                <button type="reset" class="btn btn-danger">Réinitialiser</button>
                            </td>
                        </tr>
                    </form>
                    <?php
                }
                ?>

                </tbody>
            </table>
    </div>
</div>
