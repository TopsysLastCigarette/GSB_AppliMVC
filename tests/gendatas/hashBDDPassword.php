<?php
/**
 * Hash des mots de passe dans la BDD GSB
 *
 * Created by PhpStorm.
 * User: monst
 * Date: 25/11/2018
 * Time: 18:15
 */

$_serveur = 'localhost';
$_bdd = 'gsb_frais';
$_user = 'userGsb';
$_mdp = 'secret';

try {
    $connexion = new PDO('mysql:host='.$_serveur.';dbname='.$_bdd, $_user, $_mdp);
    $connexion->query('SET CHARACTER SET utf8');
    // set the PDO error mode to exception
    $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected successfully";
}
catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

$requetePrepare = $connexion->prepare(
    'SELECT visiteur.id, visiteur.mdp '
            .' FROM visiteur'
);
$requetePrepare->execute();
$lesLignes= $requetePrepare->fetchAll();

$lesNouvellesLignes = array();

foreach ($lesLignes as $uneLigne) {
    $leLogin = $uneLigne['id'];
    $leMDP = $uneLigne['mdp'];
    $leNouveauMDP = password_hash($leMDP,  PASSWORD_BCRYPT);
    array_push(
        $lesNouvellesLignes, [
            'id' => $leLogin,
            'MDP' => $leNouveauMDP
            ]
    );
}

foreach ($lesNouvellesLignes as $uneLigne) {
    $requetePrepare = $connexion->prepare(
        'UPDATE visiteur '
        .'SET visiteur.mdp = :unMDP '
        .'WHERE visiteur.id = :unVisiteur'
    );
    $requetePrepare->bindParam(':unVisiteur', $uneLigne['id'], PDO::PARAM_STR);
    $requetePrepare->bindParam(':unMDP', $uneLigne['MDP'], PDO::PARAM_STR);
    $requetePrepare->execute();
}


$connexion = null;