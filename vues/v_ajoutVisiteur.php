<div id="contenu">
	<form method="post" action="">
            <?php
			if ($action == "modification") {
			?> 
            <input type="hidden" name="id" value="<?php echo $id;?> " />
            <?php
			}
			?>

		<fieldset>
			<legend> Formulaire <?php echo $action?> frais</legend>
			<table class="listLegere" style="margin: 0 auto;">
				<tr>
					<td><label for="idVisiteur">Identifiant du visiteur</label></td>
					<td><input type="text" required="required" id="idVisiteur" name="idVisiteur"
						value="<?php echo $idVisiteur; ?>" /></td>
				</tr>

				<tr>
					<td><label for="mois">Mois</label></td>
					<td><input type="text" required="required" id="mois"
						name="mois" value="<?php echo $mois; ?>" /></td>
				<tr>
				
				<tr>
					<td><label for="nbJustificatifs">Nombre de justificatifs</label></td>
					<td><input type="text" required="required" id="nbJustificatifs"
						name="nbJustificatifs" value="<?php echo $nbJustificatifs; ?>" /></td>
				</tr>

				<tr>
					<td><label for="montantValide">Montant Validé</label></td>
					<td><input type="text" required="required" id="montantValide" size="5"
						name="montantValide" min="5" max="5" value="<?php echo $montantValide; ?>" /></td>
				</tr>

				<tr>
					<td><label for="dateModif">Date de la modification</label></td>
					<td><input type="text" required="required" id="dateModif" name="dateModif"
						value="<?php echo $dateModif; ?>" /></td>
				</tr>

				<tr>
					<td><label for="idEtat">Etat</label></td>
					<td><input type="date" required="required" id="idEtat"
						name="idEtat" value="<?php echo $idEtat; ?>" /></td>
				</tr>

			</table>
			<div align="center">
			<?php 
			if($action == "ajout"){
			?>
			<input type="submit" value="Créer visiteur" />
			<?php 
			}
			if ($action == "modification") {
			?>
            	<input type="submit" formaction="validerModifVisiteur" value="Modifier frais"/>
            <?php
			}
			?>
			</div>
		</fieldset>
	</form>
</div>