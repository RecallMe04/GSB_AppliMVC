<?php /** @noinspection ALL */
/**
 * (Comptable) Vue des Frais validés
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
<hr>
<div class="panel panel-primary">
    <div class="panel-heading">Fiche de frais du mois
        <?php echo htmlspecialchars($numMois)
            . '-' . htmlspecialchars($numAnnee) ?> :
    </div>
    <div class="panel-body">
        <strong><u>Etat :</u></strong> <?php echo htmlspecialchars($libEtat) ?>
        depuis le <?php echo htmlspecialchars($dateModif) ?> <br>
        <strong><u>Montant valid&eacute; :</u></strong>
        <?php echo htmlspecialchars($montantValide) ?>
    </div>
</div>
<div class="panel panel-info">
    <div class="panel-heading">El&eacute;ments forfaitis&eacute;s</div>
    <table class="table table-bordered table-responsive">
        <tr>
            <?php
            foreach ($lesFraisForfait as $unFraisForfait) {
                $libelle = $unFraisForfait['libelle'];
                ?>
                <th> <?php echo htmlspecialchars($libelle) ?></th>
                <?php
            }
            ?>
        </tr>
        <tr>
            <?php
            foreach ($lesFraisForfait as $unFraisForfait) {
                $quantite = $unFraisForfait['quantite'];
                ?>
                <td class="qteForfait">
                    <?php echo htmlspecialchars($quantite) ?>
                </td>
                <?php
            }
            ?>
        </tr>
    </table>
</div>
<div class="panel panel-info">
    <div class="panel-heading">Descriptif des &eacute;l&eacute;ments hors forfait -
        <?php echo htmlspecialchars($nbJustificatifs) ?> justificatifs re&ccedil;us
    </div>
    <table class="table table-bordered table-responsive">
        <tr>
            <th class="date">Date</th>
            <th class="libelle">Libell&eacute;</th>
            <th class='montant'>Montant</th>
        </tr>
        <?php
        foreach ($lesFraisHorsForfait as $unFraisHorsForfait) {
            $date = $unFraisHorsForfait['date'];
            $libelle = $unFraisHorsForfait['libelle'];
            $montant = $unFraisHorsForfait['montant'];
            ?>
            <tr>
                <td><?php echo htmlspecialchars($date) ?></td>
                <td><?php echo htmlspecialchars($libelle) ?></td>
                <td><?php echo htmlspecialchars($montant) ?></td>
            </tr>
            <?php
        }
        ?>
    </table>
</div>
<div class="row">
    <div class="col-md-8">
        <form action="index.php?uc=suiviFrais&action=changerEtatFrais&idVisiteur=<?php echo htmlspecialchars($leVisiteur) ?>&idMois=<?php echo htmlspecialchars($leMois) ?>&demande=paiement"
              method="post">
            <div style="text-align: center;"><input type="submit"
             class="btn btn-success" value="Mettre en paiement"/>
            </div>
        </form>
    </div>
    <div class="col-md-1">
        <form action="index.php?uc=suiviFrais&action=changerEtatFrais&idVisiteur=<?php echo htmlspecialchars($leVisiteur) ?>&idMois=<?php echo htmlspecialchars($leMois) ?>&demande=remboursement"
              method="post">
            <div style="text-align: center;"><input type="submit"
             class="btn btn-success" value="Pay&eacute;e"/></div>
        </form>
    </div>
</div>

