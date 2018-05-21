<?php
/**
 * (Visiteur) Vue Liste des frais au forfait
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
<div class="row">
    <h2>Renseigner ma fiche de frais du mois
        <?php echo htmlspecialchars($numMois) . '-' . htmlspecialchars($numAnnee) ?>
    </h2>
    <h3>El&eacute;ments forfaitis&eacute;s</h3>
    <div class="col-md-4">
        <form method="post"
              action="index.php?uc=gererFrais&action=validerMajFraisForfait">
            <fieldset>
                <?php
                foreach ($lesFraisForfait as $unFrais) {
                    $idFrais = $unFrais['idfrais'];
                    $libelle = $unFrais['libelle'];
                    $quantite = $unFrais['quantite'];
                    ?>
                    <div class="form-group">
                        <label>
                            <?php echo htmlspecialchars($libelle) ?></label>
                        <input type="text"
                               name="lesFrais[<?php echo htmlspecialchars($idFrais) ?>]"
                               size="10" maxlength="5"
                               value="<?php echo htmlspecialchars($quantite) ?>"
                               class="form-control">
                    </div>
                    <?php
                }
                ?>
                <button class="btn btn-success" type="submit">Ajouter</button>
                <button class="btn btn-danger" type="reset">Effacer</button>
            </fieldset>
        </form>
    </div>
</div>
