<?php
$O_auteur = $A_vue['auteur'];
$A_auteurs = $A_vue['auteurs'];
$A_articles = $A_vue['articles'];

$S_options = '';
if(count($A_auteurs)>1)
{
	foreach ($A_auteurs as $O_auteur2)
	{
		if($O_auteur2->donneIdentifiant()!=$O_auteur->donneIdentifiant())
		{
			$S_options .= '<option value="'.$O_auteur2->donneIdentifiant().'">'.$O_auteur2->donnePrenom().' '.$O_auteur2->donneNom().'</option>';
		}
	}
}

?>	<header class="row">
		<h2 class="small-8 columns">Suppression de l'auteur "<?php echo $O_auteur->donnePrenom().' '.$O_auteur->donneNom();?>"</h2>
		<a class="button warning right small-4 columns" href="/auteur">Retour</a>
	</header>
	
	<?php if(!empty($A_articles))
	{ 	
		foreach ($A_articles as $O_article){$A_articlesTitres[] = $O_article->donneTitre();}
		echo	'<p>Les articles suivant sont affectés à cet auteur : <ul><li>'.implode("</li><li>",$A_articlesTitres).'</li></ul></p>';
		if(count($A_auteurs)>1)
		{
			echo	'<p>Vous pouvez selectionner un nouvel auteur pour ces articles ou les supprimer définitivement : </p>';
			echo	'<form 	method="post"'.
					'		action="/auteur/suppr/'.$O_auteur->donneIdentifiant().'">';
			echo 	'	<div class="row collapse">'.
					'		<div class="small-4 columns">'.
					'			<label for="auteur_remplacement" title="Auteur de remplacement pour les articles affectés" class="prefix">Nouvel auteur</label>'.
					'		</div>'.
					'		<div class="small-8 columns">'.
					'			<select title="Auteur de remplacement pour les articles affectés" id="auteur_remplacement" name="auteur_remplacement">'.
					'				'.$S_options.
					'			</select>'.
					'		</div>'.
					'	</div>';
			echo 	'	<div class="text-center">'.
					'		<a class="button warning" href="/auteur">Annuler</a>'.
					'		<input class="button" type="submit" value="Remplacer" />'.
					'		<input class="button" type="submit" value="Supprimer" />'.
					'	</div>';
			echo 	'</form>';
		}else{
			echo 	'<p class="panel callout text-center">La suppression de cet auteur entrainera la suppression de ces articles !</p>';
		}
	}
	if (empty($A_articles)||count($A_auteurs)<=1)
	{
		echo	'<p class="text-center">Souhaitez-vous supprimer définitivement cet auteur ?</p>';
		echo	'<div class="text-center">'.
				'	<a class="button warning" href="/auteur">Annuler</a>'.
				'	<a class="button" href="/auteur/suppr/'.$O_auteur->donneIdentifiant().'">Confirmer</a>'.
				'</div>';
	}
	?>

