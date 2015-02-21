<?php
$S_optionCategorie = "";
if (count($A_vue['categories']))
{
	foreach ($A_vue['categories'] as $O_categorie)
	{
		$S_select= $O_categorie->donneIdentifiant() == $A_vue['article']->donneCategorieId() ? 'select' : '';
		$S_optionCategorie .=  '<option value="' . $O_categorie->donneIdentifiant() . '"'.$S_select.' >' . $O_categorie->donneTitre() . '</option>';
	}
}
$S_optionAuteur = "";
if (count($A_vue['auteurs']))
{
	foreach ($A_vue['auteurs'] as $O_auteur)
	{
		$S_select= $O_auteur->donneIdentifiant() == $A_vue['article']->donneAuteurId() ? 'select' : '';
		$S_optionAuteur .= '<option value="' . $O_auteur->donneIdentifiant() . '" '.$S_select.' >' . $O_auteur->donneNom() . " " . $O_auteur->donnePrenom() . "</option>";
	}
}
?>	<header class="row small-10 small-centered">
		<h2 class="small-8 columns">Modifier l'article "<?php echo $A_vue['article']->donneTitre();?>"</h2>
		<a class="button warning right small-4 columns" href='/article'>Retour</a>
	</header>
	<form 	action="/article/miseajour/<?php echo $A_vue['article']->donneIdentifiant();?>"
			method="post">
		<div class="row collapse small-10 small-centered">
			<div class="small-3 columns">
				<label 	class="prefix" title="Titre de l'article. (Jusqu'à 32 caractères)"
						for="article_titre_<?php echo $A_vue['article']->donneIdentifiant();?>">* Titre</label>
			</div>
			<div class="small-9 columns">
				<input 	type="text" title="Titre de l'article. (Jusqu'à 32 caractères)"
						name="article_titre_<?php echo $A_vue['article']->donneIdentifiant();?>"
						id="article_titre_<?php echo $A_vue['article']->donneIdentifiant();?>"
						maxlength="32" 
						placeholder="Jusqu'à 32 caractères."
						value="<?php echo $A_vue['article']->donneTitre(); ?>"
						required autofocus />
			</div>
		</div>
		<div class="row collapse small-10 small-centered">
			<div class="small-3 columns">
				<label	class="prefix" title="Veuillez choisir une catégorie."
						for="article_categorie_<?php echo $A_vue['article']->donneIdentifiant();?>">* Catégorie</label>
			</div>
			<div class="small-9 columns">	
				<select id="article_categorie_<?php echo $A_vue['article']->donneIdentifiant();?>" 
						name="article_categorie_<?php echo $A_vue['article']->donneIdentifiant();?>"
						title="Veuillez choisir une catégorie.">
					<?php echo $S_optionCategorie ; ?>
				</select>
			</div>
		</div>
		<div class="row collapse small-10 small-centered">
			<div class="small-3 columns">
				<label	class="prefix" title="Veuillez choisir un auteur."
						for="article_auteur_<?php echo  $A_vue['article']->donneIdentifiant();?>">* Auteur</label>
			</div>
			<div class="small-9 columns">	
				<select id="article_auteur_<?php echo  $A_vue['article']->donneIdentifiant();?>"
						name="article_auteur_<?php echo  $A_vue['article']->donneIdentifiant();?>"
						title="Veuillez choisir un auteur.">
					<?php echo $S_optionAuteur ; ?>
				</select>
			</div>
		</div>
		<label	class="row text-center" title="Veuillez remplir le contenu."
				for="article_contenu_<?php echo $A_vue['article']->donneIdentifiant();?>">* Contenu</label>
		<textarea rows="12" id= "article_contenu_<?php echo  $A_vue['article']->donneIdentifiant();?>"
				name="article_contenu_<?php echo  $A_vue['article']->donneIdentifiant();?>"
				title="Veuillez remplir le contenu." 
				required><?php echo $A_vue['article']->donneContenu();?></textarea>
		<div class="row small-10 small-centered">
    		<em>* Champs obligatoires</em>
    	</div>
		<div class="row small-10 small-centered">
			<input class="button expand" type="submit" name="modifier" Value="Modifier" />
		</div>		
	</form>