<?php
/**
 * Vue Tableau des fiches de frais disponibles
 *
 * PHP version 7
 *
 * @category PPE
 * @package  GSB
 * @author   Joël Deligne <monsters.freaks@gmail.com>
 * @version  GIT: <0>
 * @link     http://www.reseaucerta.org Contexte « Laboratoire GSB »
 */
?>
<div class="panel panel-info">
    <div class="panel-heading">Liste des fiches de frais</div>
    <form method="post"
          action="index.php?uc=suivrePaiement&action=voirFicheFrais"
          role="form">
        <table class="table table-bordered table-responsive">
            <tr>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Mois</th>
                <th>Etat</th>
            </tr>
            <?php
            foreach ($lesFiches as $uneFiche) {
                ?>
                <tr>
                    <th> <?php echo $uneFiche['nom'] ?></th>
                    <th> <?php echo $uneFiche['prenom'] ?></th>
                    <th> <?php echo $uneFiche['mois'].'/'.$uneFiche['annee'] ?></th>
                    <th> <?php echo $uneFiche['etat'] ?></th>
                    <input type="hidden" name ="leVisiteur" value="<?php echo $uneFiche['idVisiteur']?>">
                    <input type="hidden" name ="leMois" value="<?php echo $uneFiche['moisComplet']?>">
                    <th><button class="btn btn-info" type="submit">Voir fiche</button></th>
                </tr>
                <?php
            }
            ?>
        </table>
    </form>
</div>