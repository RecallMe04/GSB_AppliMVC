<?php

/**
 * (Android) Transfert des données JSON en BDD
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
error_reporting(E_ALL | E_STRICT);
require_once 'includes/class.pdogsb.inc.php';
$pdo = PdoGsb::getPdoGsb();
if (isset($_REQUEST["operation"])) {
    if ($_REQUEST["operation"] == "frais") {
        $lesdonnees = $_REQUEST["lesdonnees"];
        $donnees = json_decode($lesdonnees);
        $personne = $donnees[0];
        $idVisiteur = $pdo->getIdVisiteur($personne[0], $personne[1]);
        if (!is_null($idVisiteur)) {
            print("Connexion de l'utilisateur réussi%");
            $mois = $donnees[1];
            $ficheFraisExiste = $pdo->ficheFraisExiste($idVisiteur, $mois);
            if ($ficheFraisExiste == false) {
                $pdo->creeNouvelleFicheFrais($idVisiteur, $mois);
                print("Création d'une nouvelle fiche de frais%");
            }
            $fraisForfaitAJour = $donnees[4];
            if ($fraisForfaitAJour == 1) {
                $lesFrais = $donnees[2];
                $qteKm = $lesFrais[0];
                $qteRepas = $lesFrais[1];
                $qteNuitee = $lesFrais[2];
                $qteEtape = $lesFrais[3];
                if ($pdo->ligneFraisForfaitExiste($idVisiteur, $mois, "KM")) {
                    $pdo->majLigneFraisForfait(
                        $idVisiteur, $mois, $qteKm, "KM"
                    );
                    print("Mise à jour du frais forfait kilomètre%");
                } else {
                    $pdo->creerLigneFraisForfait(
                        $idVisiteur, $mois, $qteKm, "KM"
                    );
                    print("Création du frais forfait kilomètre%");
                }
                if ($pdo->ligneFraisForfaitExiste($idVisiteur, $mois, "REP")) {
                    $pdo->majLigneFraisForfait(
                        $idVisiteur, $mois, $qteRepas, "REP"
                    );
                    print("Mise à jour du frais forfait repas%");
                } else {
                    $pdo->creerLigneFraisForfait(
                        $idVisiteur,
                        $mois,
                        $qteRepas,
                        "REP"
                    );
                    print("Création du frais forfait repas%");
                }
                if ($pdo->ligneFraisForfaitExiste($idVisiteur, $mois, "NUI")) {
                    $pdo->majLigneFraisForfait(
                        $idVisiteur,
                        $mois,
                        $qteNuitee,
                        "NUI"
                    );
                    print("Mise à jour du frais forfait nuitee%");
                } else {
                    $pdo->creerLigneFraisForfait(
                        $idVisiteur,
                        $mois,
                        $qteNuitee,
                        "NUI"
                    );
                    print("Création du frais forfait nuitee%");
                }
                if ($pdo->ligneFraisForfaitExiste($idVisiteur, $mois, "ETP")) {
                    $pdo->majLigneFraisForfait(
                        $idVisiteur, $mois, $qteEtape, "ETP"
                    );
                    print("Mise à jour du frais forfait etape%");
                } else {
                    $pdo->creerLigneFraisForfait(
                        $idVisiteur,
                        $mois,
                        $qteEtape,
                        "ETP"
                    );
                    print("Création du frais forfait etape%");
                }
            }
            $fraisHF = $donnees[3];
            $numAnnee = substr($mois, 0, 4);
            $numMois = substr($mois, 4, 2);
            $pdo->supprimerToutLesFraisHorsForfaitAndroid($idVisiteur, $mois);
            for ($i = 0; $i < count($fraisHF); $i++) {
                $jour = $fraisHF[$i];
                $i++;
                $montant = $fraisHF[$i];
                $i++;
                $motif = $fraisHF[$i];
                $dateAnglaise = $numAnnee . "-" . $numMois . "-" . $jour;
                $pdo->creeNouveauFraisHorsForfaitAndroid(
                    $idVisiteur,
                    $mois,
                    $motif,
                    $dateAnglaise,
                    $montant
                );
                print("Création du frais hors-forfait%");
            }
            print("Transfert terminé%");
        } else {
            print "Erreur de login ou de mot de passe%";
        }
    }
}
