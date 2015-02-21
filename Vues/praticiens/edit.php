<?php
$O_praticien = $A_vue['praticien'];
?>	<header class="row small-10 small-centered">
		<h2 class="small-8 columns">Editer le praticien "<?php echo $O_praticien->donnePrenom().' '.$O_praticien->donneNom();?>"</h2>
		<a class="button warning right small-4 columns" href="/praticien">Retour</a>
	</header>
	
	<form action="/praticien/miseajour/<?php echo $O_praticien->donneIdentifiant();?>"
			name="praticien_edition"
			method="post">
		<div class="row collapse small-10 small-centered">
			<div class="small-3 columns">
				<label class="prefix" title="Nom du praticien. (Entre 3 et 24 lettres et espaces)"
					for="praticien_nom_<?php echo $O_praticien->donneIdentifiant();?>">* Nom</label>
			</div>
			<div class="small-9 columns">
				<input type="text" title="Nom du praticien. (Entre 3 et 24 lettres et espaces)"
					name="praticien_nom_<?php echo $O_praticien->donneIdentifiant()?>"
					id="praticien_nom_<?php echo $O_praticien->donneIdentifiant()?>"
					placeholder="Entre 3 et 24 lettres et espaces"
					maxlength="24" pattern="^[a-zA-Z][a-zA-Z ]*[a-zA-Z]$"
					value="<?php echo $O_praticien->donneNom();?>"
					required autofocus />
			</div>
		</div>
		<div class="row collapse small-10 small-centered">
			<div class="small-3 columns">
				<label class="prefix" title="Prenom du praticien. (Entre 3 et 24 lettres et espaces)"
					for="praticien_prenom_<?php echo $O_praticien->donneIdentifiant();?>">* Nom</label>
			</div>
			<div class="small-9 columns">
				<input type="text" title="Prenom du praticien. (Entre 3 et 24 lettres et espaces)"
					name="praticien_prenom_<?php echo $O_praticien->donneIdentifiant()?>"
					id="praticien_prenom_<?php echo $O_praticien->donneIdentifiant()?>"
					placeholder="Entre 3 et 24 lettres et espaces"
					maxlength="24" pattern="^[a-zA-Z][a-zA-Z ]*[a-zA-Z]$"
					value="<?php echo $O_praticien->donnePrenom();?>"
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