<?php
/**
 * (Visiteur) Vue Liste des frais hors forfait
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
?>
<hr>
<div class="row">
    <div class="panel panel-info">
        <div class="panel-heading">Descriptif des &eacute;l&eacute;ments hors forfait</div>
        <table class="table table-bordered table-responsive">
            <thead>
            <tr>
                <th class="date">Date</th>
                <th class="libelle">Libell&eacute;</th>
                <th class="montant">Montant</th>
                <th class="action">&nbsp;</th>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach ($lesFraisHorsForfait as $unFraisHorsForfait) {
                $libelle = $unFraisHorsForfait['libelle'];
                $date = $unFraisHorsForfait['date'];
                $montant = $unFraisHorsForfait['montant'];
                $id = $unFraisHorsForfait['id'];
                ?>
                <tr>
                    <td> <?php echo htmlspecialchars($date) ?></td>
                    <td> <?php echo htmlspecialchars($libelle) ?></td>
                    <td><?php echo htmlspecialchars($montant) ?></td>
                    <td><a href="index.php?uc=gererFrais&action=supprimerFrais&idFrais=<?php echo htmlspecialchars($id) ?>"
                           onclick="return confirm('Voulez-vous vraiment supprimer ce frais?');">Supprimer ce frais</a>
                    </td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
    </div>
</div>
<div class="row">
    <h3>Nouvel &eacute;l&eacute;ment hors forfait</h3>
    <div class="col-md-4">
        <form action="index.php?uc=gererFrais&action=validerCreationFrais"
              method="post">
            <div class="form-group">
                <label for="txtDateHF">Date (jj/mm/aaaa): </label>
                <input type="text" id="txtDateHF" name="dateFrais"
                       class="form-control">
            </div>
            <div class="form-group">
                <label for="txtLibelleHF">Libell&eacute;</label>
                <input type="text" id="txtLibelleHF" name="libelle"
                       class="form-control">
            </div>
            <div class="form-group">
                <label for="txtMontantHF">Montant : </label>
                <div class="input-group">
                    <span class="input-group-addon">&euro;</span>
                    <input type="text" id="txtMontantHF" name="montant"
                           class="form-control" value="">
                </div>
            </div>
            <button class="btn btn-success" type="submit">Ajouter</button>
            <button class="btn btn-danger" type="reset">Effacer</button>
        </form>
    </div>
</div>