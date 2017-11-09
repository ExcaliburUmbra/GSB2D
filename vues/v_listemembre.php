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
			<td> <input type="checkbox" name="RB[]" value="<?php echo $ficheFrais ['idVisiteur'] ?>"></td>
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
<script>
// allocate the function to the window scrolling
   window.onscroll = fixedTop;
   
   var startingY = false;

   function fixedTop() {
       
       // First top value recovery
       if (!startingY) startingY = parseInt(document.getElementById("fixedtop").style.top);
       
       // Scroll top value
       if (window.pageYOffset) {        
           var yrt = window.pageYOffset;
       } else if (document.body.scrollTop){ 
           var yrt = document.body.scrollTop;
       } else { 
           var yrt = document.documentElement.scrollTop;
       }
       
       document.getElementById("fixedtop").style.top = (yrt + startingY)+ "px";
   }
</script>
<div id="fixedtop" style="position:absolute; left: 1150px; top: 300px; width: 100px; height: 100px; background: #77AADD"> 
<input type="button" value="Rembourser" onclick="window.location.href='rembourse?idVisiteur=<?php echo $ficheFrais['idVisiteur']; ?>'" />
</div>