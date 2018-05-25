<?php /** @noinspection PhpCSValidationInspection */
/** @noinspection PhpCSValidationInspection */

/**
 * Classe d'accès aux données.
 *
 * PHP Version 7
 *
 * @category  PPE
 * @package   GSB
 * @author    Cheri Bibi - Réseau CERTA <contact@reseaucerta.org>
 * @author    José GIL - CNED <jgil@ac-nice.fr>
 * @author    Teddy MARTOIA <ted.2000@hotmail.fr>
 * @copyright 2017 Réseau CERTA
 * @copyright 2018 Teddy MARTOIA
 * @license   Réseau CERTA
 * @version   GIT: <0>
 * @link      http://www.php.net/manual/fr/book.pdo.php PHP Data Objects sur php.net
 */

/**
 * Classe d'accès aux données.
 *
 * Utilise les services de la classe PDO
 * pour l'application GSB
 * Les attributs sont tous statiques,
 * les 4 premiers pour la connexion
 * $monPdo de type PDO
 * $monPdoGsb qui contiendra l'unique instance de la classe
 *
 * PHP Version 7
 *
 * @category  PPE
 * @package   GSB
 * @author    Cheri Bibi - Réseau CERTA <contact@reseaucerta.org>
 * @author    José GIL <jgil@ac-nice.fr>
 * @author    Teddy MARTOIA <ted.2000@hotmail.fr>
 * @copyright 2017 Réseau CERTA
 * @copyright 2018 Teddy MARTOIA
 * @license   Réseau CERTA
 * @version   Release: 1.0
 * @link      http://www.php.net/manual/fr/book.pdo.php PHP Data Objects sur php.net
 */
class PdoGsb
{

    private static $_serveur = 'mysql:host=localhost';
    private static $_bdd = 'dbname=gsb_frais';
    private static $_user = 'userGsb';
    private static $_mdp = '!@Secret@!123';
    private static $_monPdo;
    private static $_monPdoGsb = null;

    //private static $_serveur = 'mysql:host=db737425363.db.1and1.com';
    //private static $_bdd = 'dbname=db737425363';
    //private static $_user = 'dbo737425363';
    //private static $_mdp = '!@Secret@!123';
    //private static $_monPdo;
    //private static $_monPdoGsb = null;

    /**
     * Constructeur privé, crée l'instance de PDO qui sera sollicitée
     * pour toutes les méthodes de la classe
     */
    private function __construct()
    {
        PdoGsb::$_monPdo = new PDO(
            PdoGsb::$_serveur . ';' . PdoGsb::$_bdd, PdoGsb::$_user,
            PdoGsb::$_mdp
        );
        PdoGsb::$_monPdo->query('SET CHARACTER SET utf8');
    }

    /**
     * Méthode destructeur appelée dès qu'il n'y a plus de référence sur un
     * objet donné, ou dans n'importe quel ordre pendant la séquence d'arrêt.
     */
    public function __destruct()
    {
        PdoGsb::$_monPdo = null;
    }

    /**
     * Fonction statique qui crée l'unique instance de la classe
     * Appel : $instancePdoGsb = PdoGsb::getPdoGsb();
     *
     * @return PdoGsb l'unique objet de la classe PdoGsb
     */
    public static function getPdoGsb()
    {
        if (PdoGsb::$_monPdoGsb == null) {
            return PdoGsb::$_monPdoGsb = new PdoGsb();
        }
        return PdoGsb::$_monPdoGsb;
    }

    /**
     * Retourne sous forme d'un tableau associatif toutes les lignes de frais
     * au forfait concernées par les deux arguments
     *
     * @param string $idVisiteur ID du visiteur
     * @param string $mois       Mois sous la forme aaaamm
     *
     * @return array l'id, le libelle et la quantité sous la forme d'un tableau
     * associatif
     */
    public function getLesFraisForfait($idVisiteur, $mois)
    {
        $requetePrepare = PdoGSB::$_monPdo->prepare(
            'SELECT fraisforfait.id as idfrais, '
            . 'fraisforfait.libelle as libelle, '
            . 'lignefraisforfait.quantite as quantite '
            . 'FROM lignefraisforfait '
            . 'INNER JOIN fraisforfait '
            . 'ON fraisforfait.id = lignefraisforfait.idfraisforfait '
            . 'WHERE lignefraisforfait.idvisiteur = :unIdVisiteur '
            . 'AND lignefraisforfait.mois = :unMois '
            . 'ORDER BY lignefraisforfait.idfraisforfait'
        );
        $requetePrepare->bindParam(
            ':unIdVisiteur', $idVisiteur, PDO::PARAM_STR
        );
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        return $requetePrepare->fetchAll();
    }

    /**
     * Retourne sous forme d'un tableau associatif toutes les lignes de frais
     * hors forfait concernées par les deux arguments.
     *
     * La boucle foreach ne peut être utilisée ici car on procède
     * à une modification de la structure itérée - transformation du champ date-
     *
     * @param string $idVisiteur ID du visiteur
     * @param string $mois       Mois sous la forme aaaamm
     *
     * @return array tous les champs des lignes de frais hors forfait sous la forme
     * d'un tableau associatif
     */
    public function getLesFraisHorsForfait($idVisiteur, $mois)
    {
        $requetePrepare = PdoGsb::$_monPdo->prepare(
            'SELECT * FROM lignefraishorsforfait '
            . 'WHERE lignefraishorsforfait.idvisiteur = :unIdVisiteur '
            . 'AND lignefraishorsforfait.mois = :unMois'
        );
        $requetePrepare->bindParam(
            ':unIdVisiteur', $idVisiteur, PDO::PARAM_STR
        );
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $lesLignes = $requetePrepare->fetchAll();
        for ($i = 0; $i < count($lesLignes); $i++) {
            $date = $lesLignes[$i]['date'];
            $lesLignes[$i]['date'] = dateAnglaisVersFrancais($date);
        }
        return $lesLignes;
    }

    /**
     * Retourne tous les id de la table FraisForfait
     *
     * @return array un tableau d'id
     */
    public function getLesIdFrais()
    {
        $requetePrepare = PdoGsb::$_monPdo->prepare(
            'SELECT fraisforfait.id as idfrais '
            . 'FROM fraisforfait ORDER BY fraisforfait.id'
        );
        $requetePrepare->execute();
        return $requetePrepare->fetchAll();
    }

    /**
     * Retourne les informations d'une fiche de frais d'un visiteur pour un
     * mois donné
     *
     * @param string $idVisiteur ID du visiteur
     * @param string $mois       Mois sous la forme aaaamm
     *
     * @return array un tableau avec des champs de jointure entre une fiche de frais
     *         et la ligne d'état
     */
    public function getLesInfosFicheFrais($idVisiteur, $mois)
    {
        $requetePrepare = PdoGSB::$_monPdo->prepare(
            'SELECT fichefrais.idetat as idEtat, '
            . 'fichefrais.datemodif as dateModif,'
            . 'fichefrais.nbjustificatifs as nbJustificatifs, '
            . 'fichefrais.montantvalide as montantValide, '
            . 'etat.libelle as libEtat '
            . 'FROM fichefrais '
            . 'INNER JOIN etat ON fichefrais.idetat = etat.id '
            . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
            . 'AND fichefrais.mois = :unMois'
        );
        $requetePrepare->bindParam(
            ':unIdVisiteur', $idVisiteur, PDO::PARAM_STR
        );
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $laLigne = $requetePrepare->fetch();
        return $laLigne;
    }

    /**
     * Retourne le nombre de justificatif d'un visiteur pour un mois donné
     *
     * @param string $idVisiteur ID du visiteur
     * @param string $mois       Mois sous la forme aaaamm
     *
     * @return integer le nombre total de justificatifs
     */
    public function getNbjustificatifs($idVisiteur, $mois)
    {
        $requetePrepare = PdoGsb::$_monPdo->prepare(
            'SELECT fichefrais.nbjustificatifs as nb FROM fichefrais '
            . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
            . 'AND fichefrais.mois = :unMois'
        );
        $requetePrepare->bindParam(
            ':unIdVisiteur', $idVisiteur, PDO::PARAM_STR
        );
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $laLigne = $requetePrepare->fetch();
        return $laLigne['nb'];
    }

    /**
     * Modifie l'état et met la date de modification
     * à la date courante d'une fiche de frais.
     *
     * @param string $idVisiteur ID du visiteur
     * @param string $mois       Mois sous la forme aaaamm
     * @param string $etat       Nouvel état de la fiche de frais
     *
     * @return null
     */
    public function majEtatFicheFrais($idVisiteur, $mois, $etat)
    {
        $requetePrepare = PdoGSB::$_monPdo->prepare(
            'UPDATE ficheFrais '
            . 'SET idetat = :unEtat, datemodif = now() '
            . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
            . 'AND fichefrais.mois = :unMois'
        );
        $requetePrepare->bindParam(':unEtat', $etat, PDO::PARAM_STR);
        $requetePrepare->bindParam(
            ':unIdVisiteur', $idVisiteur, PDO::PARAM_STR
        );
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
    }

    /**
     * Met à jour la table ligneFraisForfait pour un visiteur et
     * un mois donné en enregistrant les nouveaux montants
     *
     * @param string $idVisiteur ID du visiteur
     * @param string $mois       Mois sous la forme aaaamm
     * @param array  $lesFrais   tableau associatif de clé idFrais et
     *                           de valeur la quantité pour ce frais
     *
     * @return null
     */
    public function majFraisForfait($idVisiteur, $mois, $lesFrais)
    {
        $lesCles = array_keys($lesFrais);
        foreach ($lesCles as $unIdFrais) {
            $qte = $lesFrais[$unIdFrais];
            $requetePrepare = PdoGSB::$_monPdo->prepare(
                'UPDATE lignefraisforfait '
                . 'SET lignefraisforfait.quantite = :uneQte '
                . 'WHERE lignefraisforfait.idvisiteur = :unIdVisiteur '
                . 'AND lignefraisforfait.mois = :unMois '
                . 'AND lignefraisforfait.idfraisforfait = :unIdFrais'
            );
            $requetePrepare->bindParam(':uneQte', $qte, PDO::PARAM_INT);
            $requetePrepare->bindParam(
                ':unIdVisiteur', $idVisiteur, PDO::PARAM_STR
            );
            $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
            $requetePrepare->bindParam(
                ':unIdFrais', $unIdFrais, PDO::PARAM_STR
            );
            $requetePrepare->execute();
        }
    }

    /**
     * Met à jour le nombre de justificatifs de la table ficheFrais
     * pour le mois et le visiteur concerné
     *
     * @param string  $idVisiteur      ID du visiteur
     * @param string  $mois            Mois sous la forme aaaamm
     * @param integer $nbJustificatifs Nombre de justificatifs
     *
     * @return null
     */
    public function majNbJustificatifs($idVisiteur, $mois, $nbJustificatifs)
    {
        $requetePrepare = PdoGB::$monPdo->prepare(
            'UPDATE fichefrais '
            . 'SET nbjustificatifs = :unNbJustificatifs '
            . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
            . 'AND fichefrais.mois = :unMois'
        );
        $requetePrepare->bindParam(
            ':unNbJustificatifs', $nbJustificatifs, PDO::PARAM_INT
        );
        $requetePrepare->bindParam(
            ':unIdVisiteur', $idVisiteur, PDO::PARAM_STR
        );
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
    }

    /**
     * Crée un nouveau frais hors forfait depuis le pc pour un visiteur un mois donné
     * à partir des informations fournies en paramètre
     *
     * @param string $idVisiteur ID du visiteur
     * @param string $mois       Mois sous la forme aaaamm
     * @param string $libelle    Libellé du frais
     * @param string $date       Date du frais au format français jj//mm/aaaa
     * @param float  $montant    Montant du frais
     *
     * @return null
     */
    public function creeNouveauFraisHorsForfait(
        $idVisiteur,
        $mois,
        $libelle,
        $date,
        $montant
    )
    {
        $dateFr = dateFrancaisVersAnglais($date);
        $requetePrepare = PdoGSB::$_monPdo->prepare(
            'INSERT INTO lignefraishorsforfait '
            . 'VALUES (null, :unIdVisiteur,:unMois, :unLibelle, :uneDateFr, '
            . ':unMontant, 0, 0)'
        );
        $requetePrepare->bindParam(
            ':unIdVisiteur', $idVisiteur, PDO::PARAM_STR
        );
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unLibelle', $libelle, PDO::PARAM_STR);
        $requetePrepare->bindParam(':uneDateFr', $dateFr, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMontant', $montant, PDO::PARAM_INT);
        $requetePrepare->execute();
    }

    /**
     * Crée un nouveau frais hors forfait depuis
     * le téléphone Android pour un visiteur un mois donné
     * à partir des informations fournies en paramètre
     *
     * @param string $idVisiteur ID du visiteur
     * @param string $mois       Mois sous la forme aaaamm
     * @param string $libelle    Libellé du frais
     * @param string $date       Date du frais au format anglaise aaaa-mm-jj
     * @param float  $montant    Montant du frais
     *
     * @return null
     */
    public function creeNouveauFraisHorsForfaitAndroid(
        $idVisiteur,
        $mois,
        $libelle,
        $date,
        $montant
    )
    {
        $requetePrepare = PdoGSB::$_monPdo->prepare(
            'INSERT INTO lignefraishorsforfait '
            . 'VALUES (null, :unIdVisiteur,:unMois, :unLibelle, :uneDate, '
            . ':unMontant, 0, 1)'
        );
        $requetePrepare->bindParam(
            ':unIdVisiteur', $idVisiteur, PDO::PARAM_STR
        );
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unLibelle', $libelle, PDO::PARAM_STR);
        $requetePrepare->bindParam(':uneDate', $date, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMontant', $montant, PDO::PARAM_STR);
        $requetePrepare->execute();
    }

    /**
     * Crée un nouveau frais hors forfait pour un visiteur un mois donné
     * à partir des informations fournies en paramètre
     *
     * @param string $idFraisHF ID du frais hors-forfait
     * @param float  $montant   Montant du frais
     *
     * @return null
     */
    public function modifieFraisHorsForfait($idFraisHF, $montant)
    {
        $requetePrepare = PdoGSB::$_monPdo->prepare(
            'UPDATE lignefraishorsforfait '
            . 'SET lignefraishorsforfait.montant = :unMontant '
            . 'WHERE lignefraishorsforfait.id = :unIdFraisHorsForfait'
        );
        $requetePrepare->bindParam(':unMontant', $montant, PDO::PARAM_STR);
        $requetePrepare->bindParam(
            ':unIdFraisHorsForfait',
            $idFraisHF,
            PDO::PARAM_STR
        );
    }

    /**
     * Supprime l'ensemble des frais hors-forfait
     * envoyé depuis le portable pour le visiteur, le mois
     * et une date donnée.
     *
     * @param string $idVisiteur ID du visiteur
     * @param string $mois       ID du mois
     *
     * @return null
     */
    public function supprimerToutLesFraisHorsForfaitAndroid($idVisiteur, $mois)
    {
        $requetePrepare = PdoGSB::$_monPdo->prepare(
            'DELETE FROM lignefraishorsforfait '
            . 'WHERE lignefraishorsforfait.idvisiteur = :unIdVisiteur '
            . 'AND lignefraishorsforfait.mois = :unMois '
            . 'AND lignefraishorsforfait.portable = 1'
        );
        $requetePrepare->bindParam(
            ':unIdVisiteur', $idVisiteur, PDO::PARAM_STR
        );
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
    }

    /**
     * Crée une nouvelle fiche de frais et les lignes de frais au forfait
     * pour un visiteur et un mois donnés
     *
     * Récupère le dernier mois en cours de traitement, met à 'CL' son champs
     * idEtat, crée une nouvelle fiche de frais avec un idEtat à 'CR' et crée
     * les lignes de frais forfait de quantités nulles
     *
     * @param string $idVisiteur ID du visiteur
     * @param string $mois       Mois sous la forme aaaamm
     *
     * @return null
     */
    public function creeNouvellesLignesFrais($idVisiteur, $mois)
    {
        $dernierMois = $this->dernierMoisSaisi($idVisiteur);
        $laDerniereFiche = $this->getLesInfosFicheFrais(
            $idVisiteur, $dernierMois
        );
        if ($laDerniereFiche['idEtat'] == 'CR') {
            $this->majEtatFicheFrais($idVisiteur, $dernierMois, 'CL');
        }
        $requetePrepare = PdoGsb::$_monPdo->prepare(
            'INSERT INTO fichefrais (idvisiteur,mois,nbjustificatifs,'
            . 'montantvalide,datemodif,idetat) '
            . "VALUES (:unIdVisiteur,:unMois,0,0,now(),'CR')"
        );
        $requetePrepare->bindParam(
            ':unIdVisiteur', $idVisiteur, PDO::PARAM_STR
        );
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $lesIdFrais = $this->getLesIdFrais();
        foreach ($lesIdFrais as $unIdFrais) {
            $requetePrepare = PdoGsb::$_monPdo->prepare(
                'INSERT INTO lignefraisforfait (idvisiteur,mois,'
                . 'idfraisforfait,quantite) '
                . 'VALUES(:unIdVisiteur, :unMois, :idFrais, 0)'
            );
            $requetePrepare->bindParam(
                ':unIdVisiteur', $idVisiteur, PDO::PARAM_STR
            );
            $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
            $requetePrepare->bindParam(
                ':idFrais', $unIdFrais['idfrais'], PDO::PARAM_STR
            );
            $requetePrepare->execute();
        }
    }

    /**
     * Retourne le dernier mois en cours d'un visiteur
     *
     * @param string $idVisiteur ID du visiteur
     *
     * @return string le mois sous la forme aaaamm
     */
    public function dernierMoisSaisi($idVisiteur)
    {
        $requetePrepare = PdoGsb::$_monPdo->prepare(
            'SELECT MAX(mois) as dernierMois '
            . 'FROM fichefrais '
            . 'WHERE fichefrais.idvisiteur = :unIdVisiteur'
        );
        $requetePrepare->bindParam(
            ':unIdVisiteur', $idVisiteur, PDO::PARAM_STR
        );
        $requetePrepare->execute();
        $laLigne = $requetePrepare->fetch();
        $dernierMois = $laLigne['dernierMois'];
        return $dernierMois;
    }

    /**
     * Teste si un visiteur possède une fiche de frais pour le mois passé en argument
     *
     * @param string $idVisiteur ID du visiteur
     * @param string $mois       Mois sous la forme aaaamm
     *
     * @return boolean vrai ou faux
     */
    public function estPremierFraisMois($idVisiteur, $mois)
    {
        $boolReturn = false;
        $requetePrepare = PdoGsb::$_monPdo->prepare(
            'SELECT fichefrais.mois FROM fichefrais '
            . 'WHERE fichefrais.mois = :unMois '
            . 'AND fichefrais.idvisiteur = :unIdVisiteur'
        );
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->bindParam(
            ':unIdVisiteur', $idVisiteur, PDO::PARAM_STR
        );
        $requetePrepare->execute();
        if (!$requetePrepare->fetch()) {
            $boolReturn = true;
        }
        return $boolReturn;
    }

    /**
     * Retourne les informations d'un visiteur
     *
     * @param string $login Login du visiteur
     * @param string $mdp   Mot de passe du visiteur
     *
     * @return array l'id, le nom et le prénom sous la forme d'un tableau associatif
     */
    public function getInfosVisiteur($login, $mdp)
    {
        $requetePrepare = PdoGsb::$_monPdo->prepare(
            'SELECT visiteur.id AS id, visiteur.nom AS nom, '
            . 'visiteur.prenom AS prenom '
            . 'FROM visiteur '
            . 'WHERE visiteur.login = :unLogin AND visiteur.mdp = :unMdp'
        );
        $requetePrepare->bindParam(':unLogin', $login, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMdp', $mdp, PDO::PARAM_STR);
        $requetePrepare->execute();
        return $requetePrepare->fetch();
    }

    /**
     * Retourne les mois pour lesquel un visiteur a une fiche de frais
     *
     * @param string $idVisiteur ID du visiteur
     *
     * @return array tableau associatif de clé un mois -aaaamm- et de valeurs
     *         l'année et le mois correspondant
     */
    public function getLesMoisDisponibles($idVisiteur)
    {
        $requetePrepare = PdoGSB::$_monPdo->prepare(
            'SELECT fichefrais.mois AS mois FROM fichefrais '
            . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
            . 'ORDER BY fichefrais.mois desc'
        );
        $requetePrepare->bindParam(
            ':unIdVisiteur', $idVisiteur, PDO::PARAM_STR
        );
        $requetePrepare->execute();
        $lesMois = array();
        while ($laLigne = $requetePrepare->fetch()) {
            $mois = $laLigne['mois'];
            $numAnnee = substr($mois, 0, 4);
            $numMois = substr($mois, 4, 2);
            $lesMois[] = array(
                'mois' => $mois,
                'numAnnee' => $numAnnee,
                'numMois' => $numMois
            );
        }
        return $lesMois;
    }

    /**
     * Supprime le frais hors forfait dont l'id est passé en argument
     *
     * @param string $idFrais ID du frais
     *
     * @return null
     */
    public function supprimerFraisHorsForfait($idFrais)
    {
        $requetePrepare = PdoGSB::$_monPdo->prepare(
            'DELETE FROM lignefraishorsforfait '
            . 'WHERE lignefraishorsforfait.id = :unIdFrais'
        );
        $requetePrepare->bindParam(':unIdFrais', $idFrais, PDO::PARAM_STR);
        $requetePrepare->execute();
    }

    /**
     * Créer une nouvelle fiche de frais pour le visiteur et le mois donné.
     *
     * @param string $idVisiteur ID du visiteur
     * @param string $mois       ID du mois
     *
     * @return null
     */
    public function creeNouvelleFicheFrais($idVisiteur, $mois)
    {
        $requetePrepare = PdoGSB::$_monPdo->prepare(
            'INSERT INTO fichefrais (idvisiteur, mois, nbjustificatifs, '
            . 'montantvalide, datemodif, idetat) '
            . "VALUES (:unIdVisiteur,:unIdMois,0,0,now(),'CR')"
        );
        $requetePrepare->bindParam(
            ':unIdVisiteur', $idVisiteur, PDO::PARAM_STR
        );
        $requetePrepare->bindParam(':unIdMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
    }

    /**
     * Permet de savoir si le mois passé en paramètre correspond
     * à une fiche à l'état cloturée.
     *
     * @param string $idVisiteur ID du visiteur
     * @param string $idMois     ID du mois
     *
     * @return boolean vrai si c'est un mois cloturé sinon retourne faux
     */
    public function estMoisCloturee($idVisiteur, $idMois)
    {
        $requetePrepare = PdoGSB::$_monPdo->prepare(
            'SELECT * '
            . 'FROM fichefrais '
            . "WHERE fichefrais.idetat = 'CL' "
            . 'AND fichefrais.idvisiteur = :unIdVisiteur '
            . 'AND fichefrais.mois = :unIdMois'
        );
        $requetePrepare->bindParam(
            ':unIdVisiteur', $idVisiteur, PDO::PARAM_STR
        );
        $requetePrepare->bindParam(':unIdMois', $idMois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $laLigne = $requetePrepare->fetch();
        if ($laLigne == null) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Test si une fiche de frais existe si elle existe
     * on renvoi true sinon false
     *
     * @param string $idVisiteur ID du visiteur
     * @param string $mois       ID du mois
     *
     * @return boolean vrai si la fiche existe sinon retourne faux
     */
    public function ficheFraisExiste($idVisiteur, $mois)
    {
        $requetePrepare = PdoGSB::$_monPdo->prepare(
            'SELECT * '
            . 'FROM fichefrais '
            . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
            . 'AND fichefrais.mois = :unIdMois'
        );
        $requetePrepare->bindParam(
            ':unIdVisiteur', $idVisiteur, PDO::PARAM_STR
        );
        $requetePrepare->bindParam(':unIdMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $lesLignes = $requetePrepare->fetch();
        if ($lesLignes == null) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Retourne les informations d'un comptable
     *
     * @param string $login Login du comptable
     * @param string $mdp   Mot de passe du comptable
     *
     * @return array l'id du comptable, son nom et son prénom
     *         sous la forme d'un tableau associatif
     */
    public function getInfosComptable($login, $mdp)
    {
        $requetePrepare = PdoGsb::$_monPdo->prepare(
            'SELECT comptable.id AS id, comptable.nom AS nom, '
            . 'comptable.prenom AS prenom '
            . 'FROM comptable '
            . 'WHERE comptable.login = :unLogin AND comptable.mdp = :unMdp'
        );
        $requetePrepare->bindParam(':unLogin', $login, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMdp', $mdp, PDO::PARAM_STR);
        $requetePrepare->execute();
        return $requetePrepare->fetch();
    }

    /**
     * Retourne tout les mois cloturés
     *
     * @param string $idVisiteur ID du visiteur
     *
     * @return array des mois cloturés [idMois, annee, mois]
     */
    public function getToutLesMoisClotures($idVisiteur)
    {
        $requetePrepare = PdoGSB::$_monPdo->prepare(
            'SELECT fichefrais.mois '
            . 'FROM fichefrais '
            . "WHERE fichefrais.idetat = 'CL' "
            . 'AND fichefrais.idvisiteur = :unIdVisiteur '
            . 'ORDER BY fichefrais.mois desc'
        );
        $requetePrepare->bindParam(
            ':unIdVisiteur', $idVisiteur, PDO::PARAM_STR
        );
        $requetePrepare->execute();
        $lesMois = array();
        while ($laLigne = $requetePrepare->fetch()) {
            $mois = $laLigne['mois'];
            $numAnnee = substr($mois, 0, 4);
            $numMois = substr($mois, 4, 2);
            $lesMois[] = array(
                'mois' => $mois,
                'numAnnee' => $numAnnee,
                'numMois' => $numMois
            );
        }
        return $lesMois;
    }

    /**
     * Retourne tout les mois exceptés ceux en saisie
     *
     * @param string $idVisiteur ID du visiteur
     *
     * @return array tableau contenant les mois validés, validés et mise en paiement
     *         et les mois cloturés [idMois, l'année, le mois]
     */
    public function getToutLesMoisValidees($idVisiteur)
    {
        $requetePrepare = PdoGSB::$_monPdo->prepare(
            'SELECT * '
            . 'FROM fichefrais '
            . "WHERE (fichefrais.idvisiteur = :unIdVisiteur "
            . "AND fichefrais.idetat = 'V') "
            . "OR (fichefrais.idvisiteur = :unIdVisiteur "
            . "AND fichefrais.idetat = 'VA') "
            . "OR (fichefrais.idvisiteur = :unIdVisiteur "
            . "AND fichefrais.idetat = 'CL') "
            . 'ORDER BY fichefrais.mois DESC'
        );
        $requetePrepare->bindParam(
            ':unIdVisiteur', $idVisiteur, PDO::PARAM_STR
        );
        $requetePrepare->execute();
        $lesMois = array();
        while ($laLigne = $requetePrepare->fetch()) {
            $mois = $laLigne['mois'];
            $numAnnee = substr($mois, 0, 4);
            $numMois = substr($mois, 4, 2);
            $lesMois[] = array(
                'mois' => $mois,
                'numAnnee' => $numAnnee,
                'numMois' => $numMois
            );
        }
        return $lesMois;
    }

    /**
     * Récupère le montant pour chaque frais forfait
     * (se fier à la base de données pour la récupération par l'indice du tableau)
     *
     * @return array un tableau contenant l'indice et le montant de chaque frais
     */
    public function getLesPrixFraisForfait()
    {
        $requetePrepare = PdoGSB::$_monPdo->prepare(
            'SELECT fraisforfait.montant AS montant '
            . 'FROM fraisforfait'
        );
        $requetePrepare->execute();
        return $requetePrepare->fetchAll();
    }

    /**
     * Recupère le libelle du frais hors-forfait à partir de l'id du hors-forfait
     *
     * @param string $idHorsForfait ID du frais hors-forfait
     *
     * @return string le libelle du frais hors-forfait
     */
    public function getLibelleFraisHorsForfait($idHorsForfait)
    {
        $requetePrepare = PdoGSB::$_monPdo->prepare(
            'SELECT lignefraishorsforfait.libelle AS libelle '
            . 'FROM lignefraishorsforfait '
            . 'WHERE lignefraishorsforfait.id = :unIdHorsForfait'
        );
        $requetePrepare->bindParam(
            ':unIdHorsForfait',
            $idHorsForfait,
            PDO::PARAM_INT
        );
        $requetePrepare->execute();
        $laLigne = $requetePrepare->fetch();
        $libelle = $laLigne['libelle'];
        return $libelle;
    }

    /**
     * Récupère le mois et l'id du visiteur par rapport à l'id hors forfait
     * passé en paramètre
     *
     * @param string $idHorsForfait ID de l'hors forfait
     *
     * @return array un tableau contenant le mois et l'id du visiteur
     */
    public function getMoisVisiteurFraisHorsForfait($idHorsForfait)
    {
        $requetePrepare = PdoGSB::$_monPdo->prepare(
            'SELECT lignefraishorsforfait.mois AS mois, '
            . 'lignefraishorsforfait.idvisiteur AS idVisiteur '
            . 'FROM lignefraishorsforfait '
            . 'WHERE lignefraishorsforfait.id = :unIdHorsForfait'
        );
        $requetePrepare->bindParam(
            ':unIdHorsForfait',
            $idHorsForfait,
            PDO::PARAM_INT
        );
        $requetePrepare->execute();
        return $requetePrepare->fetch();
    }

    /**
     * Calcul le montant total des frais hors-forfait
     * qui ne sont pas refusés pour le mois du visiteur
     *
     * @param string $idVisiteur ID du visiteur
     * @param string $mois       ID du mois
     *
     * @return float le total des frais hors forfaits
     */
    public function getMontantTotalFraisHorsForfait($idVisiteur, $mois)
    {
        $requetePrepare = PdoGSB::$_monPdo->prepare(
            'SELECT lignefraishorsforfait.montant AS montant '
            . 'FROM lignefraishorsforfait '
            . 'WHERE lignefraishorsforfait.idvisiteur = :unIdVisiteur '
            . 'AND lignefraishorsforfait.mois = :unIdMois '
            . 'AND lignefraishorsforfait.refuse = 0'
        );
        $requetePrepare->bindParam(
            ':unIdVisiteur', $idVisiteur, PDO::PARAM_STR
        );
        $requetePrepare->bindParam(':unIdMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $montantFraisHF = 0.0;
        while ($laLigne = $requetePrepare->fetch()) {
            $montantFraisHF += $laLigne['montant'];
        }
        return $montantFraisHF;
    }

    /**
     * Requête pour récupérer le nom et le prénom du visiteur
     *
     * @param string $idVisiteur ID du visiteur
     *
     * @return string le nom et le prénom du visiteur
     */
    public function getNomPrenomVisiteur($idVisiteur)
    {
        $requetePrepare = PdoGsb::$_monPdo->prepare(
            'SELECT visiteur.nom AS nom, visiteur.prenom AS prenom FROM visiteur '
            . 'WHERE visiteur.id = :unIdVisiteur'
        );
        $requetePrepare->bindParam(
            ':unIdVisiteur', $idVisiteur, PDO::PARAM_STR
        );
        $requetePrepare->execute();
        $laLigne = $requetePrepare->fetch();
        $nom = $laLigne['nom'];
        $prenom = $laLigne['prenom'];
        return $nom . ' ' . $prenom;
    }

    /**
     * Récupère la quantité d'étape par le biais de l'id du mois et du visiteur
     *
     * @param string $idVisiteur ID du visiteur
     * @param string $mois       ID du mois
     *
     * @return integer la quantité d'étape
     */
    public function getQuantiteEtape($idVisiteur, $mois)
    {
        $requetePrepare = PdoGSB::$_monPdo->prepare(
            'SELECT lignefraisforfait.quantite AS quantite '
            . 'FROM lignefraisforfait '
            . 'WHERE lignefraisforfait.idvisiteur = :unIdVisiteur '
            . 'AND lignefraisforfait.mois = :unIdMois '
            . "AND lignefraisforfait.idfraisforfait = 'ETP'"
        );
        $requetePrepare->bindParam(
            ':unIdVisiteur', $idVisiteur, PDO::PARAM_STR
        );
        $requetePrepare->bindParam(':unIdMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $laLigne = $requetePrepare->fetch();
        $quantiteEtape = $laLigne['quantite'];
        return $quantiteEtape;
    }

    /**
     * Récupère la quantité de kilomètres par le biais de l'id du mois et du visiteur
     *
     * @param string $idVisiteur ID du visiteur
     * @param string $mois       ID du mois
     *
     * @return integer la quantité de kilomètres
     */
    public function getQuantiteKm($idVisiteur, $mois)
    {
        $requetePrepare = PdoGSB::$_monPdo->prepare(
            'SELECT lignefraisforfait.quantite AS quantite '
            . 'FROM lignefraisforfait '
            . 'WHERE lignefraisforfait.idvisiteur = :unIdVisiteur '
            . 'AND lignefraisforfait.mois = :unIdMois '
            . "AND lignefraisforfait.idfraisforfait = 'KM'"
        );
        $requetePrepare->bindParam(
            ':unIdVisiteur', $idVisiteur, PDO::PARAM_STR
        );
        $requetePrepare->bindParam(':unIdMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $laLigne = $requetePrepare->fetch();
        $quantiteKm = $laLigne['quantite'];
        return $quantiteKm;
    }

    /**
     * Récupère la quantité de nuitées par le biais de l'id du mois et du visiteur
     *
     * @param string $idVisiteur ID du visiteur
     * @param string $mois       ID du mois
     *
     * @return int la quantité de nuitées
     */
    public function getQuantiteNuitees($idVisiteur, $mois)
    {
        $requetePrepare = PdoGSB::$_monPdo->prepare(
            'SELECT lignefraisforfait.quantite AS quantite '
            . 'FROM lignefraisforfait '
            . 'WHERE lignefraisforfait.idvisiteur = :unIdVisiteur '
            . 'AND lignefraisforfait.mois = :unIdMois '
            . "AND lignefraisforfait.idfraisforfait = 'NUI'"
        );
        $requetePrepare->bindParam(
            ':unIdVisiteur', $idVisiteur, PDO::PARAM_STR
        );
        $requetePrepare->bindParam(':unIdMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $laLigne = $requetePrepare->fetch();
        $quantiteNuitee = $laLigne['quantite'];
        return $quantiteNuitee;
    }

    /**
     * Récupère la quantité de repas par le biais de l'id du mois et du visiteur
     *
     * @param string $idVisiteur ID du visiteur
     * @param string $mois       ID du mois
     *
     * @return int la quantité de repas
     */
    public function getQuantiteRepas($idVisiteur, $mois)
    {
        $requetePrepare = PdoGSB::$_monPdo->prepare(
            'SELECT lignefraisforfait.quantite AS quantite '
            . 'FROM lignefraisforfait '
            . 'WHERE lignefraisforfait.idvisiteur = :unIdVisiteur '
            . 'AND lignefraisforfait.mois = :unIdMois '
            . "AND lignefraisforfait.idfraisforfait = 'REP'"
        );
        $requetePrepare->bindParam(
            ':unIdVisiteur', $idVisiteur, PDO::PARAM_STR
        );
        $requetePrepare->bindParam(':unIdMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $laLigne = $requetePrepare->fetch();
        $quantiteRepas = $laLigne['quantite'];
        return $quantiteRepas;
    }

    /**
     * Retourne tout les mois pour lesquels un visiteur a déjà une fiche de frais
     *
     * @return array tableau associatif de clé un mois -aaaamm- et de valeurs
     *         l'année et le mois correspondant
     */
    public function getToutLesMoisDisponibles()
    {
        $requetePrepare = PdoGSB::$_monPdo->prepare(
            'SELECT DISTINCT fichefrais.mois AS mois FROM fichefrais '
            . 'ORDER BY fichefrais.mois desc'
        );
        $requetePrepare->execute();
        $lesMois = array();
        while ($laLigne = $requetePrepare->fetch()) {
            $mois = $laLigne['mois'];
            $numAnnee = substr($mois, 0, 4);
            $numMois = substr($mois, 4, 2);
            $lesMois[] = array(
                'mois' => $mois,
                'numAnnee' => $numAnnee,
                'numMois' => $numMois
            );
        }
        return $lesMois;
    }

    /**
     * Retourne tout les visiteurs
     *
     * @return array tableau associatif de clé un nom et un prénom
     */
    public function getToutLesVisiteurs()
    {
        $requetePrepare = PdoGsb::$_monPdo->prepare(
            'SELECT visiteur.id AS id, '
            . 'visiteur.nom AS nom, '
            . 'visiteur.prenom AS prenom '
            . 'FROM visiteur '
            . 'ORDER BY visiteur.nom asc'
        );
        $requetePrepare->execute();
        $lesVisiteurs = array();
        while ($laLigne = $requetePrepare->fetch()) {
            $id = $laLigne['id'];
            $nom = $laLigne['nom'];
            $prenom = $laLigne['prenom'];
            $lesVisiteurs[] = array(
                'id' => $id,
                'nom' => $nom,
                'prenom' => $prenom
            );
        }
        return $lesVisiteurs;
    }

    /**
     * Met à jour un frais hors-forfait pour le mois d'un visiteur
     *
     * @param string $mois          ID du mois
     * @param string $idHorsForfait ID du frais hors-forfait
     *
     * @return null
     */
    public function majFraisHorsForfait($mois, $idHorsForfait)
    {
        $requetePrepare = PdoGSB::$_monPdo->prepare(
            'UPDATE lignefraishorsforfait '
            . 'SET lignefraishorsforfait.mois = :unIdMois '
            . 'WHERE lignefraishorsforfait.id = :unIdHorsForfait'
        );
        $requetePrepare->bindParam(':unIdMois', $mois, PDO::PARAM_STR);
        $requetePrepare->bindParam(
            ':unIdHorsForfait',
            $idHorsForfait,
            PDO::PARAM_INT
        );
        $requetePrepare->execute();
    }

    /**
     * Met en paiement une fiche de frais et lui affecte la date actuelle
     *
     * @param string $idVisiteur ID du visiteur
     * @param string $mois       ID du mois
     *
     * @return null
     */
    public function mettreEnPaiementFrais($idVisiteur, $mois)
    {
        $requetePrepare = PdoGSB::$_monPdo->prepare(
            'UPDATE fichefrais '
            . "SET datemodif = now(), idetat = 'VA' "
            . 'WHERE idvisiteur = :unIdVisiteur AND mois = :unIdMois'
        );
        $requetePrepare->bindParam(
            ':unIdVisiteur', $idVisiteur, PDO::PARAM_STR
        );
        $requetePrepare->bindParam(':unIdMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
    }

    /**
     * Met en reboursement une fiche de frais et lui affecte la date actuelle
     *
     * @param string $idVisiteur ID du visiteur
     * @param string $mois       ID du mois
     *
     * @return null
     */
    public function mettreEnRemboursementFrais($idVisiteur, $mois)
    {
        $requetePrepare = PdoGSB::$_monPdo->prepare(
            'UPDATE fichefrais '
            . "SET datemodif = now(), idetat = 'RB' "
            . 'WHERE idvisiteur = :unIdVisiteur AND mois = :unIdMois'
        );
        $requetePrepare->bindParam(
            ':unIdVisiteur', $idVisiteur, PDO::PARAM_STR
        );
        $requetePrepare->bindParam(':unIdMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
    }

    /**
     * Récupère le libelle du hors-forfait et lui ajoute le texte "REFUSE : "
     * puis met à jour le frais hors-forfait si il n'est pas déjà refusé.
     *
     * @param string $idHorsForfait ID du hors-forfait
     *
     * @return null
     */
    public function refusFraisHorsForfait($idHorsForfait)
    {
        $libelle = $this->getLibelleFraisHorsForfait($idHorsForfait);
        $libelleRefus = 'REFUSE : ' . $libelle;
        if (strlen($libelleRefus) > 100) {
            $taille = strlen($libelleRefus);
            $taille -= 100;
            $libelle = substr($libelleRefus, 0, -$taille);
        } else {
            $libelle = $libelleRefus;
        }
        $requetePrepare = PdoGSB::$_monPdo->prepare(
            'UPDATE lignefraishorsforfait '
            . 'SET lignefraishorsforfait.libelle = :unLibelleDeRefus, '
            . 'lignefraishorsforfait.refuse = 1 '
            . 'WHERE lignefraishorsforfait.id = :unIdHorsForfait '
            . 'AND lignefraishorsforfait.refuse = 0'
        );
        $requetePrepare->bindParam(
            ':unLibelleDeRefus',
            $libelleRefus,
            PDO::PARAM_STR
        );
        $requetePrepare->bindParam(
            ':unIdHorsForfait',
            $idHorsForfait,
            PDO::PARAM_INT
        );
        $requetePrepare->execute();
    }

    /**
     * Récupère l'id du frais du visiteur et le mois
     * à partir de la table lignefraishorsforfait
     * puis converti le mois (String) en (Integer) pour passer
     * sur le mois suivant puis le retransforme en (String)
     * pour l'intégrer dans la base de données.
     * Test si la fiche de frais existe si elle n'existe pas alors on la crée
     * ensuite on met à jour le frais hors-forfait pour le mois suivant.
     *
     * @param string $idHorsForfait ID de l'hors-forfait
     *
     * @return null
     */
    public function reportFraisHorsForfait($idHorsForfait)
    {
        $laLigne = $this->getMoisVisiteurFraisHorsForfait($idHorsForfait);
        $mois = $laLigne['mois'];
        $idVisiteur = $laLigne['idVisiteur'];
        $intNumAnnee = intval(substr($mois, 0, 4));
        $intNumMois = intval(substr($mois, 4, 2));
        $moisSuivant = $intNumMois + 1;
        if ($moisSuivant > 12) {
            $intNumAnnee++;
            $intNumMois = 1;
        } else {
            $intNumMois++;
        }
        $strAnneeMois = strval($intNumAnnee * 100 + $intNumMois);

        $idFrais = $this->ficheFraisExiste($idVisiteur, $strAnneeMois);

        if ($idFrais == false) {
            $this->creeNouvelleFicheFrais($idVisiteur, $strAnneeMois);
        }
        $this->majFraisHorsForfait($strAnneeMois, $idHorsForfait);
    }

    /**
     * Récupère les différent prix des frais forfait
     * puis récupère les différentes quantités de ces frais
     * pour créer un montant total de frais forfait.
     * Récupère le montant total des frais hors-forfait qui ne sont pas refusés.
     * On additionne alors les deux pour avoir le montant à validé de la fiche.
     * On met à jour la fiche avec le montant, on change
     * la date pour quelle concorde avec la modification
     * et on passe l'état de la fiche a validé
     *
     * @param string $idVisiteur ID du visiteur
     * @param string $mois       ID du mois
     *
     * @return null
     */
    public function mettreEnValidationFrais($idVisiteur, $mois)
    {
        $lesLignes = $this->getLesPrixFraisForfait();
        $prixEtape = $lesLignes[0]['montant'];
        $prixKm = $lesLignes[1]['montant'];
        $prixNuitee = $lesLignes[2]['montant'];
        $prixRepas = $lesLignes[3]['montant'];
        $quantiteEtape = $this->getQuantiteEtape($idVisiteur, $mois);
        $quantiteKm = $this->getQuantiteKm($idVisiteur, $mois);
        $quantiteNuitee = $this->getQuantiteNuitees($idVisiteur, $mois);
        $quantiteRepas = $this->getQuantiteRepas($idVisiteur, $mois);
        $montantFrais = ($quantiteEtape * $prixEtape)
            + ($quantiteKm * $prixKm)
            + ($quantiteNuitee * $prixNuitee)
            + ($quantiteRepas * $prixRepas);
        $montantFraisHF = $this->getMontantTotalFraisHorsForfait(
            $idVisiteur, $mois
        );
        $montant = $montantFrais + $montantFraisHF;
        $requetePrepare = PdoGSB::$_monPdo->prepare(
            'UPDATE fichefrais '
            . "SET datemodif = now(), idetat = 'V', "
            . "montantvalide = :unMontantValidee "
            . 'WHERE idvisiteur = :unIdVisiteur AND mois = :unIdMois'
        );
        $requetePrepare->bindParam(
            ':unMontantValidee', $montant, PDO::PARAM_STR
        );
        $requetePrepare->bindParam(
            ':unIdVisiteur', $idVisiteur, PDO::PARAM_STR
        );
        $requetePrepare->bindParam(':unIdMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
    }

    /**
     * Retourne l'id d'un visiteur
     *
     * @param string $login Login du visiteur
     * @param string $mdp   Mot de passe du visiteur
     *
     * @return string ID du visiteur
     */
    public function getIdVisiteur($login, $mdp)
    {
        $requetePrepare = PdoGsb::$_monPdo->prepare(
            'SELECT visiteur.id AS id '
            . 'FROM visiteur '
            . 'WHERE visiteur.login = :unLogin AND visiteur.mdp = :unMdp'
        );
        $requetePrepare->bindParam(':unLogin', $login, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMdp', $mdp, PDO::PARAM_STR);
        $requetePrepare->execute();
        $laLigne = $requetePrepare->fetch();
        return $laLigne['id'];
    }

    /**
     * Met à jour la table ligneFraisForfait pour un visiteur et
     * un mois donné en enregistrant
     * le nouveaux montant pour l'id du frais passé en paramètre
     *
     * @param string  $idVisiteur ID du visiteur
     * @param string  $mois       Mois sous la forme aaaamm
     * @param integer $qte        Montant du frais
     * @param string  $idFrais    ID du frais à modifier
     *
     * @return null
     */
    public function majLigneFraisForfait($idVisiteur, $mois, $qte, $idFrais)
    {
        $requetePrepare = PdoGSB::$_monPdo->prepare(
            'UPDATE lignefraisforfait '
            . 'SET lignefraisforfait.quantite = :uneQte '
            . 'WHERE lignefraisforfait.idvisiteur = :unIdVisiteur '
            . 'AND lignefraisforfait.mois = :unMois '
            . 'AND lignefraisforfait.idfraisforfait = :unIdFrais'
        );
        $requetePrepare->bindParam(':uneQte', $qte, PDO::PARAM_INT);
        $requetePrepare->bindParam(
            ':unIdVisiteur', $idVisiteur, PDO::PARAM_STR
        );
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unIdFrais', $idFrais, PDO::PARAM_STR);
        $requetePrepare->execute();
    }

    /**
     * Insere une nouvelle ligneFraisForfait pour un visiteur,
     * un mois et une quantité
     *
     * @param string  $idVisiteur ID du visiteur
     * @param string  $mois       Mois sous la forme aaaamm
     * @param integer $qte        Montant du frais
     * @param string  $idFrais    ID du frais à modifier
     *
     * @return null
     */
    public function creerLigneFraisForfait($idVisiteur, $mois, $qte, $idFrais)
    {
        $requetePrepare = PdoGsb::$_monPdo->prepare(
            'INSERT INTO lignefraisforfait (idvisiteur,mois,'
            . 'idfraisforfait,quantite) '
            . 'VALUES(:unIdVisiteur, :unMois, :unIdFrais, :uneQte)'
        );
        $requetePrepare->bindParam(':uneQte', $qte, PDO::PARAM_INT);
        $requetePrepare->bindParam(
            ':unIdVisiteur', $idVisiteur, PDO::PARAM_STR
        );
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unIdFrais', $idFrais, PDO::PARAM_STR);
        $requetePrepare->execute();
    }

    /**
     * Cherche si un frais forfait existe pour le visiteur, le mois et l'id du frais
     * donné et renvoi vrai ou faux selon le cas
     *
     * @param string $idVisiteur ID du visiteur
     * @param string $mois       ID du mois
     * @param string $idFrais    ID du frais
     *
     * @return boolean vrai si le frais existe sinon retourne faux
     */
    public function ligneFraisForfaitExiste($idVisiteur, $mois, $idFrais)
    {
        $requetePrepare = PdoGsb::$_monPdo->prepare(
            'SELECT lignefraisforfait.quantite AS quantite '
            . 'FROM lignefraisforfait '
            . 'WHERE lignefraisforfait.idvisiteur = :unIdVisiteur '
            . 'AND lignefraisforfait.idfraisforfait = :unIdFrais '
            . 'AND lignefraisforfait.mois = :unMois'
        );
        $requetePrepare->bindParam(
            ':unIdVisiteur', $idVisiteur, PDO::PARAM_STR
        );
        $requetePrepare->bindParam(':unIdFrais', $idFrais, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $laLigne = $requetePrepare->fetch();
        if (is_null($laLigne['quantite'])) {
            return false;
        }
        return true;
    }

}
