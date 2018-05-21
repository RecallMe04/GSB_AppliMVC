<?php
/**
 * (Comptable) Vue Liste des mois cloturés
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
<div class="row">
    <div class="col-md-3"></div>
    <div class="col-md-6">
        <div style="text-align: center;"><h3>S&eacute;lectionner un mois cl&ocirc;tur&eacute;</h3></div>
        <form action="index.php?uc=validerFrais&action=afficherFrais&idVisiteur=<?php echo htmlspecialchars($leVisiteur) ?>"
              method="post">
            <div class="form-group">
                <label for="lstMois" accesskey="n"></label>
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
                                    . '/' . htmlspecialchars($numAnnee) ?>
                            </option>
                            <?php
                        } else {
                            ?>
                            <option value="<?php echo htmlspecialchars($mois) ?>">
                                <?php echo htmlspecialchars($numMois)
                                    . '/' . htmlspecialchars($numAnnee) ?>
                            </option>
                            <?php
                        }
                    }
                    ?>
                </select>
            </div>
            <div style="text-align: center;"><input type="submit"
                                    value="Valider" class="btn btn-success"></div>
        </form>
    </div>
</div>