	<header class="row small-10 small-centered">
		<h2 class="small-8 columns">Nouvel auteur</h2>
		<a class="button warning right small-4 columns" href='/auteur'>Retour</a>
	</header>
	<form method="post" action="/auteur/creer">
		<div class="row collapse small-10 small-centered columns">
			<div class="small-3 columns">
				<label	class="prefix" title="Nom de l'auteur. (Entre 3 et 24 lettres et espaces)"
						for="auteur_nouveau_nom">Nom</label>
			</div>
			<div class="small-9 columns">	
				<input	type="text" title="Nom de l'auteur. (Entre 3 et 24 lettres et espaces)"
						id="auteur_nouveau_nom" 
						name="auteur_nouveau_nom" 
						placeholder="Entre 3 et 24 lettres et espaces." 
						maxlength="24" pattern="^[a-zA-Z][a-zA-Z ]*[a-zA-Z]$"
						required autofocus/>
			</div>
		</div>
		<div class="row collapse small-10 small-centered columns">
			<div class="small-3 columns">
				<label	class="prefix" title="Prénom de l'auteur. (Entre 3 et 24 lettres et espaces)" 
						for="auteur_nouveau_prenom">Prénom</label>
			</div>
			<div class="small-9 columns">	
				<input	type="text" title="Prénom de l'auteur. (Entre 3 et 24 lettres et espaces)"
						name="auteur_nouveau_prenom"
						id="auteur_nouveau_prenom" 
						placeholder="Entre 3 et 24 lettres et espaces."
						maxlength="24"  pattern="^[a-zA-Z][a-zA-Z ]*[a-zA-Z]$" 
						required />
			</div>
		</div>
		<div class="row small-10 small-centered">
    		<em>* Champs obligatoires</em>
    	</div>
		<div class="small-10 small-centered columns">	
			<input class="button expand radius" type="submit" value="Créer" />
		</div>
	</form>