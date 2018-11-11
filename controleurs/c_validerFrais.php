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
$idVisiteur = filter_input(INPUT_POST, 'lstVisiteurs', FILTER_SANITIZE_STRING);
//Si aucun visiteur n'est selectionné dans lstVisiteurs, le premier de la liste le sera par défaut
if (!isset($idVisiteur)) {
    $idVisiteur = $lesVisiteurs[0]['id'];
}
$lesMois = $pdo->getLesMoisDisponibles($idVisiteur);
$leMois = filter_input(INPUT_POST, 'lstMois', FILTER_SANITIZE_STRING);
//Si aucun mois n'est selectionné dans lstMois, le premier de la liste le sera par défaut
if (!isset($leMois)) {
    $leMois = $lesMois[0];
}
require 'vues/v_listeVisiteurs.php';

switch($action){
case 'voirEtatFrais':
    $infoVisiteur = $pdo->getNomVisiteur($idVisiteur);
    $nomVisiteur = $infoVisiteur['nom'];
    $prenomVisiteur = $infoVisiteur['prenom'];

    //Recuperation de la mise à jour à faire
    $maj = filter_input(INPUT_GET, 'maj', FILTER_SANITIZE_STRING);
    if (isset($maj)) {
        switch($maj) {
        case 'correctionFraisForfait':
            $lesFrais
                = filter_input(
                    INPUT_POST, 'lesFrais', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY
                );
            if (lesQteFraisValides($lesFrais)) {
                $pdo->majFraisForfait($idVisiteur, $leMois, $lesFrais);
                ajouterMajFrais('Les frais forfaitisés ont été mis à jour pour '.$nomVisiteur.' '.$prenomVisiteur);
                include 'vues/v_maj.php';
            } else {
                ajouterErreur('Les valeurs des frais doivent être numériques');
                include 'vues/v_erreurs.php';
            }
            break;
        case 'correctionFraisHorsForfait':
            $actionButton = filter_input(INPUT_POST, 'actionButton', FILTER_SANITIZE_STRING);
            $leFrais = filter_input(INPUT_POST, 'leFrais', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
            switch($actionButton) {
            case 'corriger':
                valideInfosFrais($leFrais['date'], $leFrais['libelle'], $leFrais['montant']);
                if (nbErreurs() > 0) {
                    include 'vues/v_erreurs.php';
                } else {
                    $pdo->majFraisHorsForfait($idVisiteur, $leMois, $leFrais);
                    ajouterMajFrais('Les frais hors forfait ont été mis à jour pour '.$nomVisiteur.' '.$prenomVisiteur);
                    include 'vues/v_maj.php';
                }
                break;
            case 'supprimer' :
                //some Code
                break;
            }
            break;
        }
    }

    $lesFraisForfait = $pdo->getLesFraisForfait($idVisiteur, $leMois);
    $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idVisiteur, $leMois);
    $nbJustificatifs = $pdo->getNbjustificatifs($idVisiteur, $leMois);
    include 'vues/v_correctionFraisForfait.php';
    include 'vues/v_correctionFraisHorsForfait.php';
    include 'vues/v_nbJustificatifs.php';
    break;
case 'validerFicheVisiteur':
    //code ici
    break;
}

