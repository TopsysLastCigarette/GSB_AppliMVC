<?php
/**
 * Validation des frais
 *
 * PHP version 7
 *
 * @category  PPE
 * @package   GSB
 * @author    Joël Deligne <monsters.freaks@gmail.com>
 * @version   GIT: <0>
 * @link      http://www.reseaucerta.org Contexte « Laboratoire GSB »
 */

$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);
$lesVisiteurs = $pdo->getVisiteurs();
switch($action){
case 'selectionVisiteur':
    $idVisiteur = filter_input(INPUT_POST, 'lstVisiteurs', FILTER_SANITIZE_STRING);
    //Si aucun visiteur n'est selectionné dans lstVisiteurs, le premier de la liste le sera par défaut
    if (!isset($idVisiteur)) {
        $idVisiteur = $lesVisiteurs[0]['id'];
    }
    $lesMois = $pdo->getLesMoisDisponibles($idVisiteur);
    include 'vues/v_listeVisiteurs.php';
    break;
case 'voirEtatFrais':
    $idVisiteur = filter_input(INPUT_GET, 'visiteur', FILTER_SANITIZE_STRING);
    $infoVisiteur = $pdo->getNomVisiteur($idVisiteur);
    $nomVisiteur = $infoVisiteur['nom'];
    $prenomVisiteur = $infoVisiteur['prenom'];
    $lesMois = $pdo->getLesMoisDisponibles($idVisiteur);
    $leMois = filter_input(INPUT_POST, 'lstMois', FILTER_SANITIZE_STRING);
    include 'vues/v_listeVisiteurs.php';
    $lesFraisForfait = $pdo->getLesFraisForfait($idVisiteur, $leMois);
    $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idVisiteur,$leMois);
    include 'vues/v_correctionFraisForfait.php';
    include 'vues/v_correctionFraisHorsForfait.php';
    break;
case 'validerFrais':
    //action validerFrais
    break;
case 'corrigerFraisForfait':
    //action Correction de frais forfaitisés
    break;
case 'corrigerFraisHorsForfait':
    //action correction frais hors forfait
    break;
case 'reinitialiserFraisForfait':
     //action Reinitialisation frais forfait
    break;
case 'reinitialiserFraisHorsForfait':
     //action Reinitialisation frais hors forfait
    break;
}