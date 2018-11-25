<?php
/**
 * Vue Gestion des paiements
 *
 * PHP Version 7
 *
 * @category PPE
 * @package  GSB
 * @author   Joël Deligne <monsters.freaks@gmail.com>
 * @version  GIT: <0>
 * @link     http://www.reseaucerta.org Contexte « Laboratoire GSB »
 */
?>

<div class="button-submit">
    <form method="post"
          action=""
          role="form">
        <input name="lstVisiteurs" value ="<?php echo $idVisiteur ?>" type="hidden">
        <input name="lstMois" value ="<?php echo $leMois ?>" type="hidden">
        <button class="btn btn-success"
                onclick="this.form.action='index.php?uc=suivrePaiement&action=miseEnPaiement';"
                type="submit">Mettre en paiement
        </button>
        <button class="btn btn-info"
                onclick="this.form.action='index.php?uc=suivrePaiement&action=estRemboursé';"
                type="submit">Frais remboursé
        </button>
    </form>
</div>
