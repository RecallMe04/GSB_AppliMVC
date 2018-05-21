<?php

/**
 * (Commun) Gestion de l'accueil
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
if ($visiteurEstConnecte) {
    include 'vues/visiteur/v_accueil.php';
} elseif ($comptableEstConnecte) {
    include 'vues/comptable/v_accueil.php';
} else {
    include 'vues/v_connexion.php';
}
