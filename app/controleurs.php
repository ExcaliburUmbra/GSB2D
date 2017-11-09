<?php
require_once __DIR__ . '/../modele/class.pdogsb.php';
use Symfony\Component\HttpFoundation\Request;
use Silex\Application;
use Symfony\Component\HttpFoundation\Response;
use Silex\RedirectableUrlMatcher;

// ********************************************ContrÃ´leur connexion*****************//
class ConnexionControleur {
	public function __construct() {
		ob_start (); // dÃ©marre le flux de sortie
		$_SESSION ['msgEntete'] = "Accueil";
		require_once __DIR__ . '/../vues/v_entete.php';
	}
	public function accueil() {
		require_once __DIR__ . '/../vues/v_connexion.php';
		require_once __DIR__ . '/../vues/v_pied.php';
		$view = ob_get_clean (); // rÃ©cupÃ¨re le contenu du flux et le vide
		return $view; // retourne le flux
	}
	public function verifierUser(Request $request, Application $app) {
		session_start ();
		$login = htmlentities($request->get ( 'login' ));
		$mdp = htmlentities($request->get ( 'mdp' ));
		$pdo = PdoGsb::getPdoGsb ();
		$visiteur = $pdo->getInfosVisiteur ( $login, $mdp );
		if (! is_array ( $visiteur )) {
			$comptable = $pdo->getInfosComptable ( $login, $mdp );
			if (! is_array ( $comptable )) {
				$app ['couteauSuisse']->ajouterErreur ( "Login ou mot de passe incorrect" );
				require_once __DIR__ . '/../vues/v_erreurs.php';
				require_once __DIR__ . '/../vues/v_connexion.php';
				require_once __DIR__ . '/../vues/v_pied.php';
				$view = ob_get_clean ();
			} else {
				$id = $comptable ['id'];
				$nom = $comptable ['nom'];
				$prenom = $comptable ['prenom'];
				$_SESSION ['categorie'] = "comptable";
				$_SESSION ['msgEntete'] = "Gestion des frais";
				$app ['couteauSuisse']->connecter ( $id, $nom, $prenom );
				require_once __DIR__ . '/../vues/v_sommaire.php';
				require_once __DIR__ . '/../vues/v_pied.php';
				$view = ob_get_clean ();
			}
		} else {
			$id = $visiteur ['id'];
			$nom = $visiteur ['nom'];
			$prenom = $visiteur ['prenom'];
			$_SESSION ['categorie'] = "visiteur";
			$_SESSION ['msgEntete'] = "Suivi du remboursement des frais";
			$app ['couteauSuisse']->connecter ( $id, $nom, $prenom );
			require_once __DIR__ . '/../vues/v_sommaire.php';
			require_once __DIR__ . '/../vues/v_pied.php';
			$view = ob_get_clean ();
		}
		return $view;
	}
	public function deconnecter(Application $app) {
		$app ['couteauSuisse']->deconnecter ();
		$app ['couteauSuisse']->Logout ();
		return $app->redirect ( 'index.php' );
	}
}
// **************************************ContrÃ´leur EtatFrais**********************
class EtatFraisControleur {
	private $idVisiteur;
	private $pdo;
	public function init() {
		$this->idVisiteur = $_SESSION ['id'];
		$this->pdo = PdoGsb::getPdoGsb ();
		ob_start (); // dÃ©marre le flux de sortie
		require_once __DIR__ . '/../vues/v_entete.php';
		require_once __DIR__ . '/../vues/v_sommaire.php';
	}
	public function selectionnerMois(Application $app) {
		session_start ();
		if ($app ['couteauSuisse']->estConnecte ()) {
			$this->init ();
			$lesMois = $this->pdo->getLesMoisDisponibles ( $this->idVisiteur );
			// Afin de sÃ©lectionner par dÃ©faut le dernier mois dans la zone de liste
			// on demande toutes les clÃ©s, et on prend la premiÃ¨re,
			// les mois Ã©tant triÃ©s dÃ©croissants
			$lesCles = array_keys ( $lesMois );
			$moisASelectionner = $lesCles [0];
			require_once __DIR__ . '/../vues/v_listeMois.php';
			require_once __DIR__ . '/../vues/v_pied.php';
			$view = ob_get_clean ();
			return $view;
		} else {
			return Response::HTTP_NOT_FOUND;
		}
	}
	public function voirFrais(Request $request, Application $app) {
		session_start ();
		if ($app ['couteauSuisse']->estConnecte ()) {
			$this->init ();
			$leMois = $request->get ( 'lstMois' );
			$this->pdo = PdoGsb::getPdoGsb ();
			$lesMois = $this->pdo->getLesMoisDisponibles ( $this->idVisiteur );
			$moisASelectionner = $leMois;
			$lesFraisForfait = $this->pdo->getLesFraisForfait ( $this->idVisiteur, $leMois );
			$lesInfosFicheFrais = $this->pdo->getLesInfosFicheFrais ( $this->idVisiteur, $leMois );
			$numeroAnnee = substr ( $leMois, 0, 4 );
			$numeroMois = substr ( $leMois, 4, 2 );
			$libEtat = $lesInfosFicheFrais ['libEtat'];
			$montantValide = $lesInfosFicheFrais ['montantValide'];
			$nbJustificatifs = $lesInfosFicheFrais ['nbJustificatifs'];
			$dateModif = $lesInfosFicheFrais ['dateModif'];
			$dateModif = $app ['couteauSuisse']->dateAnglaisVersFrancais ( $dateModif );
			require_once __DIR__ . '/../vues/v_listeMois.php';
			require_once __DIR__ . '/../vues/v_etatFrais.php';
			require_once __DIR__ . '/../vues/v_pied.php';
			$view = ob_get_clean ();
			return $view;
		} else {
			$response = new Response ();
			$response->setContent ( 'Connexion nÃ©cessaire' );
			return $response;
		}
	}
}
// ************************************Controleur GererFicheFrais********************
class GestionFicheFraisControleur {
	private $pdo;
	private $mois;
	private $idVisiteur;
	private $numAnnee;
	private $numMois;
	public function init(Application $app) {
		$this->idVisiteur = $_SESSION ['id'];
		ob_start ();
		require_once __DIR__ . '/../vues/v_entete.php';
		require_once __DIR__ . '/../vues/v_sommaire.php';
		$this->mois = $app ['couteauSuisse']->getMois ( date ( "d/m/Y" ) );
		$this->numAnnee = substr ( $this->mois, 0, 4 );
		$this->numMois = substr ( $this->mois, 4, 2 );
		$this->pdo = PdoGsb::getPdoGsb ();
	}
	public function saisirFrais(Application $app) {
		session_start ();
		if ($app ['couteauSuisse']->estConnecte ()) {
			$this->init ( $app );
			if ($this->pdo->estPremierFraisMois ( $this->idVisiteur, $this->mois )) {
				$this->pdo->creeNouvellesLignesFrais ( $this->idVisiteur, $this->mois );
			}
			$lesFraisForfait = $this->pdo->getLesFraisForfait ( $this->idVisiteur, $this->mois );
			$numMois = $this->numMois;
			$numAnnee = $this->numAnnee;
			require_once __DIR__ . '/../vues/v_listeFraisForfait.php';
			require_once __DIR__ . '/../vues/v_pied.php';
			$view = ob_get_clean ();
			return $view;
		} else {
			$response = new Response ();
			$response->setContent ( 'Connexion nécessaire' );
			return $response;
		}
	}
	public function validerFrais(Request $request, Application $app) {
		session_start ();
		if ($app ['couteauSuisse']->estConnecte ()) {
			$this->init ( $app );
			$lesFrais = $request->get ( 'lesFrais' );
			if ($app ['couteauSuisse']->lesQteFraisValides ( $lesFrais )) {
				$this->pdo->majFraisForfait ( $this->idVisiteur, $this->mois, $lesFrais );
			} else {
				$app ['couteauSuisse']->ajouterErreur ( "Les valeurs des frais doivent être numériques" );
				require_once __DIR__ . '/../vues/v_erreurs.php';
				require_once __DIR__ . '/../vues/v_pied.php';
			}
			$lesFraisForfait = $this->pdo->getLesFraisForfait ( $this->idVisiteur, $this->mois );
			$numMois = $this->numMois;
			$numAnnee = $this->numAnnee;
			require_once __DIR__ . '/../vues/v_listeFraisForfait.php';
			require_once __DIR__ . '/../vues/v_pied.php';
			$view = ob_get_clean ();
			return $view;
		} else {
			$response = new Response ();
			$response->setContent ( 'Connexion nécessaire' );
			return $response;
		}
	}
}
// *************************Controleur du comptable ********************************
class gestionGestionnaireControleur {
	private $idGestionnaire;
	private $pdo;
	public function init(Application $app) {
		$this->idGestionnaire = $_SESSION ['id'];
		$this->pdo = PdoGsb::getPdoGsb ();
	}
	public function ListeDesFichesFraisRB(Application $app) {
		session_start ();
		if ($app ['couteauSuisse']->estConnecte ()) {
			$this->init ( $app );
			// récupération de toutes les fiches de frais
			$infoV = $this->pdo->getLesFichesFraisRB();
			require_once __DIR__ . '/../vues/v_entete.php';
			require_once __DIR__ . '/../vues/v_sommaire.php';
			require_once __DIR__ . '/../vues/v_listeRB.php';
			require_once __DIR__ . '/../vues/v_pied.php';
			$view = ob_get_clean ();
			return $view;
		} else {
			$app->redirect ( 'index.php' );
			require_once __DIR__ . '/../vues/v_entete.php';
			$app ['couteauSuisse']->ajouterErreur ( "Vous devez d'abord vous connecter pour accéder à cette page." );
			require_once __DIR__ . '/../vues/v_erreurs.php';
			require_once __DIR__ . '/../vues/v_connexion.php';
			require_once __DIR__ . '/../vues/v_pied.php';
			$view = ob_get_clean ();
			return $view;
		}
	}
	public function rembourse(Request $request, Application $app)
	{
		session_start ();
		if ($app ['couteauSuisse']->estConnecte ()) {
			$idVisiteur = $request->get('idVisiteur');
			$this->init ( $app );
			// récupération de toutes les fiches de frais
			$infoV = $this->pdo->getLesFichesFraisVA();
			$this->pdo->rembourse($idVisiteur);
			require_once __DIR__ . '/../vues/v_entete.php';
			require_once __DIR__ . '/../vues/v_sommaire.php';
			require_once __DIR__ . '/../vues/v_listemembre.php';
			require_once __DIR__ . '/../vues/v_pied.php';
			$view = ob_get_clean ();
			return $view;
		} else {
			$app->redirect ( 'index.php' );
			require_once __DIR__ . '/../vues/v_entete.php';
			$app ['couteauSuisse']->ajouterErreur ( "Vous devez d'abord vous connecter pour accéder à cette page." );
			require_once __DIR__ . '/../vues/v_erreurs.php';
			require_once __DIR__ . '/../vues/v_connexion.php';
			require_once __DIR__ . '/../vues/v_pied.php';
			$view = ob_get_clean ();
			return $view;
		}
	}
	public function ListeDesFichesFraisVA(Application $app) {
		session_start ();
		if ($app ['couteauSuisse']->estConnecte ()) {
			$this->init ( $app );
			// récupération de toutes les fiches de frais
			$infoV = $this->pdo->getLesFichesFraisVA();
			require_once __DIR__ . '/../vues/v_entete.php';
			require_once __DIR__ . '/../vues/v_sommaire.php';
			require_once __DIR__ . '/../vues/v_listemembre.php';
			require_once __DIR__ . '/../vues/v_pied.php';
			$view = ob_get_clean ();
			return $view;
		} else {
			$app->redirect ( 'index.php' );
			require_once __DIR__ . '/../vues/v_entete.php';
			$app ['couteauSuisse']->ajouterErreur ( "Vous devez d'abord vous connecter pour accéder à cette page." );
			require_once __DIR__ . '/../vues/v_erreurs.php';
			require_once __DIR__ . '/../vues/v_connexion.php';
			require_once __DIR__ . '/../vues/v_pied.php';
			$view = ob_get_clean ();
			return $view;
		}
	/*public function ListeDesMembres(Application $app) {
		session_start ();
		if ($app ['couteauSuisse']->estConnecte ()) {
			$this->init ( $app );
			// récupération de tous les visiteurs
			$infoV = $this->pdo->getToutesLesInfosDesVisiteurs ();
			require_once __DIR__ . '/../vues/v_entete.php';
			require_once __DIR__ . '/../vues/v_sommaire.php';
			require_once __DIR__ . '/../vues/v_listemembre.php';
			require_once __DIR__ . '/../vues/v_pied.php';
			$view = ob_get_clean ();
			return $view;
		} else {
			$app->redirect ( 'index.php' );
			require_once __DIR__ . '/../vues/v_entete.php';
			$app ['couteauSuisse']->ajouterErreur ( "Vous devez d'abord vous connecter pour accéder à cette page." );
			require_once __DIR__ . '/../vues/v_erreurs.php';
			require_once __DIR__ . '/../vues/v_connexion.php';
			require_once __DIR__ . '/../vues/v_pied.php';
			$view = ob_get_clean ();
			return $view;
		}
		*/
	}

	
	public function ajoutVisiteur(Application $app, Request $request) {
		session_start ();
		if ($app ['couteauSuisse']->estConnecte ()) {
			$this->init ( $app );
			require_once __DIR__ . '/../vues/v_entete.php';
			require_once __DIR__ . '/../vues/v_sommaire.php';
			// variable qui sert au formulaire
			$action = "ajout";
			$nom = $request->get ( 'nom' );
			$prenom = $request->get ( 'prenom' );
			$adresse = $request->get ( 'adresse' );
			$cp = $request->get ( 'cp' );
			$ville = $request->get ( 'ville' );
			$dateEmbauche = $request->get ( 'dateEmbauche' );
			// on met le nom et le prenom en minuscule pur le login
			$login = strtolower ( $nom [0] . $prenom );
			// on appelle la fonction random de CouteauSuisse pour nous permettre de donner des lettres et chiffres au hasard
			$id = strtolower ( $app ['couteauSuisse']->random ( 'char', 0, 1 ) . $app ['couteauSuisse']->random ( 'Num', 0, 3 ) );
			$password = $app ['couteauSuisse']->random ( 'charNum', 0, 5 );
			// on vérifie si le visiteur n'existe pas déjà dans la base de données grace au login au nom et au prénom
			$visiteurExistant = $this->pdo->getVisiteurExistant ( $login, $nom, $prenom );
			// on vérifie si l'ID du visiteur n'est pas déjà utilisé par un autre visiteur
			$visiteurExistantGraceALID = $this->pdo->getInfoVisiteurID ( $id );
			// tant que l'id est utilisé par un autre visiteur, on va devoir recréer une chaine de caractère pour l'ID
			// et re-vérifier à chaque fois si un visiteur existe ou non
			while ( $visiteurExistantGraceALID ) {
				$id = strtolower ( $app ['couteauSuisse']->random ( 'char', 0, 1 ) . $app ['couteauSuisse']->random ( 'Num', 0, 3 ) );
				$visiteurExistantGraceALID = $this->pdo->getInfoVisiteurID ( $id );
			}
			// si le visiteur existe alors un message d'erreur s'affiche
			if ($visiteurExistant) {
				$app ['couteauSuisse']->ajouterInformationUtilisateur ( "Ce visiteur est déjà présent dans la base de données." );
				require_once __DIR__ . '/../vues/v_information.php';
			} else {
				// si les champs ne sont pas vides alors on ajoute un nouveau visiteur
				if (! empty ( $nom ) && ! empty ( $prenom ) && ! empty ( $adresse ) && ! empty ( $cp ) && ! empty ( $ville ) && ! empty ( $dateEmbauche )) {
					// ces 6 lignes servent à empecher les failles XSS
					$nom = htmlentities ( $nom );
					$prenom = htmlentities ( $prenom );
					$adresse = htmlentities ( $adresse );
					$cp = htmlentities ( $cp );
					$ville = htmlentities ( $ville );
					$dateEmbauche = htmlentities ( $dateEmbauche );
					// fonction d'ajout du visiteur
					$visiteurAjouter = $this->pdo->ajoutVisiteur ( $nom, $prenom, $adresse, $cp, $ville, $dateEmbauche, $login, $password, $id );
					// si la fonction ajoutVisiteur retourne un résultat d'erreur alors on affiche qu'une erreur s'est produite
					if ($visiteurAjouter) {
						$app ['couteauSuisse']->ajouterInformationUtilisateur ( 'Une erreur s\'est produite lors de l\'ajout du visiteur' );
						require_once __DIR__ . '/../vues/v_information.php';
					} else {
						// sinon le visiteur a bien été ajouté
						$app ['couteauSuisse']->ajouterInformationUtilisateur ( 'Le visiteur a bien été ajouté !' );
						require_once __DIR__ . '/../vues/v_information.php';
					}
				}
				require_once __DIR__ . '/../vues/v_ajoutVisiteur.php';
			}
			require_once __DIR__ . '/../vues/v_pied.php';
			$view = ob_get_clean ();
			return $view;
		} else {
			$app->redirect ( 'index.php' );
			require_once __DIR__ . '/../vues/v_entete.php';
			$app ['couteauSuisse']->ajouterErreur ( "Vous devez d'abord vous connecter pour accéder à cette page." );
			require_once __DIR__ . '/../vues/v_erreurs.php';
			require_once __DIR__ . '/../vues/v_connexion.php';
			require_once __DIR__ . '/../vues/v_pied.php';
			$view = ob_get_clean ();
			return $view;
		}
	}
	public function modifierFichesFrais(Application $app, Request $request) {
		session_start ();
		if ($app ['couteauSuisse']->estConnecte ()) {
			$this->init ( $app );
			require_once __DIR__ . '/../vues/v_entete.php';
			require_once __DIR__ . '/../vues/v_sommaire.php';
			// variable qui sert au formulaire
			$action = "modification";
			// récupération de la variable envoyée par l'URL
			$idVisiteur = $request->get ( 'idVisiteur' );
			// on execute la requete qui permet de recuperer la fiche de frais grace à l'ID
			$infoV = $this->pdo->getLesFiches( $idVisiteur );
			$mois = $infoV ['mois'];
			$nbJustificatifs = $infoV ['nbJustificatifs'];
			$montantValide = $infoV ['montantValide'];
			$dateModif = $infoV ['dateModif'];
			$idEtat = $infoV ['idEtat'];
			require_once __DIR__ . '/../vues/v_ajoutVisiteur.php';
			require_once __DIR__ . '/../vues/v_pied.php';
			$view = ob_get_clean ();
			return $view;
		} else {
			$app->redirect ( 'index.php' );
			require_once __DIR__ . '/../vues/v_entete.php';
			$app ['couteauSuisse']->ajouterErreur ( "Vous devez d'abord vous connecter pour accéder à cette page." );
			require_once __DIR__ . '/../vues/v_erreurs.php';
			require_once __DIR__ . '/../vues/v_connexion.php';
			require_once __DIR__ . '/../vues/v_pied.php';
			$view = ob_get_clean ();
			return $view;
		}
	}
	
	/*public function modifierVisiteur(Application $app, Request $request) {
		session_start ();
		if ($app ['couteauSuisse']->estConnecte ()) {
			$this->init ( $app );
			require_once __DIR__ . '/../vues/v_entete.php';
			require_once __DIR__ . '/../vues/v_sommaire.php';
			// variable qui sert au formulaire
			$action = "modification";
			// récupération de la variable envoyée par l'URL
			$id = $request->get ( 'id' );
			// on execute la requete qui permet de recuperer le visiteur grace à l'ID
			$infoV = $this->pdo->getInfoVisiteurID ( $id );
			$nom = $infoV ['nom'];
			$prenom = $infoV ['prenom'];
			$adresse = $infoV ['adresse'];
			$cp = $infoV ['cp'];
			$ville = $infoV ['ville'];
			$dateEmbauche = $infoV ['dateEmbauche'];
			require_once __DIR__ . '/../vues/v_ajoutVisiteur.php';
			require_once __DIR__ . '/../vues/v_pied.php';
			$view = ob_get_clean ();
			return $view;
		} else {
			$app->redirect ( 'index.php' );
			require_once __DIR__ . '/../vues/v_entete.php';
			$app ['couteauSuisse']->ajouterErreur ( "Vous devez d'abord vous connecter pour accéder à cette page." );
			require_once __DIR__ . '/../vues/v_erreurs.php';
			require_once __DIR__ . '/../vues/v_connexion.php';
			require_once __DIR__ . '/../vues/v_pied.php';
			$view = ob_get_clean ();
			return $view;
		}
	}*/
	
	public function validerModifVisiteur(Application $app, Request $request) {
		session_start ();
		if ($app ['couteauSuisse']->estConnecte ()) {
			$this->init ( $app );
			require_once __DIR__ . '/../vues/v_entete.php';
			require_once __DIR__ . '/../vues/v_sommaire.php';
			// on recupère les variables qu'on a envoyé via le formulaire
			$id = htmlentities ( $request->get ( 'id' ) );
			$nom = htmlentities ( $request->get ( 'nom' ) );
			$prenom = htmlentities ( $request->get ( 'prenom' ) );
			$adresse = htmlentities ( $request->get ( 'adresse' ) );
			$cp = htmlentities ( $request->get ( 'cp' ) );
			$ville = htmlentities ( $request->get ( 'ville' ) );
			$dateEmbauche = htmlentities ( $request->get ( 'dateEmbauche' ) );
			// on recréé le login via le nouveau nom et prénom
			$login = strtolower ( $nom [0] . $prenom );
			// on modifie le visiteur
			$reponseRequete = $this->pdo->modifierVisiteur ( $nom, $prenom, $adresse, $cp, $ville, $dateEmbauche, $id );
			if ($reponseRequete) {
				$app ['couteauSuisse']->ajouterErreur ( "Erreur dans la modification du visiteur" );
				require_once __DIR__ . '/../vues/v_erreurs.php';
			} else {
				$app ['couteauSuisse']->ajouterInformationUtilisateur ( " Le visiteur a bien été modifé ! " );
				require_once __DIR__ . '/../vues/v_information.php';
			}			
			require_once __DIR__ . '/../vues/v_pied.php';
			$view = ob_get_clean ();
			return $view;
		} else {
			$app->redirect ( 'index.php' );
			require_once __DIR__ . '/../vues/v_entete.php';
			$app ['couteauSuisse']->ajouterErreur ( "Vous devez d'abord vous connecter pour accéder à cette page." );
			require_once __DIR__ . '/../vues/v_erreurs.php';
			require_once __DIR__ . '/../vues/v_connexion.php';
			require_once __DIR__ . '/../vues/v_pied.php';
			$view = ob_get_clean ();
			return $view;
		}
	}
	
}
?>

	