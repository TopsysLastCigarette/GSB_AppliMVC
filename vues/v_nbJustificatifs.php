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
<div class="col-md-4">
    <label>Nombres de justificatifs :</label>
    <input name="nbJustificatifs" value="<?php echo $nbJustificatifs ?>"
           size="10">
</div>
