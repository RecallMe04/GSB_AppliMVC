<?php
/**
 * (Comptable) Vue Liste des visiteurs
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
        <div style="text-align: center;"><h3>S&eacute;lectionner un visiteur</h3></div>
        <form action="index.php?uc=suiviFrais&action=saisieMois"
              method="post">
            <div class="form-group">
                <label for="lstVisiteur" accesskey="n"></label>
                <select id="lstVisiteur" name="lstVisiteur" class="form-control">
                    <?php
                    foreach ($lesVisiteur as $unVisiteur) {
                        $visiteur = $unVisiteur['id'];
                        $nom = $unVisiteur['nom'];
                        $prenom = $unVisiteur['prenom'];
                        if ($visiteur == $visiteurASelectionner) {
                            ?>
                            <option selected value=
                            "<?php echo htmlspecialchars($visiteur) ?>">
                                <?php echo htmlspecialchars($nom)
                                    . ' ' . htmlspecialchars($prenom) ?>
                            </option>
                            <?php
                        } else {
                            ?>
                            <option value=
                                    "<?php echo htmlspecialchars($visiteur) ?>">
                                <?php echo htmlspecialchars($nom)
                                    . ' ' . htmlspecialchars($prenom) ?>
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