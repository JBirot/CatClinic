	<header class="row small-10 small-centered">
		<h2 class="small-8 columns">Nouveau proprietaire</h2>
		<a class="button warning right small-4 columns" href="/proprietaire">Retour</a>
	</header>
	<form method="post" action="/proprietaire/creer">
		<div class="row collapse small-10 small-centered columns">
			<div class="small-3 columns">
				<label class="prefix" title="Nom du proprietaire. (Entre 3 et 24 lettres et espaces)"
					for="proprietaire_nouveau_nom">* Nom</label>
			</div>
			<div class="small-9 columns">
				<input type="text" title="Nom du proprietaire (Entre 3 et 24 lettres et espaces)"
					name="proprietaire_nouveau_nom"
					id="proprietaire_nouveau_nom"
					placeholder="Entre 3 et 24 lettres et espaces"
					maxlength="24" pattern="^[a-zA-Z][a-zA-Z ]*[a-zA-Z]$"
					required autofocus />
			</div>
		</div>
		<div class="row collapse small-10 small-centered columns">
			<div class="small-3 columns">
				<label class="prefix" title="Prenom du proprietaire. (Entre 3 et 24 lettres et espaces)"
					for="proprietaire_nouveau_prenom">* Prenom</label>
			</div>
			<div class="small-9 columns">
				<input type="text" title="Prenom du proprietaire (Entre 3 et 24 lettres et espaces)"
					name="proprietaire_nouveau_prenom"
					id="proprietaire_nouveau_prenom"
					placeholder="Entre 3 et 24 lettres et espaces"
					maxlength="24" pattern="^[a-zA-Z][a-zA-Z ]*[a-zA-Z]$"
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