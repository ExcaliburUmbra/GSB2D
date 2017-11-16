<?php
/** 
 * Classe d'accès aux données. 
 
 * Utilise les services de la classe PDO
 * pour l'application GSB
 * Les attributs sont tous statiques,
 * les 4 premiers pour la connexion
 * $monPdo de type PDO 
 * $monPdoGsb qui contiendra l'unique instance de la classe
 
 * @package default
 * @author Cheri Bibi
 * @version    1.0
 * @link       http://www.php.net/manual/fr/book.pdo.php
 */
class PdoGsb {
	private static $serveur = 'mysql:host=localhost';
	private static $bdd = 'dbname=gsb2016';
	private static $user = 'root';
	private static $mdp = '';
	private static $monPdo;
	private static $monPdoGsb = null;
	/**
	 * Constructeur privé, crée l'instance de PDO qui sera sollicitée
	 * pour toutes les méthodes de la classe
	 */
	private function __construct() {
		PdoGsb::$monPdo = new PDO ( PdoGsb::$serveur . ';' . PdoGsb::$bdd, PdoGsb::$user, PdoGsb::$mdp );
		PdoGsb::$monPdo->query ( "SET CHARACTER SET utf8" );
	}
	public function _destruct() {
		PdoGsb::$monPdo = null;
	}
	/**
	 * Fonction statique qui crée l'unique instance de la classe
	 *
	 * Appel : $instancePdoGsb = PdoGsb::getPdoGsb();
	 *
	 * @return l'unique objet de la classe PdoGsb
	 */
	public static function getPdoGsb() {
		if (PdoGsb::$monPdoGsb == null) {
			PdoGsb::$monPdoGsb = new PdoGsb ();
		}
		return PdoGsb::$monPdoGsb;
	}
	/**
	 * Retourne les informations d'un visiteur
	 *
	 * @param
	 *        	$login
	 * @param
	 *        	$mdp
	 * @return l'id, le nom et le prénom sous la forme d'un tableau associatif
	 *        
	 */
	public function getInfosVisiteur($login, $mdp) {
		$sql = PdoGsb::$monPdo->prepare ( "select id, nom, prenom from visiteur
		where login=:login and mdp=:mdp" );
		$sql->bindParam ( ':login', $login );
		$sql->bindParam ( ':mdp', $mdp );
		$sql->execute ();
		$ligne = $sql->fetch ();
		return $ligne;
	}
	
	public function getInfosComptable($login, $mdp) {
		$sql = PdoGsb::$monPdo->prepare ( "select id, nom, prenom from comptable
		where login=:login and mdp=:mdp" );
		$sql->bindParam ( ':login', $login );
		$sql->bindParam ( ':mdp', $mdp );
		$sql->execute ();
		$ligne = $sql->fetch ();
		return $ligne;
	}
	public function getLesFichesFraisVA() {
		$sql = PdoGsb::$monPdo->prepare ( "select * from fichefrais where idEtat='VA' order by fichefrais.idvisiteur" );
		$sql->execute ();
		$req = $sql->fetchAll ();
		return $req;
	}
	public function getLesFichesFraisRB() {
		$sql = PdoGsb::$monPdo->prepare ( "select * from fichefrais where idEtat='RB' order by fichefrais.idvisiteur" );
		$sql->execute ();
		$req = $sql->fetchAll ();
		return $req;
	}
	
	public function rembourse($idVisiteur,$mois)
	{
		$sql = PdoGsb::$monPdo->prepare("BEGIN;
		UPDATE fichefrais SET idEtat='RB' WHERE idVisiteur='$idVisiteur' AND mois='$mois';
		UPDATE fichefrais SET dateModif=CURRENT_DATE WHERE idVisiteur='$idVisiteur' AND mois='$mois';
		COMMIT;");
		$sql->execute();
	}
	/**
	 * Retourne les informations de tous les visiteurs
	 *
	 *
	 * @return l'id, le nom, le prénom, l'adresse, le code postal, la ville, la date d'embauche sous la forme d'un tableau associatif
	 *        
	 */
	public function getToutesLesInfosDesVisiteurs() {
		$sql = PdoGsb::$monPdo->prepare ( "select * from visiteur order by nom, prenom;" );
		$sql->execute ();
		$req = $sql->fetchAll ();
		return $req;
	}
	
	/**
	 * Retourne les informations d'un gestionnaire
	 *
	 * @param
	 *        	$login
	 * @param
	 *        	$mdp
	 * @return l'id, le nom et le prénom sous la forme d'un tableau associatif
	 */
	public function getInfosGestionnaire($login, $mdp) {
		$req = PdoGsb::$monPdo->prepare ( "select id, nom, prenom from gestionnaire where login=:login and mdp=:mdp" );
		$req->bindParam ( ':login', $login );
		$req->bindParam ( ':mdp', $mdp );
		$req->execute ();
		return $req->fetch ();
	}
}
?>