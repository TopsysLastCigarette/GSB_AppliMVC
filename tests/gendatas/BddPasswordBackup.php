<?php
/**
 * Réinitialisation des mots de passe originels de la base de données
 *
 * Created by PhpStorm.
 * User: monst
 * Date: 25/11/2018
 * Time: 18:15
 */

$_serveur = 'localhost';
$_bdd = 'gsb_frais_backup';
$_user = 'root';
$_mdp = '';

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
    .'FROM visiteur '
);
$requetePrepare->execute();
$lesLignes = $requetePrepare->fetchAll();

$connexion=null;
$_bdd = 'gsb_frais';

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

foreach($lesLignes as $uneLigne){
    $requetePrepare = $connexion->prepare(
        'UPDATE visiteur '
        .'SET visiteur.mdp = :unMdp '
        .'WHERE visiteur.id = :unVisiteur '
    );
    $requetePrepare->bindParam(':unMdp',$uneLigne['mdp'], PDO::PARAM_STR);
    $requetePrepare->bindParam(':unVisiteur', $uneLigne['id'], PDO::PARAM_STR);
    $requetePrepare->execute();
}

