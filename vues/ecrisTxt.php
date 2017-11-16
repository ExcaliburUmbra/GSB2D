<?php
$connexion = mysqli_connect("localhost","root","","gsb2016");
$ecrire = fopen('C:\wamp64\www\GSB mission 2D_PHIM_Rothtana\vues\txtFicheFrais.txt',"w");
$sql = "SELECT * FROM fichefrais ORDER BY idVisiteur";
$result = mysqli_query($connexion,$sql);
if (!$result) {
    printf("Error: %s\n", mysqli_error($connexion));
    exit();
}
$data = "";
while($row = mysqli_fetch_array($result))
{
    $data = $row['idVisiteur'] . ';' . $row['mois'] . ';' . $row['nbJustificatifs']  . ';' . $row['montantValide'] . ';' . $row['dateModif'];
    $ecrire = fopen('C:\wamp64\www\GSB mission 2D_PHIM_Rothtana\vues\txtFicheFrais.txt',"a+");
    fputs($ecrire,$data);
    fputs($ecrire,"\n");
    echo $data;
}
header("Refresh:0;url=http://gsbd/public/ListePDF");
?>