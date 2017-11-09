<?php
/* Définition des routes */
$app->match ( '/', "ConnexionControleur::accueil" );
$app->match ( '/verifierUser', "ConnexionControleur::verifierUser" );
$app->match ( '/deconnecter', "ConnexionControleur::deconnecter" );

$app->match ( '/selectionnerMois', "EtatFraisControleur::selectionnerMois" );
$app->match ( '/voirFrais', "EtatFraisControleur::voirFrais" );

$app->match ( '/saisirFrais', "GestionFicheFraisControleur::saisirFrais" );
$app->match ( '/validerFrais', "GestionFicheFraisControleur::validerFrais" );

$app->match ( '/creationEtatMembre', "gestionGestionnaireControleur::creationEtatMembre" );
$app->match ( '/ajoutVisiteur', "gestionGestionnaireControleur::ajoutVisiteur" );
$app->match ( '/ListeDesFichesFraisVA', "gestionGestionnaireControleur::ListeDesFichesFraisVA" );
$app->match ( '/ListeDesFichesFraisRB', "gestionGestionnaireControleur::ListeDesFichesFraisRB" );
$app->match ( '/rembourse', "gestionGestionnaireControleur::rembourse" );
$app->match ( '/modifierFichesFrais', "gestionGestionnaireControleur::modifierFichesFrais" );
$app->match ( '/validerModifVisiteur', "gestionGestionnaireControleur::validerModifVisiteur" );
?>