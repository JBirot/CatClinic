<?php
$O_auteur = $A_vue['auteur'];
?>	<header class="row small-10 small-centered">
		<h2 class="small-8 columns">Editer l'auteur "<?php echo $O_auteur->donnePrenom().' '.$O_auteur->donneNom(); ?>"</h2>
		<a class="button warning right small-4 columns" href='/auteur'>Retour</a>
	</header>
	<form 	action="/auteur/miseajour/<?php echo $O_auteur->donneIdentifiant(); ?>"
			name="auteur_edition"
			method="post">
		<div class="row collapse small-10 small-centered">
			<div class="small-3 columns">
				<label	class="prefix" title="Nom de l'auteur. (Entre 4 et 24 lettres et espaces)"
						for="auteur_nom_<?php echo $O_auteur->donneIdentifiant(); ?>">* Nom</label>
			</div>
			<div class="small-9 columns">
				<input 	type="text" title="Nom de l'auteur. (Entre 3 et 24 lettres et espaces)"
						name="auteur_nom_<?php echo $O_auteur->donneIdentifiant(); ?>"
						id="auteur_nom_<?php echo $O_auteur->donneIdentifiant(); ?>"
						placeholder="Entre 3 et 24 lettres et espaces." 
						maxlength="24" pattern="^[a-zA-Z][a-zA-Z ]*[a-zA-Z]$"
						value="<?php echo $O_auteur->donneNom(); ?>" 
						required autofocus/>
			</div>
		</div>
		<div class="row collapse small-10 small-centered">
			<div class="small-3 columns">
				<label 	class="prefix" title="Prénom de l'auteur. (Entre 3 et 24 lettres et espaces)"
						for="auteur_prenom_<?php echo $O_auteur->donneIdentifiant(); ?>">* Prénom</label>
			</div>
			<div class="small-9 columns"> 
				<input 	type="text" title="Prénom de l'auteur. (Entre 3 et 24 lettres et espaces)"
						name="auteur_prenom_<?php echo $O_auteur->donneIdentifiant() ; ?>"
						id="auteur_prenom_<?php echo $O_auteur->donneIdentifiant() ; ?>"
						placeholder="Entre 3 et 24 lettres et espaces." 
						maxlength="24" pattern="^[a-zA-Z][a-zA-Z ]*[a-zA-Z]$"
						value="<?php echo $O_auteur->donnePrenom();?>" 
						required />
			</div>
		</div>
		<div class="row small-10 small-centered">
    		<em>* Champs obligatoires</em>
    	</div>
		<div class="row small-10 small-centered">
			<input class="button expand" type="submit" name="modifier" Value="Modifier" />
		</div>
	</form>