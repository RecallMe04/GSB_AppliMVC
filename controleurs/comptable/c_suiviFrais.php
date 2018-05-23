<?php /** @noinspection PhpCSValidationInspection */

/**
 * (Comptable) Gestion du suivi des frais
 *
 * PHP Version 7
 *
 * @category  PPE
 * @package   GSB
 * @author    Teddy MARTOIA <ted.2000@hotmail.fr>
 * @copyright 2018 Teddy MARTOIA
 * @license   Réseau CERTA
 * @version   GIT: <0>
 * @link      http://www.reseaucerta.org Contexte « Laboratoire GSB »
 */
$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);
$lesVisiteur = $pdo->getToutLesVisiteurs();
switch ($action) {
case 'saisieVisiteur' :
    include 'vues/comptable/v_suiviListeVisiteurs.php';
    break;
case 'saisieMois' :
    $leVisiteur = filter_input(
        INPUT_POST, 'lstVisiteur', FILTER_SANITIZE_STRING
    );
    $visiteurASelectionner = $leVisiteur;
    $lesMois = $pdo->getToutLesMoisValidees($leVisiteur);
    if ($lesMois == null) {
        ajouterErreur("Pas de fiche de frais à rembourser pour ce visiteur");
        include 'vues/v_erreurs.php';
        include 'vues/comptable/v_suiviListeVisiteurs.php';
    } else {
        include 'vues/comptable/v_suiviListeVisiteurs.php';
        include 'vues/comptable/v_suiviListeMoisValides.php';
    }
    break;
case 'afficherFrais' :
    $leVisiteur = filter_input(INPUT_GET, 'idVisiteur', FILTER_SANITIZE_STRING);
    if ((filter_input(INPUT_GET, 'mode', FILTER_SANITIZE_STRING))
        == 'etatModifie'
    ) {
        $leMois = filter_input(INPUT_GET, 'idMois', FILTER_SANITIZE_STRING);
    } else {
        $leMois = filter_input(INPUT_POST, 'lstMois', FILTER_SANITIZE_STRING);
        if ($pdo->estMoisCloturee($leVisiteur, $leMois)) {
            header(
                'Location: index.php?uc=validerFrais&action=afficherFrais'
                . '&idVisiteur=' . $leVisiteur
                . '&idMois=' . $leMois
                . '&mode=validerFrais'
            );
        }
    }
    $visiteurASelectionner = $leVisiteur;
    include 'vues/comptable/v_suiviListeVisiteurs.php';
    $lesMois = $pdo->getToutLesMoisValidees($leVisiteur);
    $moisASelectionner = $leMois;
    include 'vues/comptable/v_suiviListeMoisValides.php';
    $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($leVisiteur, $leMois);
    $lesFraisForfait = $pdo->getLesFraisForfait($leVisiteur, $leMois);
    $lesInfosFicheFrais = $pdo->getLesInfosFicheFrais($leVisiteur, $leMois);
    $numAnnee = substr($leMois, 0, 4);
    $numMois = substr($leMois, 4, 2);
    $libEtat = $lesInfosFicheFrais['libEtat'];
    $montantValide = $lesInfosFicheFrais['montantValide'];
    $nbJustificatifs = $lesInfosFicheFrais['nbJustificatifs'];
    $dateModif = dateAnglaisVersFrancais($lesInfosFicheFrais['dateModif']);
    include 'vues/comptable/v_suiviFraisValides.php';
    break;
case 'changerEtatFrais':
    $leVisiteur = filter_input(INPUT_GET, 'idVisiteur', FILTER_SANITIZE_STRING);
    $leMois = filter_input(INPUT_GET, 'idMois', FILTER_SANITIZE_STRING);
    $demande = filter_input(INPUT_GET, 'demande', FILTER_SANITIZE_STRING);
    if ($demande == 'remboursement') {
        $pdo->mettreEnRemboursementFrais($leVisiteur, $leMois);
        header(
            'Location: index.php?uc=suiviFrais&action=afficherFrais'
            . '&idVisiteur=' . $leVisiteur
            . '&idMois=' . $leMois
            . '&mode=etatModifie'
        );
    } elseif ($demande == 'paiement') {
        $pdo->mettreEnPaiementFrais($leVisiteur, $leMois);
        header(
            'Location: index.php?uc=suiviFrais&action=afficherFrais'
            . '&idVisiteur=' . $leVisiteur
            . '&idMois=' . $leMois
            . '&mode=etatModifie'
        );
    }
    break;
}