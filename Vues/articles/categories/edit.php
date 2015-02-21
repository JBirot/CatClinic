<?php
$O_categorie = $A_vue['categorie'];
?>	<header class="row small-10 small-centered">
		<h2 class="small-8 columns">Editer la catégorie "<?php echo $O_categorie->donneTitre(); ?>"</h2>
		<a class="button warning right small-4 columns" href='/categorie'>Retour</a>
	</header>
	<form 	action="/categorie/miseajour/<?php echo $O_categorie->donneIdentifiant(); ?>"
			name="categorie_edition"
			method="post">
		<div class="row collapse small-10 small-centered">
			<div class="small-3 columns">
				<label	class="prefix" title="Titre de la catégorie. (Jusqu'à 32 caractères)"
						for="categorie_titre_<?php echo $O_categorie->donneIdentifiant(); ?>">* Titre</label>
			</div>
			<div class="small-9 columns">
				<input 	type="text" title="Titre de la catégorie. (Jusqu'à 32 caractères)"
						name="categorie_titre_<?php echo $O_categorie->donneIdentifiant(); ?>" 
						id="categorie_titre_<?php echo $O_categorie->donneIdentifiant(); ?>"
						placeholder="Jusqu'à 32 caractères." maxlength="32"
						value="<?php echo $O_categorie->donneTitre(); ?>"
						required autofocus />
			</div>
		</div>
		<div class="row small-10 small-centered">
    		<em>* Champs obligatoires</em>
    	</div>
		<div class="row small-10 small-centered">
			<input class="button expand" type="submit" name="modifier" Value="Modifier" />
		</div>
	</form>
