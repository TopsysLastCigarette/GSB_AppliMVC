<?php
/**
 * Vue correction des frais forfaitisé
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
<div class="row">
    <h2>Valider la fiche de frais de
        <?php echo $nomVisiteur.' '.$prenomVisiteur ?>
    </h2>
    <h3>Eléments forfaitisés</h3>
    <div class="col-md-4">
        <form method="post"
              action="index.php?uc=validationFrais&action=voirEtatFrais&maj=correctionFraisForfait"
              role="form">
            <input name="lstVisiteurs" value ="<?php echo $idVisiteur ?>" type="hidden">
            <input name="lstMois" value ="<?php echo $leMois ?>" type="hidden">
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
