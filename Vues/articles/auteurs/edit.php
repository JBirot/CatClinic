<?php
$O_auteur = $A_vue['auteur'];
?>

<section>
	<h1>Editer la catégorie "<?php echo $O_auteur->donneTitre(); ?>"</h1>
	<form 	action="/auteur/miseajour/<?php echo $O_auteur->donneIdentifiant(); ?>"
			name="auteur_edition"
			method="post">
			<label for="auteur_nom_<?php echo $O_auteur->donneIdentifiant(); ?>">Nom</label>
			<input 	type="text" name="auteur_nom_<?php echo $O_auteur->donneIdentifiant(); ?>" 
					value="<?php echo $O_auteur->donneNom(); ?>" />
			<label for="auteur_prenom_<?php echo $O_auteur->donneIdentifiant(); ?>">Prénom</label> 
			<input type="text" name="auteur_prenom_<?php echo $O_auteur->donneIdentifiant() ; ?>"
					value="<?php $O_auteur->donnePrenom();?>" />
			<input type="submit" name="modifier" Value="Modifier" />
	</form>
</section>
