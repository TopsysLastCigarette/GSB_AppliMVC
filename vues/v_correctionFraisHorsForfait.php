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
            <tbody>
            <?php
            foreach ($lesFraisHorsForfait as $unFraisHorsForfait) {
                $libelle = htmlspecialchars($unFraisHorsForfait['libelle']);
                $date = $unFraisHorsForfait['date'];
                $montant = $unFraisHorsForfait['montant'];
                $id = $unFraisHorsForfait['id']; ?>
                <tr>
                    <td><input type="text" id="date"
                        name="leFrais[<?php echo $date ?>]"
                        value="<?php echo $date ?>" size="12"></td>
                    <td><input type="text" id="libelle"
                               name="leFrais[<?php echo $libelle ?>]"
                               value="<?php echo $libelle ?>" size="30"></td>
                    <td><input type="text" id="montant"
                               name="leFrais[<?php echo $montant ?>]"
                               value="<?php echo $montant ?>"></td>
                    <td><a href="index.php?uc=gererFrais&action=supprimerFrais&idFrais=<?php echo $id ?>"
                           onclick="return confirm('Voulez-vous vraiment supprimer ce frais?');">Supprimer ce frais</a>
                        <button class="btn btn-success" type="submit">Corriger</button>
                        <button class="btn btn-danger" type="reset">Réinitialiser</button>
                    </td>
                </tr>
                <?php
            }
            ?>

            </tbody>
        </table>
    </div>
</div>
