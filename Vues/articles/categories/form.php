	<header class="row small-10 small-centered">
		<h2 class="small-8 columns">Nouvelle catégorie</h2>
		<a class="button warning right small-4 columns" href='/categorie'>Retour</a>
	</header>
	<form method="post" action="/categorie/creer">
		<div class="row collapse small-10 small-centered columns">
			<div class="small-3 columns">
				<label	class="prefix" title="Titre de la catégorie. (Jusqu'à 32 caractères)" 
						for="categorie_nouveau_titre">* Titre</label>
			</div>
			<div class="small-9 columns">
				<input	type="text" title="Titre de la catégorie. (Jusqu'à 32 caractères)"
						name="categorie_nouveau_titre"
						id="categorie_nouveau_titre" 
						maxlength="32" placeholder="Jusqu'à 32 caractères."
						required autofocus/>
			</div>
		</div>
		<div class="row small-10 small-centered">
    		<em>* Champs obligatoires</em>
    	</div>
		<div class="small-10 small-centered columns">
			<input class="button expand radius" type="submit" value="Créer" />
		</div>
	</form>