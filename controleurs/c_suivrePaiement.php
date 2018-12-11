<?php
/**
 * Suivi des paiement de frais
 *
 * PHP version 7
 *
 * @category PPE
 * @package  GSB
 * @author   Joël Deligne <monsters.freaks@gmail.com>
 * @version  GIT: <0>
 * @link     http://www.reseaucerta.org Contexte « Laboratoire GSB »
 */

$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);
$lesVisiteurs = $pdo->getVisiteurs();
$idVisiteur = filter_input(INPUT_POST, 'lstVisiteurs', FILTER_SANITIZE_STRING);
//Si aucun visiteur n'est selectionné dans lstVisiteurs, le premier de la liste le sera par défaut
if (!isset($idVisiteur)) {
    $idVisiteur = $lesVisiteurs[0]['id'];
}
$infoVisiteur = $pdo->getNomVisiteur($idVisiteur);
$nomVisiteur = $infoVisiteur['nom'];
$prenomVisiteur = $infoVisiteur['prenom'];
$lesMois = $pdo->getLesMoisParEtat($idVisiteur, 'VA', 'MP', 'RB');
$leMois = filter_input(INPUT_POST, 'lstMois', FILTER_SANITIZE_STRING);
//Si aucun mois n'est selectionné dans lstMois, le premier de la liste le sera par défaut
if (!isset($leMois)) {
    $leMois = $lesMois[0];
}
require 'vues/v_listeVisiteurs.php';

if (isset($action)) {

    $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idVisiteur, $leMois);
    $lesFraisForfait = $pdo->getLesFraisForfait($idVisiteur, $leMois);
    $lesInfosFicheFrais = $pdo->getLesInfosFicheFrais($idVisiteur, $leMois);
    $numAnnee = substr($leMois, 0, 4);
    $numMois = substr($leMois, 4, 2);
    $libEtat = $lesInfosFicheFrais['libEtat'];
    $montantValide = $lesInfosFicheFrais['montantValide'];
    $nbJustificatifs = $lesInfosFicheFrais['nbJustificatifs'];
    $dateModif = dateAnglaisVersFrancais($lesInfosFicheFrais['dateModif']);

    switch($action) {
    case 'miseEnPaiement':
        if ($libEtat === 'Mise en paiement') {
             ajouterErreur('La mise en paiement de cette fiche de frais a déjà été faites.');
             include 'vues/v_erreurs.php';
        } else {
            $pdo->majEtatFicheFrais($idVisiteur, $leMois, 'MP');
            $libEtat = 'Mise en paiement';
            ajouterMajFrais('La mise en paiement de la fiche de frais a été effectuée');
            include 'vues\v_maj.php';
        }
        break;
    case 'estRemboursé' :
        if ($libEtat === 'Validée') {
            ajouterErreur('Une fiche de frais doit d\'abord être mise en paiement');
            include 'vues/v_erreurs.php';
        } else {
            $pdo->majEtatFicheFrais($idVisiteur, $leMois, 'RB');
            $libEtat = 'Remboursée';
            ajouterMajFrais('La fiche de frais est bien enregistré comme étant remboursée');
            include 'vues\v_maj.php';
        }
        break;
    }
    include 'vues/v_etatFrais.php';
    //Affichage des boutons seulement si les frais ne sont pas déjà remboursés
    if ($libEtat != 'Remboursée') {
        include 'vues/v_gestionPaiement.php';
    }
    include 'vues/v_telechargerPDF.php';
}