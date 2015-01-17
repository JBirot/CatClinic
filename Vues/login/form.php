<section id="loginbox">
    <h1 class="text-center">Cat Clinic</h1>
    
	<dl class="tabs center" role="navigation">
	    <dd class="active"><a href="">Connexion</a></dd>
	    <dd><a href="/Inscription">Inscription</a></dd>
	</dl>	
	
    <?php
        // si une erreur s'est produite à la soumission du formulaire, elle remonte ici
        Vue::montrer('standard/erreurs');

        // l'authentification de l'utilisateur a pu échouer et néanmoins positionner l'identifiant de l'utilisateur
        // il ira dans l'attribut "value" de notre zone de texte
        $S_identifiant = BoiteAOutils::recupererDepuisSession('login');
    ?>
    
    <form action="/login/validation" method="post">
        <div class="row collapse">
        	<div class="small-3 large-2 columns">
        		<label class="prefix" for="login">Identifiant</label>
        	</div>
        	<div class="small-9 large-10 columns">
        		<input type="text" name="login" id="login" value="<?php echo $S_identifiant; ?>" />
        	</div>
        </div>
        <div class="row collapse">
        	<div class="small-3 large-2 columns">
        		<label class="prefix" for="motdepasse">Mot de passe</label>
        	</div>
        	<div class="small-9 large-10 columns">
        		<input type="password" name="motdepasse" id="motdepasse" />
        	</div>
        </div>
        <input id="submit" type="submit" value="Se Connecter" class="button round center" />
    </form> 
</section>