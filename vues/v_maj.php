<?php
/**
 * Vue Mise à jour
 *
 * PHP version 7
 *
 * @category  PPE
 * @package   GSB
 * @author    Joël Deligne <monsters.freaks@gmail.com>
 * @version   GIT: <0>
 * @link      http://www.reseaucerta.org Contexte « Laboratoire GSB »
 */
?>
<div class="alert alert-success">
    <?php
    foreach ($_REQUEST['infoMaj'] as $maj) {
        echo '<p>' . htmlspecialchars($maj) . '</p>';
    }
    ?>
</div>