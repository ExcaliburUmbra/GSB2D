    <!-- Division pour le sommaire -->
    <div id="menuGauche">
     <div id="infosUtil">
       </div>  
        <ul id="menuList">
			<li >
			<?php 
			if($_SESSION['categorie'] == 'visiteur')
			{
			?>
				  Visiteur :<br>
			<?php 
			}
			else 
			{
			?>
				Comptable :<br/>
			<?php 
			}
			?>
				<?php echo $_SESSION['prenom']."  ".$_SESSION['nom'] ?>
			</li>
			<?php if($_SESSION['categorie'] == "visiteur")
			{
			?>
	           <li class="smenu">
	           <a href="saisirFrais" title="Saisie fiche de frais ">Saisie fiche de frais</a>
	           </li>
	           <li class="smenu">
	           <a href="selectionnerMois" title="Consultation de mes fiches de frais">Mes fiches de frais</a>
	           </li>
           <?php 
			}
			else 
			{
			?>
				<li class="smenu">
				<a href="ListeDesFichesFraisVA" title="Consulter les visiteurs ">Liste des fiches de frais VA</a>
				<a href="ListeDesFichesFraisRB" title="Consulter les visiteurs ">Liste des fiches de frais RB</a>
				</li>
			<?php
			}
			?>
 	   	   <li class="smenu">
           <a href="deconnecter" title="Se déconnecter">Déconnexion</a>
           </li>
         </ul>
    </div>
    