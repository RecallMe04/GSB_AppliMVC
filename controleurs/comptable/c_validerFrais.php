<?php

/**
 * (Comptable) Gestion de la validation des frais
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
    include 'vues/comptable/v_validerListeVisiteurs.php';
    break;
case 'saisieMois' :
    $leVisiteur = filter_input(
        INPUT_POST, 'lstVisiteur', FILTER_SANITIZE_STRING
    );
    $visiteurASelectionner = $leVisiteur;
    $lesMois = $pdo->getToutLesMoisClotures($leVisiteur);
    if ($lesMois == null) {
        ajouterErreur("Pas de fiche de frais à valider pour ce visiteur");
        include 'vues/v_erreurs.php';
        include 'vues/comptable/v_validerListeVisiteurs.php';
    } else {
        include 'vues/comptable/v_validerListeVisiteurs.php';
        include 'vues/comptable/v_validerListeMoisClotures.php';
    }
    break;
case 'afficherFrais':
    $leVisiteur = filter_input(INPUT_GET, 'idVisiteur', FILTER_SANITIZE_STRING);
    $mode = filter_input(INPUT_GET, 'mode', FILTER_SANITIZE_STRING);
    if ($mode == 'modifierFrais') {
        $leMois = filter_input(INPUT_GET, 'idMois', FILTER_SANITIZE_STRING);
        $nomVisiteur = $pdo->getNomPrenomVisiteur($leVisiteur);
        $numAnnee = substr($leMois, 0, 4);
        $numMois = substr($leMois, 4, 2);
        echo '<script>alert("La modification des frais pour ' . $nomVisiteur
            . ' a été prise en pour le ' . $numMois
            . '/' . $numAnnee . '");</script>';
    } elseif ($mode == 'modifierFraisHF') {
        $leMois = filter_input(INPUT_GET, 'idMois', FILTER_SANITIZE_STRING);
    } elseif ($mode == 'validerFrais') {
        $leMois = filter_input(INPUT_GET, 'idMois', FILTER_SANITIZE_STRING);
        ajouterErreur("Veuillez d'abord valider ce mois pour ce visiteur");
        include 'vues/v_erreurs.php';
    } else {
        $leMois = filter_input(INPUT_POST, 'lstMois', FILTER_SANITIZE_STRING);
    }
    $lesMois = $pdo->getToutLesMoisClotures($leVisiteur);
    $visiteurASelectionner = $leVisiteur;
    $moisASelectionner = $leMois;
    include 'vues/comptable/v_validerListeVisiteurs.php';
    include 'vues/comptable/v_validerListeMoisClotures.php';
    $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($leVisiteur, $leMois);
    $lesFraisForfait = $pdo->getLesFraisForfait($leVisiteur, $leMois);
    $lesInfosFicheFrais = $pdo->getLesInfosFicheFrais($leVisiteur, $leMois);
    $numAnnee = substr($leMois, 0, 4);
    $numMois = substr($leMois, 4, 2);
    $libEtat = $lesInfosFicheFrais['libEtat'];
    $montantValide = $lesInfosFicheFrais['montantValide'];
    $nbJustificatifs = $lesInfosFicheFrais['nbJustificatifs'];
    $dateModif = dateAnglaisVersFrancais($lesInfosFicheFrais['dateModif']);
    include 'vues/comptable/v_validerFraisClotures.php';
    break;
case 'modifierFrais':
    $leVisiteur = filter_input(INPUT_GET, 'idVisiteur', FILTER_SANITIZE_STRING);
    $leMois = filter_input(INPUT_GET, 'idMois', FILTER_SANITIZE_STRING);
    $lesFrais = filter_input(
        INPUT_POST,
        'lesFrais',
        FILTER_DEFAULT,
        FILTER_FORCE_ARRAY
    );
    if (lesQteFraisValides($lesFrais)) {
        $pdo->majFraisForfait($leVisiteur, $leMois, $lesFrais);
        header(
            'Location: index.php?uc=validerFrais&action=afficherFrais'
            . '&idVisiteur=' . $leVisiteur
            . '&idMois=' . $leMois
            . '&mode=modifierFrais'
        );
    } else {
        ajouterErreur('Les valeurs des frais doivent être numériques');
        include 'vues/v_erreurs.php';
    }
    break;
case 'modifierFraisHF':
    $leVisiteur = filter_input(INPUT_GET, 'idVisiteur', FILTER_SANITIZE_STRING);
    $leMois = filter_input(INPUT_GET, 'idMois', FILTER_SANITIZE_STRING);
    $leFraisHF = filter_input(INPUT_GET, 'idFraisHF', FILTER_SANITIZE_STRING);
    $demande = filter_input(INPUT_GET, 'demande', FILTER_SANITIZE_STRING);
    if ($demande == "refus") {
        $pdo->refusFraisHorsForfait($leFraisHF);
        header(
            'Location: index.php?uc=validerFrais&action=afficherFrais'
            . '&idVisiteur=' . $leVisiteur
            . '&idMois=' . $leMois
            . '&mode=modifierFraisHF'
        );
    } else {
        if ($demande == "report") {
            $pdo->reportFraisHorsForfait($leFraisHF);
            header(
                'Location: index.php?uc=validerFrais&action=afficherFrais'
                . '&idVisiteur=' . $leVisiteur
                . '&idMois=' . $leMois
                . '&mode=modifierFraisHF'
            );
        }
    }
    break;
case 'validerFrais':
    $leVisiteur = filter_input(INPUT_GET, 'idVisiteur', FILTER_SANITIZE_STRING);
    $leMois = filter_input(INPUT_GET, 'idMois', FILTER_SANITIZE_STRING);
    $pdo->mettreEnValidationFrais($leVisiteur, $leMois);
    header('Location: index.php?uc=validerFrais&action=saisieVisiteur');
    break;
}