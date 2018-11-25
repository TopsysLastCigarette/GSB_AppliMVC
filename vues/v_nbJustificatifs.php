<?php
/**
 * Vue nombre de justificatif
 *
 * PHP version 7
 *
 * @category  PPE
 * @package   GSB
 * @author    Joël Deligne <monsters.freaks@gmail.com>
 * @version   GIT: <0>
 * @link      http://www.reseaucerta.org Contexte « Laboratoire GSB »
 */

if (!isset($nbJustificatifs)) {
    $nbJustificatifs = 0;
}
?>
<form method="post"
      action="index.php?uc=validationFrais&action=validationComptable"
      role="form">
    <label>Nombres de justificatifs :</label>
    <input name="nbJustificatifs" value="<?php echo $nbJustificatifs ?>"
           size="10">
    <input name="lstVisiteurs" value ="<?php echo $idVisiteur ?>" type="hidden">
    <input name="lstMois" value ="<?php echo $leMois ?>" type="hidden">
    <div class="button-submit">
        <button class="btn btn-success" type=submit>Valider</button>
        <button class="btn btn-danger" type="reset">Réinitialiser</button>
    </div>
</form>

