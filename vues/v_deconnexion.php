<?php
/**
 * (Commun) Vue Déconnexion
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
deconnecter();
?>
    <div class="alert alert-info" role="alert">
        <p>Vous avez bien &eacute;t&eacute; d&eacute;connect&eacute; ! <a href="index.php">Cliquez ici</a>
            pour revenir &agrave; la page de connexion.</p>
    </div>
<?php
header("Refresh: 3;URL=index.php");
