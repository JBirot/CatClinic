	<header class="row small-10 small-centered">
		<h2 class="small-8 columns">Nouvel utilisateur</h2>
		<a class="button warning right small-4 columns" href='/utilisateur'>Retour</a>
	</header>
	<form  action="/utilisateur/creer" method="post">		
		<div class="row collapse small-10 small-centered">
			<div class="small-3 columns">
				<label	class="prefix" title="Login de l'utilisateur. (Entre 4 et 24 chiffres et lettres)"
						for="identifiant">Login</label>
			</div>
			<div class="small-9 columns">
				<input	type="text" title="Login de l'utilisateur. (Entre 4 et 24 chiffres et lettres)"
						id="identifiant" 
						name="identifiant" 
						maxlength="24" pattern="^([a-zA-Z0-9]{4,24})$" 
						placeholder="Entre 4 et 24 chiffres et lettres." 
						required autofocus/>
			</div>
		</div>
		<div class="row collapse small-10 small-centered">
			<div class="small-3 columns">
				<label	class="prefix" title="Mot de passe de l'utilisateur. (Entre 6 et 24 caractères)"
						for="motdepasse">Mot de passe</label>
			</div>
			<div class="small-9 columns">
				<input	type="password" title="Mot de passe de l'utilisateur. (Entre 6 et 24 caractères)"
						id="motdepasse" 
						name="mot_de_passe" 
						maxlength="24" pattern=".{6,24}"
						placeholder="Entre 6 et 24 caractères." 
						required/>
			</div>
		</div>
		<div class="row small-10 small-centered">
			<input class="button expand" type="submit" value="Créer" name="creer" />
		</div>
	</form>