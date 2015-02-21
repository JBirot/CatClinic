<?php 
$S_optionCategorie = "";
if (count($A_vue['categories']))
{
	foreach ($A_vue['categories'] as $O_categorie)
	{
		$S_optionCategorie .=  '<option value="' . $O_categorie->donneIdentifiant() . '" >' . $O_categorie->donneTitre() . '</option>'; 
	}			
}
$S_optionAuteur = "";
if (count($A_vue['auteurs']))
{
	foreach ($A_vue['auteurs'] as $O_auteur)
	{
		$S_optionAuteur .= '<option value="' . $O_auteur->donneIdentifiant() . '" >' . $O_auteur->donneNom() . " " . $O_auteur->donnePrenom() . "</option>";
	}
}
?>	<header class="row small-10 small-centered">
		<h2 class="small-8 columns">Nouvel article</h2>
		<a class="button warning right small-4 columns" href='/article'>Retour</a>
	</header>
	<form method="post" action="/article/creer">
		<div class="row collapse small-10 small-centered columns">
			<div class="small-3 columns">
				<label 	class="prefix" title="Titre de l'article. (Jusqu'à 32 caractères)"
						for="article_nouveau_titre">* Titre</label>
			</div>
			<div class="small-9 columns">
				<input 	type="text" title="Titre de l'article. (Jusqu'à 32 caractères)"
						name="article_nouveau_titre"
						id="article_nouveau_titre" 
						maxlength="32"
						placeholder="Jusqu'à 32 caractères." 
						required autofocus />
			</div>
		</div>
		<div class="row collapse small-10 small-centered columns">
			<div class="small-3 columns">
				<label 	class="prefix" title="Veuillez choisir une catégorie."
						for="article_nouveau_categorie">* Catégorie</label>
			</div>
			<div class="small-9 columns">
				<select	id="article_nouveau_categorie"
						name="article_nouveau_categorie"
						title="Veuillez choisir une catégorie.">
					<?php echo $S_optionCategorie ; ?>
				</select>
			</div>
		</div>
		<div class="row collapse small-10 small-centered columns">
			<div class="small-3 columns">
				<label 	class="prefix" title="Veuillez choisir un auteur"
						for="article_nouveau_auteur">* Auteur</label>
			</div>
			<div class="small-9 columns">
				<select id="article_nouveau_auteur"
						name="article_nouveau_auteur"
						title="Veuillez choisir un auteur">
					<?php  echo $S_optionAuteur ; ?>
				</select>
			</div>
		</div>
		
		<label 	class="row small-10 text-center" title="Veuillez remplir le contenu." 
				for="article_nouveau_contenu">* Contenu</label>
		
		<textarea rows="12" id="article_nouveau_contenu"
				name="article_nouveau_contenu"></textarea>
		<div class="row small-10 small-centered">
    		<em>* Champs obligatoires</em>
    	</div>		
		<div class="small-10 small-centered columns">
			<input class="button expand radius" type="submit" value="Créer" />
		</div>
	</form>