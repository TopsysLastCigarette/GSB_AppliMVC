<?php
/**
 * Création et téléchargement d'un PDF
 *
 * PHP version 7
 *
 * @category PPE
 * @package  GSB
 * @author   Joël Deligne <monsters.freaks@gmail.com>
 * @version  GIT: <0>
 * @link     http://www.reseaucerta.org Contexte « Laboratoire GSB »
 */
use Mpdf\Mpdf;

$nomVisiteur = filter_input(INPUT_POST, 'nomVisiteur', FILTER_SANITIZE_STRING);
$leMois = filter_input(INPUT_POST, 'lstMois', FILTER_SANITIZE_STRING);

$fichePDF = 'generatedPDF/REMBOURSEMENT_FRAIS_'.$leMois.'-'.$nomVisiteur.'.pdf';

//Si et seulement si le fichier n'existe pas, il est génerer (GreenIT)
if (!file_exists($fichePDF)) {
    include_once 'vendor/autoload.php';

    //Récupération des variables utiles
    $idVisiteur = filter_input(INPUT_POST, 'lstVisiteurs', FILTER_SANITIZE_STRING);
    $prenomVisiteur = filter_input(INPUT_POST, 'prenomVisiteur', FILTER_SANITIZE_STRING);

    $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idVisiteur, $leMois);
    $lesFraisForfait = $pdo->getLesFraisForfait($idVisiteur, $leMois);
    $lesInfosFicheFrais = $pdo->getLesInfosFicheFrais($idVisiteur, $leMois);
    $numAnnee = substr($leMois, 0, 4);
    $numMois = substr($leMois, 4, 2);
    $montantValide = $lesInfosFicheFrais['montantValide'];
    $lesTaux = $pdo->getLesTaux($idVisiteur);
    $dateModif = dateAnglaisVersFrancais($lesInfosFicheFrais['dateModif']);

    //Gestion des variables en vue de l'écriture html
    $css = file_get_contents('styles/stylePDF.css');
    $logo = '<div class="image"><img src="images/logo.jpg"></div>';
    $titre = '<h1>Remboursement des frais engagés</h1>';
    $identitéVisiteur = '<p class="idVisiteur">Visiteur Ref: '.$idVisiteur.' - '.$prenomVisiteur.' '.$nomVisiteur.'</p>';
    $moisFiche = moisEnLettres($numMois).' '.$numAnnee;
    //Création du tableau de frais forfaitisé
    $tabFraisForfait = '<table><thead><tr class="title"><th>Frais Forfaitaire</th>'
                                        .'<th>Quantité</th>'
                                        .'<th>Montant unitaire</th>'
                                        .'<th>Total</th>'
                                    .'</tr>'
                             .'</thead>';
    $tabFraisForfait .= '<tbody>';
    foreach ($lesFraisForfait as $unFrais) {
        $quantite = (float)$unFrais['quantite'];
        $taux = (float)$lesTaux[$unFrais['idfrais']];
        $total = $quantite * $taux;
        $tabFraisForfait .= '<tr><th>'.$unFrais['libelle'].'</th>'
                               .'<th>'.$unFrais['quantite'].'</th>'
                               .'<th>'.$lesTaux[$unFrais['idfrais']].'</th>'
                               .'<th>'.(string)$total.'</th>'
                            .'</tr>';
    }
    $tabFraisForfait .= '</tbody></table>';
    //Création du tableau de frais hors forfait
    if (!empty($lesFraisHorsForfait)) {
        $tabFraisHorsForfait = '<caption>Autre frais</caption>';
        $tabFraisHorsForfait .= '<table><thead><tr class="title"><th>Date</th>'
            .'<th>Libellé</th>'
            .'<th>Montant</th>'
            .'</tr>'
            .'</thead>';
        $tabFraisHorsForfait .='<tbody>';
        foreach ($lesFraisHorsForfait as $unFrais) {
            $tabFraisHorsForfait .= '<tr><th>'.$unFrais['date'].'</th>'
                .'<th>'.$unFrais['libelle'].'</th>'
                .'<th>'.$unFrais['montant'].'</th>'
                .'</tr>';
        }
        $tabFraisHorsForfait .= '</tbody></table>';
    }
    //Total
    $totalFiche = '<p class="total">Total '.$moisFiche.' = '.$montantValide.'</p>';
    //Pied, signature
    $pied = '<p>Fait à Paris, le '.laDateEnLettres($dateModif).'<br>Vu l\'agent comptable</p></br>';
    $pied .= '<img src="images/signature.jpg">';
    //Création du fichier PDF
    $mpdf = new Mpdf();
    $mpdf->WriteHTML('<!DOCTYPE html>');
    $mpdf->WriteHTML($css, \Mpdf\HTMLParserMode::HEADER_CSS);
    $mpdf->WriteHTML('<body>');
    $mpdf->WriteHTML($logo);
    $mpdf->WriteHTML($titre, \Mpdf\HTMLParserMode::HTML_BODY);
    $mpdf->WriteHTML($identitéVisiteur.'</br>'.'Mois : '.$moisFiche);
    $mpdf->WriteHTML($tabFraisForfait, \Mpdf\HTMLParserMode::HTML_BODY);
    if (!empty($lesFraisHorsForfait)) {
        $mpdf->WriteHTML($tabFraisHorsForfait, \Mpdf\HTMLParserMode::HTML_BODY);
    }
    $mpdf->WriteHTML($totalFiche, \Mpdf\HTMLParserMode::HTML_BODY);
    $mpdf->WriteHTML('</body><footer>');
    $mpdf->WriteHTML($pied, \Mpdf\HTMLParserMode::HTML_BODY);
    $mpdf->WriteHTML('</footer>');
    //Génération du PDF
    $mpdf->Output('generatedPDF/REMBOURSEMENT_FRAIS_'.$leMois.'-'.$nomVisiteur.'.pdf', \Mpdf\Output\Destination::FILE);
}
header('Content-Description: File Transfer');
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="'.basename($fichePDF).'"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($fichePDF));
readfile($fichePDF);

