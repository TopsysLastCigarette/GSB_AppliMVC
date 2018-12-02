<?php
/**
 * Vue Liste des visiteurs
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

<div class="selection-visiteur">
    <form action="index.php?uc=<?php echo $_GET['uc']?>"
          method="post" role="form">
        <div class="form-comptable">
            <label id="lblVisiteurs" for="lstVisiteurs" accesskey="n">Choisir le visiteur:</label>
            <select class="select2" id="lstVisiteurs" name="lstVisiteurs"
                    onchange="if(this.value != 0) { this.form.submit(); }">
                <?php
                foreach ($lesVisiteurs as $unVisiteur) {
                    $id = $unVisiteur['id'];
                    $prenom = $unVisiteur['prenom'];
                    $nom = $unVisiteur['nom'];
                    if ($id == $idVisiteur) {
                        ?>
                        <option selected value="<?php echo $id ?>">
                            <?php echo $nom.' '.$prenom ?></option>
                        <?php
                    } else {
                        ?>
                        <option value="<?php echo $id ?>">
                            <?php echo $nom.' '.$prenom ?></option>
                        <?php
                    }
                }
                ?>
            </select>
        </div>
    </form>
    <form action="index.php?uc=<?php echo $_GET['uc']?>&action=afficherEtat"
          method="post" role="form">
        <div class="form-comptable">
            <label id="lblMois" for="lstMois" accesskey="n">Mois :</label>
            <input id="visiteur" name="lstVisiteurs" value="<?php echo $idVisiteur ?>" type="hidden">
            <select id="lstMois" name="lstMois" class="form-control">
                <?php
                foreach ($lesMois as $unMois) {
                    $mois = $unMois['mois'];
                    $numAnnee = $unMois['numAnnee'];
                    $numMois = $unMois['numMois'];
                    if ($mois == $leMois) {
                        ?>
                        <option selected value="<?php echo $mois ?>">
                            <?php echo $numMois . '/' . $numAnnee ?></option>
                        <?php
                    } else {
                        ?>
                        <option value="<?php echo $mois ?>">
                            <?php echo $numMois . '/' . $numAnnee ?></option>
                        <?php
                    }
                }
                ?>
                <input id="ok" type="submit" value="Valider" class="btn btn-success"
                       role="button">
            </select>
        </div>
    </form>
</div>