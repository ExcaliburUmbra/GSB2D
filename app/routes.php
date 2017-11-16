<?php
/* Définition des routes */
$app->match ( '/', "ConnexionControleur::accueil" );
$app->match ( '/verifierUser', "ConnexionControleur::verifierUser" );
$app->match ( '/deconnecter', "ConnexionControleur::deconnecter" );
$app->match ( '/ListeDesFichesFraisVA', "gestionGestionnaireControleur::ListeDesFichesFraisVA" );
$app->match ( '/ListeDesFichesFraisRB', "gestionGestionnaireControleur::ListeDesFichesFraisRB" );
$app->match ( '/rembourse', "gestionGestionnaireControleur::rembourse" );
$app->match ( '/PDF', "gestionGestionnaireControleur::PDF" );
$app->match ( '/ListePDF', "gestionGestionnaireControleur::ListePDF");
?>