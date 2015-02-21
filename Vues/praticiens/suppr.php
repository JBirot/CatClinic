<?php
$O_praticien = $A_vue['praticien'];
?> <header class="row">
		<h2 class="small-8 columns">Suppression du praticien "<?php echo $O_praticien->donnePrenom().' '.$O_praticien->donneNom();?>"</h2>
		<a class="small-4 columns button warning right" href="/praticien">Retour</a>
	</header>
	
	<p class="text-center">Souhaitez-vous supprimer définitivement ce praticien ?</p>
	<div class="text-center">
		<a class="button warning" href="/praticien">Annuler</a>
		<a class="button" href="/praticien/suppr/<?php echo $O_praticien->donneIdentifiant();?>">Confirmer</a>
	</div>