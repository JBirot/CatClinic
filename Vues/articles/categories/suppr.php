<?php 
	$O_categorie = $A_vue['categorie'];
	$A_categories = $A_vue['categories'];
	$A_articles = $A_vue['articles'];
	
	$S_options = '';
	if(count($A_categories)>1)
	{
		foreach ($A_categories as $O_categorie2)
		{
			if($O_categorie2->donneIdentifiant()!=$O_categorie->donneIdentifiant())
			{
				$S_options .= '<option value="'.$O_categorie2->donneIdentifiant().'">'.$O_categorie2->donneTitre().'</option>';
			}
		}
	}
	
?>	<header class="row">
		<h2 class="small-8 columns">Suppression de la categorie "<?php echo $O_categorie->donneTitre();?>"</h2>
		<a class="button warning right small-4 columns" href="/categorie">Retour</a>
	</header>
	
	<?php if(!empty($A_articles))
	{ 	
		foreach ($A_articles as $O_article){$A_articlesTitres[] = $O_article->donneTitre();}
		echo	'<p>Les articles suivant sont affectés à cette catégorie : <ul><li>'.implode("</li><li>",$A_articlesTitres).'</li></ul></p>';
		if(count($A_categories)>1)
		{
			echo	'<p>Vous pouvez selectionner une nouvelle catégorie pour ces articles ou les supprimer définitivement : </p>';
			echo	'<form 	method="post"'.
					'		action="/categorie/suppr/'.$O_categorie->donneIdentifiant().'">';
			echo 	'	<div class="row collapse">'.
					'		<div class="small-4 columns">'.
					'			<label for="categorie_remplacement" title="Catégorie de remplacement pour les articles affectés" class="prefix">Nouvelle catégorie</label>'.
					'		</div>'.
					'		<div class="small-8 columns">'.
					'			<select title="Catégorie de remplacement pour les articles affectés" id="categorie_remplacement" name="categorie_remplacement">'.
					'				'.$S_options.
					'			</select>'.
					'		</div>'.
					'	</div>';
			echo 	'	<div class="text-center">'.
					'		<a class="button warning" href="/categorie">Annuler</a>'.
					'		<input class="button" type="submit" value="Remplacer" />'.
					'		<input class="button" type="submit" value="Supprimer" />'.
					'	</div>';
			echo 	'</form>';
		}else{
			echo 	'<p class="panel callout text-center">La suppression de cette catégorie entrainera la suppression de ces articles !</p>';
		}
	}
	if (empty($A_articles)||count($A_categories)<=1)
	{
		echo	'<p class="text-center">Souhaitez-vous supprimer définitivement cette catégorie ?</p>';
		echo	'<div class="text-center">'.
				'	<a class="button warning" href="/categorie">Annuler</a>'.
				'	<a class="button" href="/categorie/suppr/'.$O_categorie->donneIdentifiant().'">Confirmer</a>'.
				'</div>';
	}
	?>

