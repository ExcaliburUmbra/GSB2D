<div class="contenu">
	<div class="erreur" align="center">
	<?php
		foreach ( $_REQUEST ['erreurs'] as $erreur ) {
			echo $erreur;
		}
	?>
	</div>
</div>