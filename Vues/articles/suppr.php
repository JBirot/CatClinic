<?php
$O_article = $A_vue['article'];
?> <header class="row">
		<h2 class="small-8 columns">Suppression de l'article "<?php echo $O_article->donneTitre();?>"</h2>
		<a class="small-4 columns button warning right" href="/article">Retour</a>
	</header>
	
	<p class="text-center">Souhaitez-vous supprimer définitivement cet article ?</p>
	<div class="text-center">
		<a class="button warning" href="/article">Annuler</a>
		<a class="button" href="/article/suppr/<?php echo $O_article->donneIdentifiant();?>">Confirmer</a>
	</div>