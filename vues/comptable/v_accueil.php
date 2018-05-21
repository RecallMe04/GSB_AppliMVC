<?php
/** @noinspection PhpCSValidationInspection */
/**
 * (Comptable) Vue Accueil
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
?>
<div id="accueil">
    <h2>
        Contr&ocirc;le des frais
        <small> - Comptable :
            <?php
            echo htmlspecialchars($_SESSION['prenom'])
                . ' ' . htmlspecialchars($_SESSION['nom'])
            ?></small>
    </h2>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <span class="glyphicon glyphicon-bookmark"></span>
                    Navigation
                </h3>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-xs-12 col-md-12">
                        <a href="index.php?uc=validerFrais&action=saisieVisiteur"
                           class="btn btn-success btn-lg" role="button">
                            <span class="glyphicon glyphicon-pencil"></span>
                            <br>Valider les fiches de frais</a>
                        <a href="index.php?uc=suiviFrais&action=saisieVisiteur"
                           class="btn btn-success btn-lg" role="button">
                            <span class="glyphicon glyphicon-pencil"></span>
                            <br>Suivre le paiement</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>