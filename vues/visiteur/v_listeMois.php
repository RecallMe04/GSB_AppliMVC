<?php
/**
 * (Visiteur) Vue Liste des mois
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
<h2>Mes fiches de frais</h2>
<div class="row">
    <div class="col-md-4">
        <h3>S&eacute;lectionner un mois : </h3>
    </div>
    <div class="col-md-4">
        <form action="index.php?uc=etatFrais&action=voirEtatFrais"
              method="post">
            <div class="form-group">
                <label for="lstMois" accesskey="n">Mois : </label>
                <select id="lstMois" name="lstMois" class="form-control">
                    <?php
                    foreach ($lesMois as $unMois) {
                        $mois = $unMois['mois'];
                        $numAnnee = $unMois['numAnnee'];
                        $numMois = $unMois['numMois'];
                        if ($mois == $moisASelectionner) {
                            ?>
                            <option selected value=
                            "<?php echo htmlspecialchars($mois) ?>">
                                <?php echo htmlspecialchars($numMois)
                                    . '/' . htmlspecialchars($numAnnee) ?> </option>
                            <?php
                        } else {
                            ?>
                            <option value="<?php echo htmlspecialchars($mois) ?>">
                                <?php echo htmlspecialchars($numMois)
                                    . '/' . htmlspecialchars($numAnnee) ?> </option>
                            <?php
                        }
                    }
                    ?>

                </select>
            </div>
            <input type="submit" value="Valider" class="btn btn-success">
            <input type="reset" value="Effacer" class="btn btn-danger">
        </form>
    </div>
</div>