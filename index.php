<?php

/**
 * (Commun) Index du projet GSB
 *
 * PHP Version 7
 *
 * @category  PPE
 * @package   GSB
 * @author    Réseau CERTA <contact@reseaucerta.org>
 * @author    José GIL <jgil@ac-nice.fr>
 * @author    Teddy MARTOIA <ted.2000@hotmail.fr>
 * @copyright 2017 Réseau CERTA
 * @copyright 2018 Teddy MARTOIA
 * @license   Réseau CERTA
 * @version   GIT: <0>
 * @link      http://www.reseaucerta.org Contexte « Laboratoire GSB »
 */
error_reporting(E_ALL | E_STRICT);
ob_start();
require_once 'includes/fct.inc.php';
require_once 'includes/class.pdogsb.inc.php';
session_start();
$pdo = PdoGsb::getPdoGsb();
$comptableEstConnecte = comptableEstConnecte();
$visiteurEstConnecte = visiteurEstConnecte();
require 'vues/v_entete.php';
$uc = filter_input(INPUT_GET, 'uc', FILTER_SANITIZE_STRING);
if (($uc) && (!$visiteurEstConnecte) && (!$comptableEstConnecte)) {
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
    include 'controleurs/visiteur/c_gererFrais.php';
    break;
case 'etatFrais':
    include 'controleurs/visiteur/c_etatFrais.php';
    break;
case 'deconnexion':
    include 'controleurs/c_deconnexion.php';
    break;
case 'validerFrais':
    include 'controleurs/comptable/c_validerFrais.php';
    break;
case 'suiviFrais':
    include 'controleurs/comptable/c_suiviFrais.php';
    break;
}
require 'vues/v_pied.php';
