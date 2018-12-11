<?php
/**
 * Index du projet GSB
 *
 * PHP Version 7
 *
 * @category  PPE
 * @package   GSB
 * @author    Réseau CERTA <contact@reseaucerta.org>
 * @author    José GIL <jgil@ac-nice.fr>
 * @copyright 2017 Réseau CERTA
 * @license   Réseau CERTA
 * @version   GIT: <0>
 * @link      http://www.reseaucerta.org Contexte « Laboratoire GSB »
 */
use Mpdf\Mpdf;

require_once 'includes/fct.inc.php';
require_once 'includes/class.pdogsb.inc.php';

session_start();
$pdo = PdoGsb::getPdoGsb();
$estConnecte = estConnecte();

//Appel du générateur de PDF si un telechargement a été demandé (doit s'appeller avant le HTML)
if ($estConnecte && isset($_GET['download'])
    && $_GET['download']==='PDF'
    && $_SESSION['type']==='0'
) {
    include_once 'controleurs/c_download.php';
}

if (isset($_SESSION['type']) && $_SESSION['type']==='0') {
    include_once 'vues/v_enteteComptable.php';
} else {
    include_once 'vues/v_entete.php';
}

$uc = filter_input(INPUT_GET, 'uc', FILTER_SANITIZE_STRING);
if ($uc && !$estConnecte) {
    $uc = 'connexion';
} elseif (empty($uc)) {
    $uc = 'accueil';
}

switch ($uc) {
case 'connexion':
    include 'controleurs/c_connexion.php';
    break;
case 'accueil':
    include 'controleurs/c_accueil.php';
    break;
case 'gererFrais':
    include 'controleurs/c_gererFrais.php';
    break;
case 'etatFrais':
    include 'controleurs/c_etatFrais.php';
    break;
case 'validationFrais':
    //Vérification qu'un visiteur connecté ne puisse accéder au controleur du comptable
    if ($_SESSION['type']==='0') {
        include 'controleurs/c_validerFrais.php';
    } else {
        ajouterErreur('Accès interdit');
        include 'vues/v_erreurs.php';
    }
    break;
case 'suivrePaiement':
    if ($_SESSION['type']==='0') {
        include 'controleurs/c_suivrePaiement.php';
    } else {
        ajouterErreur('Accès interdit');
        include 'vues/v_erreurs.php';
    }
    break;
case 'deconnexion':
    include 'controleurs/c_deconnexion.php';
    break;
}

require 'vues/v_pied.php';
