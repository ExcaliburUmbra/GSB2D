<?php
use Symfony\Component\HttpFoundation\Request;
use Silex\Application;
class CouteauSuisse {
	/**
	 * Enregistre dans une variable session les infos d'un visiteur
	 *
	 * @param
	 *        	$id
	 * @param
	 *        	$nom
	 * @param
	 *        	$prenom
	 */
	public function connecter($id, $nom, $prenom) {
		$_SESSION ['id'] = $id;
		$_SESSION ['nom'] = $nom;
		$_SESSION ['prenom'] = $prenom;
	}
	
	/**
	 * Teste si un quelconque visiteur est connecté
	 *
	 * @return vrai ou faux
	 */
	public function estConnecte() {
		return isset ( $_SESSION ['id'] );
	}
	
	/**
	 * Détruit la session active
	 */
	public function deconnecter() {
		session_destroy ();
	}
	/**
	 * Transforme une date au format français jj/mm/aaaa vers le format anglais aaaa-mm-jj
	 *
	 * @param $madate au
	 *        	format jj/mm/aaaa
	 * @return la date au format anglais aaaa-mm-jj
	 *        
	 */
	public function dateFrancaisVersAnglais($maDate) {
		@list ( $jour, $mois, $annee ) = explode ( '/', $maDate );
		return date ( 'Y-m-d', mktime ( 0, 0, 0, $mois, $jour, $annee ) );
	}
	/**
	 * Transforme une date au format format anglais aaaa-mm-jj vers le format français jj/mm/aaaa
	 *
	 * @param $madate au
	 *        	format aaaa-mm-jj
	 * @return la date au format format français jj/mm/aaaa
	 *        
	 */
	public function dateAnglaisVersFrancais($maDate) {
		@list ( $annee, $mois, $jour ) = explode ( '-', $maDate );
		$date = "$jour" . "/" . $mois . "/" . $annee;
		return $date;
	}
	/**
	 * retourne le mois au format aaaamm selon le jour dans le mois
	 *
	 * @param $date au
	 *        	format jj/mm/aaaa
	 * @return le mois au format aaaamm
	 *        
	 */
	public function getMois($date) {
		@list ( $jour, $mois, $annee ) = explode ( '/', $date );
		if (strlen ( $mois ) == 1) {
			$mois = "0" . $mois;
		}
		return $annee . $mois;
	}
	
	/**
	 * Indique si une valeur est un entier positif ou nul
	 *
	 * @param
	 *        	$valeur
	 * @return vrai ou faux
	 *        
	 */
	public function estEntierPositif($valeur) {
		return preg_match ( "/[^0-9]/", $valeur ) == 0;
	}
	
	/**
	 * Indique si un tableau de valeurs est constitué d'entiers positifs ou nuls
	 *
	 * @param $tabEntiers :
	 *        	le tableau
	 * @return vrai ou faux
	 */
	public function estTableauEntiers($tabEntiers) {
		$ok = true;
		foreach ( $tabEntiers as $unEntier ) {
			if (! $this->estEntierPositif ( $unEntier )) {
				$ok = false;
			}
		}
		return $ok;
	}
	/**
	 * Vérifie si une date est inférieure d'un an à la date actuelle
	 *
	 * @param
	 *        	$dateTestee
	 * @return vrai ou faux
	 *        
	 */
	public function estDateDepassee($dateTestee) {
		$dateActuelle = date ( "d/m/Y" );
		@list ( $jour, $mois, $annee ) = explode ( '/', $dateActuelle );
		$annee --;
		$AnPasse = $annee . $mois . $jour;
		@list ( $jourTeste, $moisTeste, $anneeTeste ) = explode ( '/', $dateTestee );
		return ($anneeTeste . $moisTeste . $jourTeste < $AnPasse);
	}
	/**
	 * Vérifie la validité du format d'une date française jj/mm/aaaa
	 *
	 * @param
	 *        	$date
	 * @return vrai ou faux
	 *        
	 */
	public function estDateValide($date) {
		$tabDate = explode ( '/', $date );
		$dateOK = true;
		if (count ( $tabDate ) != 3) {
			$dateOK = false;
		} else {
			if (! $this->estTableauEntiers ( $tabDate )) {
				$dateOK = false;
			} else {
				if (! checkdate ( $tabDate [1], $tabDate [0], $tabDate [2] )) {
					$dateOK = false;
				}
			}
		}
		return $dateOK;
	}
	
	/**
	 * Vérifie que le tableau de frais ne contient que des valeurs numériques
	 *
	 * @param
	 *        	$lesFrais
	 * @return vrai ou faux
	 *        
	 */
	function lesQteFraisValides($lesFrais) {
		return $this->estTableauEntiers ( $lesFrais );
	}
	/**
	 * Vérifie la validité des trois arguments : la date, le libellé du frais et le montant
	 *
	 * des message d'erreurs sont ajoutés au tableau des erreurs
	 *
	 * @param
	 *        	$dateFrais
	 * @param
	 *        	$libelle
	 * @param
	 *        	$montant
	 */
	function valideInfosFrais($dateFrais, $libelle, $montant) {
		if ($dateFrais == "") {
			$this->ajouterErreur ( "Le champ date ne doit pas être vide" );
		} else {
			if (! $this->estDatevalide ( $dateFrais )) {
				$this->ajouterErreur ( "Date invalide" );
			} else {
				if (estDateDepassee ( $dateFrais )) {
					$this->ajouterErreur ( "date d'enregistrement du frais dépassé, plus de 1 an" );
				}
			}
		}
		if ($libelle == "") {
			$this->ajouterErreur ( "Le champ description ne peut pas être vide" );
		}
		if ($montant == "") {
			$this->ajouterErreur ( "Le champ montant ne peut pas être vide" );
		} else if (! is_numeric ( $montant )) {
			$this->ajouterErreur ( "Le champ montant doit être numérique" );
		}
	}
	/**
	 * Ajoute le libellÃ© d'un info au tableau des informations
	 *
	 * @param $msg :
	 *        	le libellÃ© de l'information
	 */
	function ajouterInformationUtilisateur($msg) {
		if (! isset ( $_REQUEST ['info'] )) {
			$_REQUEST ['info'] = array ();
		}
		$_REQUEST ['info'] [] = $msg;
	}
	
	/**
	 * Ajoute le libellé d'une erreur au tableau des erreurs
	 *
	 * @param $msg :
	 *        	le libellé de l'erreur
	 */
	function ajouterErreur($msg) {
		if (! isset ( $_REQUEST ['erreurs'] )) {
			$_REQUEST ['erreurs'] = array ();
		}
		$_REQUEST ['erreurs'] [] = $msg;
	}
	/**
	 * Retoune le nombre de lignes du tableau des erreurs
	 *
	 * @return le nombre d'erreurs
	 */
	function nbErreurs() {
		if (! isset ( $_REQUEST ['erreurs'] )) {
			return 0;
		} else {
			return count ( $_REQUEST ['erreurs'] );
		}
	}
	/**
	 * Permet de supprimer les cookies du navigateur ainsi que de détruire correctement la session
	 */
	function Logout() {
		$_SESSION = array ();
		if (ini_get ( "session.use_cookies" )) {
			$params = session_get_cookie_params ();
			setcookie ( session_name (), '', time () - 42000, $params ["path"], $params ["domain"], $params ["secure"], $params ["httponly"] );
		}
		session_destroy ();
		session_start ();
		$_SESSION ['result'] = " ";
	}
	/**
	 *
	 * @param
	 *        	le type de caractère
	 *        	$typeChar
	 * @param
	 *        	la position de début de la chaine
	 *        	$debut
	 * @param
	 *        	la position de la fin de la chaine
	 *        	$fin
	 * @return string retourne un identifiant tel que "LXXX"
	 *        
	 */
	function random($typeChar, $debut, $fin) {
		if ($typeChar == "char") {
			$char = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		} else if ($typeChar == 'charNum') {
			$char = 'abcdefghijklmnopqrstuvwxyz01234569';
		} else if ($typeChar == 'Num') {
			$char = '1234567890';
		}
		
		$randomChar = str_shuffle ( $char );
		return $randomChar = substr ( $randomChar, $debut, $fin );
	}
	/**
	 * Retourne les informations des tous les visiteurs dans un PDF
	 *
	 * @param
	 *        	$infoV
	 */
	function PdfEtatMembre($infoV) {
		// cr�ation d'un objet PDF
		$pdf = new PDF ();
		// variable pour le num�ro du visiteur
		$nb = 0;
		// on compte le nombre de pages
		$pdf->AliasNbPages ();
		// on choisit la police
		$pdf->SetFont ( 'Times', 'B', 15 );
		foreach ( $infoV as $visiteur ) {
			// variable qui permet de compter les visiteurs.
			$nb ++;
			// ajout d'une nouvelle page
			$pdf->AddPage ();
			// le SetX permet de se positionner sur l'axe des abscisses (de gauche � droite)
			$pdf->SetX ( 25 );
			// on affiche ensuite les valeurs du param�tre en entr�e ($infoV)
			// le multiCell est une m�thode qui permet de positionner n'importe quel texte ou l'on souhaite dans le PDF
			// Param 1 : position sur l'axe X
			// Param 2 : position sur l'axe Y
			// Param 3 : le texte � afficher
			// Param 4 : les conteurs : si on veut des contours comme un tableau en noir etc..
			// Param 5 : la position du texte dans le PDF(center, right, left)
			// Param 6 : le remplissage du reste de la cellule.
			$pdf->MultiCell ( 0, 10, "Visiteur n�" . $nb );
			$pdf->MultiCell ( 0, 20, 'Nom  : ' . $visiteur ['nom'], 0, 'C' );
			$pdf->MultiCell ( 0, 30, 'Pr�nom : ' . $visiteur ['prenom'], 0, 'C' );
			$pdf->MultiCell ( 0, 40, 'Adresse : ' . $visiteur ['adresse'] . " " . $visiteur ['cp'] . " " . $visiteur ['ville'], 0, 'C' );
			$pdf->MultiCell ( 0, 50, "Date d'embauche : " . $visiteur ['dateEmbauche'], 0, 'C' );
		}
		// on renvoie le PDF au navigateur
		$pdf->Output ();
	}
}

/* ------------------------Fin classe--------------------------- */

/* ------------------------Création du service-------------------- */

$app ['couteauSuisse'] = function () {
	return new CouteauSuisse ();
};