<?php
$O_visite = $A_vue['visite'];
?> <header class="row">
		<h2 class="small-8 columns">Suppression de la visite n°<?php echo $O_visite->donneIdentifiant();?></h2>
		<a class="small-4 columns button warning right" href="/visite">Retour</a>
	</header>
	
	<p class="text-center">Souhaitez-vous supprimer définitivement cette visite ?</p>
	<div class="text-center">
		<a class="button warning" href="/visite">Annuler</a>
		<a class="button" href="/visite/suppr/<?php echo $O_visite->donneIdentifiant();?>">Confirmer</a>
	</div>