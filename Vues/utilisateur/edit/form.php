<?php
    $O_utilisateur = $A_vue['utilisateur'];
?>	<header class="row small-10 small-centered">
		<h2 class="small-8 columns">Editer l'utilisateur "<?php echo $O_utilisateur->donneLogin(); ?>"</h2>
		<a class="button warning right small-4 columns" href="/utilisateur">Retour</a>
	</header>
	<form 	name="utilisateur"
		    id="utilisateur"
	    	method="post"
	     	action="/utilisateur/miseajour/<?php echo $O_utilisateur->donneIdentifiant(); ?>">
		<div class="row collapse small-10 small-centered">
			<div class="small-3 columns">
				<label	class="prefix" title="Login de l'utilisateur. (Entre 4 et 24 chiffres et lettres)"
						for="login" >* Login</label>
			</div>
			<div class="small-9 columns">
	             <input type="text" title="Login de l'utilisateur. (Entre 4 et 24 chiffres et lettres)"
	             		name="login"
	             		id="login"
	                    maxlength="24" pattern="^([a-zA-Z0-9]{4,24})$" 
	                    placeholder="Entre 4 et 24 chiffres et lettres."
	                    value="<?php echo $O_utilisateur->donneLogin(); ?>"
	                    required autofocus />
	        </div>
	    </div>
	    <div class="row small-10 small-centered">
	    	<em>* Champs obligatoires</em>
	    </div>
	    <div class="row small-10 small-centered">
	        <input class="button expand" type="submit" name="valid" id="valid" value="Mettre à jour" title="Cliquez sur ce bouton pour valider votre inscription" tabindex="9" />
	    </div>
	</form>