<div id="contenu">
	<h2>Liste des frais </h2>
	<form method="get" action="ListeDesFichesFraisVA">
	<table class="listeLegere"
		style="margin: 0 auto;">
		<tr>
			<th><center>idVisiteur</center></th>
			<th>mois</th>
			<th>nbJustificatifs</th>
			<th>montantValide</th>
			<th>dateModif</th>
			<th>idEtat</th>
		</tr>
		
		<?php
			foreach ( $infoV as $ficheFrais ) {
			?>
		<tr>
			<td> <?php echo $ficheFrais ['idVisiteur']?></td>
			<td> <?php echo $ficheFrais ['mois']?></td>
			<td> <?php echo $ficheFrais ['nbJustificatifs']?></td>
			<td> <?php echo $ficheFrais ['montantValide']?></td>
			<td> <?php echo $ficheFrais ['dateModif']?></td>
			<td> <?php echo $ficheFrais ['idEtat']?></td>
		</tr>
		<?php
		}
		?>	
	</table>
	</form>
	<br />
	<h2 style="text-align: center;">
		<a href="ajoutVisiteur" title="Ajouter un nouveau membre">
			Ajouter un nouveau membre</a>=
	</h2>
	<h2 style="text-align: center;">
		<a href="creationEtatMembre" title="Générer un état des membres"> Etat
			des membres</a>
	</h2>
</div>