	<header class="row small-10 small-centered">
		<h2 class="small-8 columns">Nouveau chat</h2>
		<a class="button warning right small-4 columns" href='/chat'>Retour</a>
	</header>
	<form method='post' action='/chat/creer'>
		<div class="row collapse small-10 small-centered columns">
			<div class="small-3 columns">
				<label class="prefix" title="Nom du chat. (Entre 3 et 24 lettres et espaces)"
				for="chat_nouveau_nom">* Nom</label>
			</div>
			<div class="small-9 columns">
				<input	type="text" title="Nom du chat. (Entre 3 et 24 lettres et espaces)"
						id="chat_nouveau_nom" 
						name="chat_nouveau_nom" 
						placeholder="Entre 3 et 24 lettres et espaces." 
						maxlength="24" pattern="^[a-zA-Z][a-zA-Z ]*[a-zA-Z]$"
						required autofocus/>
			</div>
		</div>
		<div class="row collapse small-10 small-centered columns">
			<div class="small-3 columns">
				<label class="prefix" title="Date de naissance du chat."
					for="chat_nouveau_age">* Naissance</label>
			</div>
			<div class="small-9 columns">
				<input type="date" title="Date de naissance du chat."
					id="chat_nouveau_age"
					name="chat_nouveau_age"
					placeholder="aaaa-mm-jj"
					maxlength="10" pattern="^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$"
					required />
			</div>
		</div>
		<div class="row collapse small-10 small-centered columns">
			<div class="small-3 columns">	
				<label class="prefix" title="Tatouage du chat."
					for="chat_nouveau_tatouage">* Tatouage</label>
			</div>
			<div class="small-9 columns">
				<input type="text" title="Tatouage du chat"
					name="chat_nouveau_tatouage"
					id="chat_nouveau_tatouage"
					placeholder="Jusqu'à 10 lettres et chiffres"
					maxlength="10" pattern="^[0-9a-Z]{1,10}$"/>
			</div>
		</div>	
		<div class="row small-10 small-centered">
    		<em>* Champs obligatoires</em>
    	</div>
		<div class="small-10 small-centered columns">	
			<input class="button expand radius" type="submit" value="Créer" />
		</div>
	</form>
