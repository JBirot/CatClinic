<?php
$O_utilisateur = $A_vue['utilisateur'];
?> <header class="row">
		<h2 class="small-8 columns">Suppression de l'utilisateur "<?php echo $O_utilisateur->donneLogin();?>"</h2>
		<a class="small-4 columns button warning right" href="/utilisateur">Retour</a>
	</header>
	
	<p class="text-center">Souhaitez-vous supprimer définitivement cet utilisateur ?</p>
	<div class="text-center">
		<a class="button warning" href="/utilisateur">Annuler</a>
		<a class="button" href="/utilisateur/suppr/<?php echo $O_utilisateur->donneIdentifiant();?>">Confirmer</a>
	</div>