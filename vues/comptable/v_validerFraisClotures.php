<?php /** @noinspection ALL */
/**
 * (Comptable) Vue des Frais cloturés
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
        <?php echo htmlspecialchars($numMois) . '-'
            . htmlspecialchars($numAnnee) ?> :
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
    <form action="index.php?uc=validerFrais&action=modifierFrais&idVisiteur=<?php echo htmlspecialchars($leVisiteur) ?>&idMois=<?php echo htmlspecialchars($leMois) ?>"
          method="post">
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
                    $idFrais = $unFraisForfait['idfrais'];
                    $quantite = $unFraisForfait['quantite'];
                    ?>
                    <td><label for="idFrais"></label>
                        <input type="text" id="idFrais"
                        name="lesFrais[<?php echo htmlspecialchars($idFrais) ?>]"
                        size="10" maxlength="5"
                        value="<?php echo htmlspecialchars($quantite) ?>"
                        class="form-control"></td>
                    <?php
                }
                ?>
                    <td>
                        <br/>
                        <input type="submit" value="Modifier">
                    </td>
            </tr>
        </table>
    </form>
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
            $idFraisHF = $unFraisHorsForfait['id'];
            $date = $unFraisHorsForfait['date'];
            $libelle = $unFraisHorsForfait['libelle'];
            $montant = $unFraisHorsForfait['montant'];
            ?>
            <tr>
                <td><?php echo htmlspecialchars($date) ?></td>
                <td><?php echo htmlspecialchars($libelle) ?></td>
                <td><?php echo htmlspecialchars($montant) ?></td>
                <td>
                    <a href="index.php?uc=validerFrais&action=modifierFraisHF&idVisiteur=<?php echo htmlspecialchars($leVisiteur) ?>&idMois=<?php echo htmlspecialchars($leMois) ?>&idFraisHF=<?php echo htmlspecialchars($idFraisHF) ?>&demande=refus"
                       onclick="return confirm('Voulez-vous vraiment refuser ce frais?');">Supprimer</a></td>
                <td>
                    <a href="index.php?uc=validerFrais&action=modifierFraisHF&idVisiteur=<?php echo htmlspecialchars($leVisiteur) ?>&idMois=<?php echo htmlspecialchars($leMois) ?>&idFraisHF=<?php echo htmlspecialchars($idFraisHF) ?>&demande=report"
                       onclick="return confirm('Voulez-vous vraiment reporter ce frais?');">Reporter</a></td>
            </tr>
            <?php
        }
        ?>
    </table>
</div>
<form action="index.php?uc=validerFrais&action=validerFrais&idVisiteur=<?php echo htmlspecialchars($leVisiteur) ?>&idMois=<?php echo htmlspecialchars($leMois) ?>"
      method="post">
    <div style="text-align: center;"><input type="submit"
                      class="btn btn-success" value="Valider la fiche de frais"/>
    </div>
</form>