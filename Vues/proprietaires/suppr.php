<?php
$O_proprietaire = $A_vue['proprietaire'];
?> <header class="row">
		<h2 class="small-8 columns">Suppression du proprietaire "<?php echo $O_proprietaire->donnePrenom().' '.$O_proprietaire->donneNom();?>"</h2>
		<a class="small-4 columns button warning right" href="/proprietaire">Retour</a>
	</header>
	
	<p class="text-center">Souhaitez-vous supprimer définitivement ce proprietaire ?</p>
	<div class="text-center">
		<a class="button warning" href="/proprietaire">Annuler</a>
		<a class="button" href="/proprietaire/suppr/<?php echo $O_proprietaire->donneIdentifiant();?>">Confirmer</a>
	</div>