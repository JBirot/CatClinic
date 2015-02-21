	<header class="row small-10 medium-8 large-6 small-centered push-top">
    	<h1 class="small-12 text-center">Cat Clinic</h1>
    	<div class="small-12 small-centered">
	    	<h2 class="small-6 columns button-fake">Connexion</h2>
	    	<a href="/Inscription" class="small-6 columns button radius">Inscription</a>
		</div>
	</header>	
	
    <?php
        // si une erreur s'est produite à la soumission du formulaire, elle remonte ici
        echo '<div class="row small-10 medium-8 large-6 small-centered">';
    			
    	Vue::montrer('standard/erreurs');
    	
    	echo '</div>';

        // l'authentification de l'utilisateur a pu échouer et néanmoins positionner l'identifiant de l'utilisateur
        // il ira dans l'attribut "value" de notre zone de texte
        $S_identifiant = BoiteAOutils::recupererDepuisSession('login');
    ?>
    
    <form action="/login/validation" method="post" class="row small-10 medium-8 large-6 small-centered">
        <div class="row collapse">
        	<div class="small-3 columns">
        		<label	class="prefix" title="Votre login. (Entre 4 et 24 chiffres et lettres)" 
        				for="identifiant">Login</label>
        	</div>
        	<div class="small-9 columns">
        		<input	type="text" title="Votre login. (Entre 4 et 24 chiffres et lettres)"
        				name="identifiant" 
        				id="identifiant" 
        				maxlength="24" pattern="^([a-zA-Z0-9]{4,24})$"
        				placeholder="Entre 4 et 24 chiffres et lettres."
        				value="<?php echo $S_identifiant; ?>" 
        				required autofocus/>
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
        <input id="submit" type="submit" value="Se Connecter" class="button expand radius center" />
    </form>