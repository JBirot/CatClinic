<?php
$O_categorie = $A_vue['categorie'];
?>

<section>
	<h1>Editer la catégorie "<?php echo $O_categorie->donneTitre(); ?>"</h1>
	<form 	action="/categorie/miseajour/<?php echo $O_categorie->donneIdentifiant(); ?>"
			name="categorie_edition"
			method="post">
			<label for="categorie_titre_<?php echo $O_categorie->donneIdentifiant(); ?>">Titre</label>
			<input 	type="text" name="categorie_titre_<?php echo $O_categorie->donneIdentifiant(); ?>" 
					value="<?php echo $O_categorie->donneTitre(); ?>" />
			<input type="submit" name="modifier" Value="Modifier" />
	</form>
</section>