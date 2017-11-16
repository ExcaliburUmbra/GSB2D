<?php
class PDF extends FPDF {
	// En-t�te
	function Header() {
		//Logo
		$this->Image(__DIR__.'/../public/images/logo.jpg',10,6,30);
		//C:\Users\Can Serkan\Documents\UwAmp\www\workspace\Projet_GSB\public\images\logo.jpg
		// Police Arial gras 15
		$this->SetFont ( 'Arial', 'B', 15 );
		// D�calage � droite
		$this->Cell ( 65 );
		// Titre
		$this->Cell ( 60, 10, 'Etat des membres', 1, 0, 'C' );
		// Saut de ligne
		$this->Ln ( 20 );
	}
	
	// Pied de page
	function Footer() {
		// Positionnement � 1,5 cm du bas
		$this->SetY ( - 15 );
		// Police Arial italique 8
		$this->SetFont ( 'Arial', 'I', 8 );
		// Num�ro de page
		$this->MultiCell ( 0, 0, 'Page ' . $this->PageNo () . '/' . $this->AliasNbPages, 0, 'C' );
		$this->MultiCell (0, 0, 'Edit� le : ' . date("Y-m-d"), 0, 'R');
	}
}