<?php
/**
 * Created by PhpStorm.
 * User: monst
 * Date: 02/12/2018
 * Time: 17:23
 */
?>
<div class="telechargerPDF">
    <form method="post"
          action=""
          role="form">
        <input name="lstVisiteurs" value ="<?php echo $idVisiteur ?>" type="hidden">
        <input name="nomVisiteur" value ="<?php echo $nomVisiteur ?>" type="hidden">
        <input name="prenomVisiteur" value ="<?php echo $prenomVisiteur ?>" type="hidden">
        <input name="lstMois" value ="<?php echo $leMois ?>" type="hidden">
        <button class="btn btn-warning"
                onclick="this.form.action='index.php?download=PDF';"
                type="submit">Télécharger PDF
        </button>
    </form>
</div>