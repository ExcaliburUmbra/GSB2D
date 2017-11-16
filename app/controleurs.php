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
			$this->init ( $app );
			$idVisiteur = $request->get('idVisiteur');
			$mois = $request->get('mois');
			// récupération de toutes les fiches de frais
			$infoV = $this->pdo->getLesFichesFraisVA();
			$this->pdo->rembourse($idVisiteur,$mois);
			require_once __DIR__ . '/../vues/v_entete.php';
			require_once __DIR__ . '/../vues/v_sommaire.php';
			require_once __DIR__ . '/../vues/v_listeFrais.php';
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
			require_once __DIR__ . '/../vues/v_listeFrais.php';
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
	public function PDF(Application $app)
	{
		session_start();
		if($app['couteauSuisse']->estConnecte())
		{
			$this->init($app);
			require_once __DIR__.'/../vues/txtFicheFrais.txt';
			$view = ob_get_clean();
			return $view;
		}
		else
		{
			return Response::HTTP_NOT_FOUND;
		}
	}
	public function ListePDF(Application $app)
	{
		session_start();
		if($app['couteauSuisse']->estConnecte())
		{
			$this->init($app);
			require_once __DIR__.'/../vues/FPDF/fpdf.php';
			require_once __DIR__.'/../vues/v_pdf.php';
			$view = ob_get_clean();
			return $view;
		}
		else
		{
			return Response::HTTP_NOT_FOUND;
		}
	}
}
?>

	