<?php
$O_proprietaire = $A_vue['proprietaire'];
?>	<header class="row small-10 small-centered">
		<h2 class="small-8 columns">Editer le proprietaire "<?php echo $O_proprietaire->donnePrenom().' '.$O_proprietaire->donneNom();?>"</h2>
		<a class="button warning right small-4 columns" href="/proprietaire">Retour</a>
	</header>
	
	<form action="/proprietaire/miseajour/<?php echo $O_proprietaire->donneIdentifiant();?>"
			name="proprietaire_edition"
			method="post">
		<div class="row collapse small-10 small-centered">
			<div class="small-3 columns">
				<label class="prefix" title="Nom du proprietaire. (Entre 3 et 24 lettres et espaces)"
					for="proprietaire_nom_<?php echo $O_proprietaire->donneIdentifiant();?>">* Nom</label>
			</div>
			<div class="small-9 columns">
				<input type="text" title="Nom du proprietaire. (Entre 3 et 24 lettres et espaces)"
					name="proprietaire_nom_<?php echo $O_proprietaire->donneIdentifiant()?>"
					id="proprietaire_nom_<?php echo $O_proprietaire->donneIdentifiant()?>"
					placeholder="Entre 3 et 24 lettres et espaces"
					maxlength="24" pattern="^[a-zA-Z][a-zA-Z ]*[a-zA-Z]$"
					value="<?php echo $O_proprietaire->donneNom();?>"
					required autofocus />
			</div>
		</div>
		<div class="row collapse small-10 small-centered">
			<div class="small-3 columns">
				<label class="prefix" title="Prenom du proprietaire. (Entre 3 et 24 lettres et espaces)"
					for="proprietaire_prenom_<?php echo $O_proprietaire->donneIdentifiant();?>">* Nom</label>
			</div>
			<div class="small-9 columns">
				<input type="text" title="Prenom du proprietaire. (Entre 3 et 24 lettres et espaces)"
					name="proprietaire_prenom_<?php echo $O_proprietaire->donneIdentifiant()?>"
					id="proprietaire_prenom_<?php echo $O_proprietaire->donneIdentifiant()?>"
					placeholder="Entre 3 et 24 lettres et espaces"
					maxlength="24" pattern="^[a-zA-Z][a-zA-Z ]*[a-zA-Z]$"
					value="<?php echo $O_proprietaire->donnePrenom();?>"
					required/>
			</div>
		</div>
		<div class="row small-10 small-centered">
    		<em>* Champs obligatoires</em>
    	</div>
		<div class="row small-10 small-centered">
			<input class="button expand" type="submit" name="modifier" Value="Modifier" />
		</div>
	</form>