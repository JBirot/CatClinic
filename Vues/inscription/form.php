
	<header class="row small-10 medium-8 large-6 small-centered push-top">
		<h1 class="small-12 text-center">Cat Clinic</h1>
		<div class="small-12 small-centered">
			<a href="login" class="small-6 columns button radius">Connexion</a>
			<h2 class="small-6 columns button-fake">Inscription</h2>
		</div>
	</header>
	<?php
	// si une erreur s'est produite à la soumission du formulaire, elle remonte ici
        echo '<div class="row small-10 medium-8 large-6 small-centered">';
    			
    	Vue::montrer('standard/erreurs');
    	
    	echo '</div>';
	?>
	
    <form action="/utilisateur/creer" method="post" class="row small-10 medium-8 large-6 small-centered">
        <div class="row collapse">
        	<div class="small-3 columns">
        		<label	for="identifiant" title="Votre login. (Entre 4 et 24 chiffres et lettres)"
        				class="prefix">Login</label>
        	</div>
        	<div class="small-9 columns">	
        		<input	type="text" title="Votre login. (Entre 4 et 24 chiffres et lettres)"
        				name="identifiant" 
        				id="identifiant" 
        				maxlength="24" pattern="^([a-zA-Z0-9]{4,24})$" 
						placeholder="Entre 4 et 24 chiffres et lettres." 
        				required autofocus />
       		</div>
        </div>
        <div class="row collapse">
        	<div class="small-3 columns">
        		<label	for="mot_de_passe" title="Votre mot de passe. (Entre 6 et 24 caractères)"
        				class="prefix">Mot de passe</label>
        	</div>
        	<div class="small-9 columns">
        		<input	type="password" title="Votre mot de passe. (Entre 6 et 24 caractères)"
        				name="mot_de_passe" 
        				id="mot_de_passe"
        				maxlength="24" pattern=".{6,24}"
        				placeholder="Entre 6 et 24 caractères."
        				required />
        	</div>
        </div>
        <input id="submit" type="submit" value="S'inscrire" class="button expand radius center" />
    </form>