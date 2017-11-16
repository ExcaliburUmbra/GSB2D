<?php
require('vues/FPDF/fpdf.php');
ob_end_clean();
class PDF extends FPDF
{
// Chargement des données
function LoadData($file)
{
    // Lecture des lignes du fichier
    $lines = file($file);
    $data = array();
    foreach($lines as $line)
        $data[] = explode(';',trim($line));
    return $data;
}

// Tableau amélioré
function ImprovedTable($header, $data)
{
    // Largeurs des colonnes
    $w = array(55, 55, 55, 55, 55);
    // En-tête
    for($i=0;$i<count($header);$i++)
        $this->Cell($w[$i],7,$header[$i],1,0,'C');
    $this->Ln();
    // Données
    foreach($data as $row)
    {
        $this->Cell($w[0],6,$row[0],'LR');
        $this->Cell($w[1],6,$row[1],'LR');
        $this->Cell($w[2],6,$row[2],'LR');
        $this->Cell($w[3],6,$row[3],'LR');
        $this->Cell($w[4],6,$row[4],'LR');
        $this->Ln();
    }
    // Trait de terminaison
    $this->Cell(array_sum($w),0,'','T');
}
}
$pdf = new PDF();
// Titres des colonnes
$header = array('ID', 'Mois', 'Nb justificatif', 'MontantValide', 'DateModif');
// Chargement des données
$data = $pdf->LoadData('vues\txtFicheFrais.txt');
$pdf->SetFont('Arial','',14);
$pdf->AddPage('L');
$pdf->ImprovedTable($header,$data);
//$pdf->AddPage('L');
$pdf->Output();
?>