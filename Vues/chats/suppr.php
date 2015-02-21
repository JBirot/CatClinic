<?php
$O_chat = $A_vue['chat'];
?> <header class="row">
		<h2 class="small-8 columns">Suppression du chat "<?php echo $O_chat->donneNom();?>"</h2>
		<a class="small-4 columns button warning right" href="/chat">Retour</a>
	</header>
	
	<p class="text-center">Souhaitez-vous supprimer définitivement ce chat ?</p>
	<div class="text-center">
		<a class="button warning" href="/chat">Annuler</a>
		<a class="button" href="/chat/suppr/<?php echo $O_chat->donneIdentifiant();?>">Confirmer</a>
	</div>