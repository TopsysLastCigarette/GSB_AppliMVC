<?php
/**
 * Classe d'accès aux données.
 *
 * PHP Version 7
 *
 * @category  PPE
 * @package   GSB
 * @author    Cheri Bibi - Réseau CERTA <contact@reseaucerta.org>
 * @author    José GIL - CNED <jgil@ac-nice.fr>
 * @copyright 2017 Réseau CERTA
 * @license   Réseau CERTA
 * @version   GIT: <0>
 * @link      http://www.php.net/manual/fr/book.pdo.php PHP Data Objects sur php.net
 */

/**
 * Classe d'accès aux données.
 *
 * Utilise les services de la classe PDO
 * pour l'application GSB
 * Les attributs sont tous statiques,
 * les 4 premiers pour la connexion
 * $monPdo de type PDO
 * $monPdoGsb qui contiendra l'unique instance de la classe
 *
 * PHP Version 7
 *
 * @category  PPE
 * @package   GSB
 * @author    Cheri Bibi - Réseau CERTA <contact@reseaucerta.org>
 * @author    José GIL <jgil@ac-nice.fr>
 * @copyright 2017 Réseau CERTA
 * @license   Réseau CERTA
 * @version   Release: 1.0
 * @link      http://www.php.net/manual/fr/book.pdo.php PHP Data Objects sur php.net
 */

class PdoGsb
{
    private static $_serveur = 'mysql:host=localhost';
    private static $_bdd = 'dbname=gsb_frais';
    private static $_user = 'userGsb';
    private static $_mdp = 'secret';
    private static $_monPdo;
    private static $_monPdoGsb = null;

    /**
     * Constructeur privé, crée l'instance de PDO qui sera sollicitée
     * pour toutes les méthodes de la classe
     */
    private function __construct()
    {
        PdoGsb::$_monPdo = new PDO(
            PdoGsb::$_serveur . ';' . PdoGsb::$_bdd,
            PdoGsb::$_user,
            PdoGsb::$_mdp
        );
        PdoGsb::$_monPdo->query('SET CHARACTER SET utf8');
    }

    /**
     * Méthode destructeur appelée dès qu'il n'y a plus de référence sur un
     * objet donné, ou dans n'importe quel ordre pendant la séquence d'arrêt.
     */
    public function __destruct()
    {
        PdoGsb::$_monPdo = null;
    }

    /**
     * Fonction statique qui crée l'unique instance de la classe
     * Appel : $instancePdoGsb = PdoGsb::getPdoGsb();
     *
     * @return Object_ l'unique objet de la classe PdoGsb
     */
    public static function getPdoGsb()
    {
        if (PdoGsb::$_monPdoGsb == null) {
            PdoGsb::$_monPdoGsb = new PdoGsb();
        }
        return PdoGsb::$_monPdoGsb;
    }

    /**
     * Retourne les informations d'un visiteur
     *
     * @param String $login Login du visiteur
     * @param String $mdp   Mot de passe du visiteur
     *
     * @return Array l'id, le nom, le prénom et le type sous la forme d'un tableau associatif
     */
    public function getInfosVisiteur($login, $mdp)
    {
        $requetePrepare = PdoGsb::$_monPdo->prepare(
            'SELECT visiteur.id AS id, visiteur.nom AS nom, '
            . 'visiteur.prenom AS prenom, visiteur.type AS type '
            . 'FROM visiteur '
            . 'WHERE visiteur.login = :unLogin AND visiteur.mdp = :unMdp'
        );
        $requetePrepare->bindParam(':unLogin', $login, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMdp', $mdp, PDO::PARAM_STR);
        $requetePrepare->execute();
        return $requetePrepare->fetch();
    }

    /**
     * Retourne sous forme d'un tableau associatif toutes les lignes de frais
     * hors forfait concernées par les deux arguments.
     * La boucle foreach ne peut être utilisée ici car on procède
     * à une modification de la structure itérée - transformation du champ date-
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return Array tous les champs des lignes de frais hors forfait sous la forme
     * d'un tableau associatif
     */
    public function getLesFraisHorsForfait($idVisiteur, $mois)
    {
        $requetePrepare = PdoGsb::$_monPdo->prepare(
            'SELECT * FROM lignefraishorsforfait '
            . 'WHERE lignefraishorsforfait.idvisiteur = :unIdVisiteur '
            . 'AND lignefraishorsforfait.mois = :unMois'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $lesLignes = $requetePrepare->fetchAll();
        for ($i = 0; $i < count($lesLignes); $i++) {
            $date = $lesLignes[$i]['date'];
            $lesLignes[$i]['date'] = dateAnglaisVersFrancais($date);
        }
        return $lesLignes;
    }

    /**
     * Retourne le nombre de justificatif d'un visiteur pour un mois donné
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return Integer le nombre entier de justificatifs
     */
    public function getNbjustificatifs($idVisiteur, $mois)
    {
        $requetePrepare = PdoGsb::$_monPdo->prepare(
            'SELECT fichefrais.nbjustificatifs as nb FROM fichefrais '
            . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
            . 'AND fichefrais.mois = :unMois'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $laLigne = $requetePrepare->fetch();
        return $laLigne['nb'];
    }

    /**
     * Retourne sous forme d'un tableau associatif toutes les lignes de frais
     * au forfait concernées par les deux arguments
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return Array l'id, le libelle et la quantité sous la forme d'un tableau
     * associatif
     */
    public function getLesFraisForfait($idVisiteur, $mois)
    {
        $requetePrepare = PdoGSB::$_monPdo->prepare(
            'SELECT fraisforfait.id as idfrais, '
            . 'fraisforfait.libelle as libelle, '
            . 'lignefraisforfait.quantite as quantite '
            . 'FROM lignefraisforfait '
            . 'INNER JOIN fraisforfait '
            . 'ON fraisforfait.id = lignefraisforfait.idfraisforfait '
            . 'WHERE lignefraisforfait.idvisiteur = :unIdVisiteur '
            . 'AND lignefraisforfait.mois = :unMois '
            . 'ORDER BY lignefraisforfait.idfraisforfait'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        return $requetePrepare->fetchAll();
    }

    /**
     * Retourne tous les id de la table FraisForfait
     *
     * @return Array un tableau associatif
     */
    public function getLesIdFrais()
    {
        $requetePrepare = PdoGsb::$_monPdo->prepare(
            'SELECT fraisforfait.id as idfrais '
            . 'FROM fraisforfait ORDER BY fraisforfait.id'
        );
        $requetePrepare->execute();
        return $requetePrepare->fetchAll();
    }

    /**
     * Met à jour la table ligneFraisForfait
     * Met à jour la table ligneFraisForfait pour un visiteur et
     * un mois donné en enregistrant les nouveaux montants
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     * @param Array  $lesFrais   tableau associatif de clé idFrais et
     *                           de valeur la quantité pour ce frais
     *
     * @return null
     */
    public function majFraisForfait($idVisiteur, $mois, $lesFrais)
    {
        $lesCles = array_keys($lesFrais);
        foreach ($lesCles as $unIdFrais) {
            $qte = $lesFrais[$unIdFrais];
            $requetePrepare = PdoGSB::$_monPdo->prepare(
                'UPDATE lignefraisforfait '
                . 'SET lignefraisforfait.quantite = :uneQte '
                . 'WHERE lignefraisforfait.idvisiteur = :unIdVisiteur '
                . 'AND lignefraisforfait.mois = :unMois '
                . 'AND lignefraisforfait.idfraisforfait = :idFrais'
            );
            $requetePrepare->bindParam(':uneQte', $qte, PDO::PARAM_INT);
            $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
            $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
            $requetePrepare->bindParam(':idFrais', $unIdFrais, PDO::PARAM_STR);
            $requetePrepare->execute();
        }
    }

    /**
     * Met à jour le nombre de justificatifs de la table ficheFrais
     * pour le mois et le visiteur concerné
     *
     * @param String  $idVisiteur      ID du visiteur
     * @param String  $mois            Mois sous la forme aaaamm
     * @param Integer $nbJustificatifs Nombre de justificatifs
     *
     * @return null
     */
    public function majNbJustificatifs($idVisiteur, $mois, $nbJustificatifs)
    {
        $requetePrepare = PdoGSB::$_monPdo->prepare(
            'UPDATE fichefrais '
            . 'SET nbjustificatifs = :unNbJustificatifs '
            . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
            . 'AND fichefrais.mois = :unMois'
        );
        $requetePrepare->bindParam(
            ':unNbJustificatifs',
            $nbJustificatifs,
            PDO::PARAM_INT
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
    }

    /**
     * Teste si un visiteur possède une fiche de frais pour le mois passé en argument
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return Boolean vrai ou faux
     */
    public function estPremierFraisMois($idVisiteur, $mois)
    {
        $boolReturn = false;
        $requetePrepare = PdoGsb::$_monPdo->prepare(
            'SELECT fichefrais.mois FROM fichefrais '
            . 'WHERE fichefrais.mois = :unMois '
            . 'AND fichefrais.idvisiteur = :unIdVisiteur'
        );
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->execute();
        if (!$requetePrepare->fetch()) {
            $boolReturn = true;
        }
        return $boolReturn;
    }

    /**
     * Retourne le dernier mois en cours d'un visiteur
     *
     * @param String $idVisiteur ID du visiteur
     *
     * @return le mois sous la forme aaaamm
     */
    public function dernierMoisSaisi($idVisiteur)
    {
        $requetePrepare = PdoGsb::$_monPdo->prepare(
            'SELECT MAX(mois) as dernierMois '
            . 'FROM fichefrais '
            . 'WHERE fichefrais.idvisiteur = :unIdVisiteur'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->execute();
        $laLigne = $requetePrepare->fetch();
        $dernierMois = $laLigne['dernierMois'];
        return $dernierMois;
    }

    /**
     * Crée une nouvelle fiche de frais et les lignes de frais au forfait
     * pour un visiteur et un mois donnés
     *
     * Récupère le dernier mois en cours de traitement, met à 'CL' son champs
     * idEtat, crée une nouvelle fiche de frais avec un idEtat à 'CR' et crée
     * les lignes de frais forfait de quantités nulles
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return null
     */
    public function creeNouvellesLignesFrais($idVisiteur, $mois)
    {
        $dernierMois = $this->dernierMoisSaisi($idVisiteur);
        $laDerniereFiche = $this->getLesInfosFicheFrais($idVisiteur, $dernierMois);
        if ($laDerniereFiche['idEtat'] == 'CR') {
            $this->majEtatFicheFrais($idVisiteur, $dernierMois, 'CL');
        }
        $requetePrepare = PdoGsb::$_monPdo->prepare(
            'INSERT INTO fichefrais (idvisiteur,mois,nbjustificatifs,'
            . 'montantvalide,datemodif,idetat) '
            . "VALUES (:unIdVisiteur,:unMois,0,0,now(),'CR')"
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $lesIdFrais = $this->getLesIdFrais();
        foreach ($lesIdFrais as $unIdFrais) {
            $requetePrepare = PdoGsb::$_monPdo->prepare(
                'INSERT INTO lignefraisforfait (idvisiteur,mois,'
                . 'idfraisforfait,quantite) '
                . 'VALUES(:unIdVisiteur, :unMois, :idFrais, 0)'
            );
            $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
            $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
            $requetePrepare->bindParam(
                ':idFrais',
                $unIdFrais['idfrais'],
                PDO::PARAM_STR
            );
            $requetePrepare->execute();
        }
    }

    /**
     * Crée un nouveau frais hors forfait pour un visiteur un mois donné
     * à partir des informations fournies en paramètre
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     * @param String $libelle    Libellé du frais
     * @param String $date       Date du frais au format français jj//mm/aaaa
     * @param Float  $montant    Montant du frais
     *
     * @return null
     */
    public function creeNouveauFraisHorsForfait(
        $idVisiteur,
        $mois,
        $libelle,
        $date,
        $montant
    ) {
        $dateFr = dateFrancaisVersAnglais($date);
        $requetePrepare = PdoGSB::$_monPdo->prepare(
            'INSERT INTO lignefraishorsforfait '
            . 'VALUES (null, :unIdVisiteur,:unMois, :unLibelle, :uneDateFr,'
            . ':unMontant) '
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unLibelle', $libelle, PDO::PARAM_STR);
        $requetePrepare->bindParam(':uneDateFr', $dateFr, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMontant', $montant, PDO::PARAM_INT);
        $requetePrepare->execute();
    }

    /**
     * Supprime le frais hors forfait dont l'id est passé en argument
     *
     * @param String $idFrais ID du frais
     *
     * @return null
     */
    public function supprimerFraisHorsForfait($idFrais)
    {
        $requetePrepare = PdoGSB::$_monPdo->prepare(
            'DELETE FROM lignefraishorsforfait '
            . 'WHERE lignefraishorsforfait.id = :unIdFrais'
        );
        $requetePrepare->bindParam(':unIdFrais', $idFrais, PDO::PARAM_STR);
        $requetePrepare->execute();
    }

    /**
     * Retourne les mois pour lesquel un visiteur a une fiche de frais
     *
     * @param String $idVisiteur ID du visiteur
     *
     * @return Array un tableau associatif de clé un mois -aaaamm- et de valeurs
     *         l'année et le mois correspondant
     */
    public function getLesMoisDisponibles($idVisiteur)
    {
        $requetePrepare = PdoGSB::$_monPdo->prepare(
            'SELECT fichefrais.mois AS mois FROM fichefrais '
            . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
            . 'ORDER BY fichefrais.mois desc'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->execute();
        $lesMois = array();
        while ($laLigne = $requetePrepare->fetch()) {
            $mois = $laLigne['mois'];
            $numAnnee = substr($mois, 0, 4);
            $numMois = substr($mois, 4, 2);
            $lesMois[] = array(
                'mois' => $mois,
                'numAnnee' => $numAnnee,
                'numMois' => $numMois
            );
        }
        return $lesMois;
    }

    /**
     * Retourne les informations d'une fiche de frais d'un visiteur pour un
     * mois donné
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return Array un tableau avec des champs de jointure entre une fiche de frais
     *         et la ligne d'état
     */
    public function getLesInfosFicheFrais($idVisiteur, $mois)
    {
        $requetePrepare = PdoGSB::$_monPdo->prepare(
            'SELECT fichefrais.idetat as idEtat, '
            . 'fichefrais.datemodif as dateModif,'
            . 'fichefrais.nbjustificatifs as nbJustificatifs, '
            . 'fichefrais.montantvalide as montantValide, '
            . 'etat.libelle as libEtat '
            . 'FROM fichefrais '
            . 'INNER JOIN etat ON fichefrais.idetat = etat.id '
            . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
            . 'AND fichefrais.mois = :unMois'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $laLigne = $requetePrepare->fetch();
        return $laLigne;
    }

    /**
     * Modifie l'état et la date de modification d'une fiche de frais.
     * Modifie le champ idEtat et met la date de modif à aujourd'hui.
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     * @param String $etat       Nouvel état de la fiche de frais
     *
     * @return null
     */
    public function majEtatFicheFrais($idVisiteur, $mois, $etat)
    {
        $requetePrepare = PdoGSB::$_monPdo->prepare(
            'UPDATE ficheFrais '
            . 'SET idetat = :unEtat, datemodif = now() '
            . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
            . 'AND fichefrais.mois = :unMois'
        );
        $requetePrepare->bindParam(':unEtat', $etat, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
    }

    /**
     * Retourne tous les visiteurs présent dans la base de données sous forme de tableau
     * associatif.
     *
     * @return Array Retourne l'id, le nom et le prenom de chacun des visiteurs  de la BDD
     */
    public function getVisiteurs()
    {
        $requetePrepare = pdoGSB::$_monPdo->prepare(
            'SELECT visiteur.id AS id, visiteur.nom AS nom, visiteur.prenom AS prenom '
            .'FROM visiteur '
            .'WHERE visiteur.type = 1'
        );
        $requetePrepare->execute();
        $lesVisiteurs = array();
        while ($laLigne = $requetePrepare->fetch()) {
            $id = $laLigne['id'];
            $nom = $laLigne['nom'];
            $prenom = $laLigne['prenom'];
            $lesVisiteurs[] = array(
                'id' => $id,
                'nom' => $nom,
                'prenom' => $prenom
            );
        }
        return $lesVisiteurs;
    }

    /**
     * Fonction permettant de récuperer le nom et le prenom d'un visiteur en passant
     * son id en paramètre
     *
     * @param String $idVisiteur ID du visiteur
     *
     * @return Array le prenom et le nom du visiteur dans un tableau associatif
     */
    public function getNomVisiteur($idVisiteur)
    {
        $requetePrepare = pdoGSB::$_monPdo->prepare(
            'SELECT visiteur.nom AS nom, visiteur.prenom AS prenom '
            .'FROM visiteur '
            .'WHERE visiteur.id = :unIdVisiteur'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->execute();
        $laLigne = $requetePrepare->fetch();
        $infoVisiteur = array(
            'nom'=>$laLigne['nom'],
            'prenom'=>$laLigne['prenom']
        );
        return $infoVisiteur;
    }

    /**
     * Fonction permettant de connaitre le type de l'utilisateur
     * en passant son id en paramètre
     *
     * @param String $idUtilisateur ID de l'utilisateur
     *
     * @return Boolean Vrai si l'utilisateur est de type comptable
     */
    public function estComptable($idUtilisateur)
    {
        $requetePrepare = pdoGSB::$_monPdo->prepare(
            'SELECT visiteur.type '
            .'FROM visiteur '
            .'WHERE visiteur.id =:unUtilisateur'
        );
        $requetePrepare->bindParam(':unUtilisateur', $idUtilisateur, PDO::PARAM_STR);
        $requetePrepare->execute();
        $laLigne = $requetePrepare->fetch();
        return $laLigne['type'] == '0';
    }

    /**
     * Met à jour la table ligneFraisHorsForfait pour un visiteur et
     * un mois donné en enregistrant les nouveaux montants
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     * @param Array  $leFrais    tableau associatif de clé id, libelle, date et
     *                           de valeur de quantité pour ce frais
     *
     * @return null
     */
    public function majFraisHorsForfait($idVisiteur, $mois, $leFrais)
    {
        $idFrais = $leFrais['id'];
        $libelle = $leFrais['libelle'];
        $date = dateFrancaisVersAnglais($leFrais['date']);
        $montant = $leFrais['montant'];

        $requetePrepare = PdoGSB::$_monPdo->prepare(
            'UPDATE lignefraishorsforfait '
            . 'SET lignefraishorsforfait.montant = :unMontant, '
            . 'lignefraishorsforfait.libelle = :unLibelle, '
            . 'lignefraishorsforfait.date = :uneDate '
            . 'WHERE lignefraishorsforfait.idvisiteur = :unIdVisiteur '
            . 'AND lignefraishorsforfait.mois = :unMois '
            . 'AND lignefraishorsforfait.id = :idFrais'
        );

            $requetePrepare->bindParam(':uneDate', $date, PDO::PARAM_STR);
            $requetePrepare->bindParam(':unMontant', $montant, PDO::PARAM_INT);
            $requetePrepare->bindParam(':unLibelle', $libelle, PDO::PARAM_STR);
            $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
            $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
            $requetePrepare->bindParam(':idFrais', $idFrais, PDO::PARAM_STR);
            $requetePrepare->execute();
    }

    /**
     * Ajoute le terme "REFUSE" devant le libellé d'un frais hors forfait
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaamm
     * @param Array  $leFrais    Tableau associatif de clé id, libelle, date
     *                           et de valeur de quantité pour ce frais
     *
     * @return null
     */
    public function refusFraisHorsForfait($idVisiteur, $mois, $leFrais)
    {
        //Vérification qu'un frais n'est pas déjà refusé
        if (!estRefuse($leFrais)) {
            $idFrais = $leFrais['id'];
            $libelleFrais = $leFrais['libelle'];
            $nouveauLibelle = 'REFUSE '.$libelleFrais;

            $requetePrepare = PdoGSB::$_monPdo->prepare(
                'UPDATE lignefraishorsforfait '
                .'SET lignefraishorsforfait.libelle = :unLibelle '
                . 'WHERE lignefraishorsforfait.idvisiteur = :unIdVisiteur '
                . 'AND lignefraishorsforfait.mois = :unMois '
                . 'AND lignefraishorsforfait.id = :idFrais'
            );

            $requetePrepare->bindParam(':unLibelle', $nouveauLibelle, PDO::PARAM_STR);
            $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
            $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
            $requetePrepare->bindParam(':idFrais', $idFrais, PDO::PARAM_STR);
            $requetePrepare->execute();
        }
    }

    /**
     * Calcul la somme du montant validé par le comptable et le met à jour dans la fiche de frais
     * Discrimine le montant refusé et ne l'incorpore pas dans le total
     *
     * @param String $idVisiteur Id du visiteur
     * @param String $mois       Mois sous la forme aaamm
     *
     * @return null
     */
    public function majMontantValide($idVisiteur, $mois)
    {
        $lesTaux = $this->getLesTaux();
        $lesFraisForfait = $this->getLesFraisForfait($idVisiteur, $mois);
        $lesFraisHorsForfait = $this->getLesFraisHorsForfait($idVisiteur, $mois);
        $totalFraisHorsForfait = 0;
        $totalFraisForfait = 0;
        //calcul du montant des frais forfaitisé
        foreach ($lesFraisForfait as $leFrais) {
            $totalFraisForfait += $leFrais['quantite']*$lesTaux[$leFrais['idfrais']];
        }
        //calcul du montant des frais hors forfait en discriminant le frais refusé
        foreach ($lesFraisHorsForfait as $leFraisHF) {
            if (!estRefuse($leFraisHF)) {
                $totalFraisHorsForfait += $leFraisHF['montant'];
            }
        }
        $totalMontant = $totalFraisForfait + $totalFraisHorsForfait;

        $requetePrepare = PdoGSB::$_monPdo->prepare(
            'UPDATE fichefrais '
            .'SET fichefrais.montantvalide =:leTotal '
            .'WHERE fichefrais.idvisiteur =:leVisiteur '
            .'AND fichefrais.mois =:leMois'
        );
        $requetePrepare->bindParam(':leTotal', $totalMontant, PDO::PARAM_STR);
        $requetePrepare->bindParam(':leVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':leMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
    }

    /**
     * Retourne les taux des frais forfaitisés sous la forme d'un tableau Associatif
     *
     * @return Array $lesTaux Tableau associatif de clé et les taux unitaire
     */
    public function getLesTaux()
    {
        $requetePrepare = PdoGSB::$_monPdo->prepare(
            'SELECT fraisforfait.id, fraisforfait.montant '
            .'FROM fraisforfait '
        );
        $requetePrepare->execute();
        $lesLignes = $requetePrepare->fetchAll();
        $lesTaux = array();
        foreach ($lesLignes as $laLigne) {
            $lesTaux += [
                $laLigne['id']=>$laLigne['montant']
            ];
        }
        return $lesTaux;
    }

    /**
     * Retourne les mois disponible en passant l'id du visiteur en paramètre
     * ainsi qu'un ou plusieurs états (jusqu'à trois états possible)
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $etat       l'état d'une fiche de frais
     *
     * @return Array un tableau associatif de clé un mois -aaaamm- et de valeurs
     *         l'année et le mois correspondant
     */
    public function getLesMoisParEtat($idVisiteur, $etat, $etat2 = null, $etat3 = null)
    {
        $laRequete = 'SELECT fichefrais.mois AS mois FROM fichefrais '
                    .'WHERE fichefrais.idvisiteur = :unIdVisiteur '
                    .'AND ( fichefrais.idetat = :unEtat ';
        if (isset($etat2)) {
            $laRequete .= 'OR fichefrais.idetat = :unEtat2 ';
            if (isset($etat3)) {
                $laRequete.= 'OR fichefrais.idetat = :unEtat3 ';
            }
        }
        $laRequete .= ') ORDER BY fichefrais.mois desc';

        $requetePrepare = PdoGSB::$_monPdo->prepare($laRequete);
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unEtat', $etat, PDO::PARAM_STR);
        if (isset($etat2)) {
            $requetePrepare->bindParam(':unEtat2', $etat2, PDO::PARAM_STR);
            if (isset($etat3)) {
                $requetePrepare->bindParam(':unEtat3', $etat3, PDO::PARAM_STR);
            }
        }
        $requetePrepare->execute();
        $lesMois = array();
        while ($laLigne = $requetePrepare->fetch()) {
            $mois = $laLigne['mois'];
            $numAnnee = substr($mois, 0, 4);
            $numMois = substr($mois, 4, 2);
            $lesMois[] = array(
                'mois' => $mois,
                'numAnnee' => $numAnnee,
                'numMois' => $numMois
            );
        }
        return $lesMois;
    }
}
