<section id="loginbox">
    <h1>Cat Clinic - Console</h1>
    <?php
        // si une erreur s'est produite à la soumission du formulaire, elle remonte ici
        Vue::montrer('standard/erreurs');

        // l'authentification de l'utilisateur a pu échouer et néanmoins positionner l'identifiant de l'utilisateur
        // il ira dans l'attribut "value" de notre zone de texte
        $S_identifiant = BoiteAOutils::recupererDepuisSession('login');
    ?>
    <form action="/login/validation" method="post">
        <div class="row"><label for="login">Identifiant:</label><input type="text" name="login" id="login" value="<?php echo $S_identifiant; ?>" /></div>
        <div class="row"><label for="motdepasse">Mot de passe:</label><input type="password" name="motdepasse" id="motdepasse" /></div>
        <div class="row"><label for="submit"> </label><input id="submit" type="submit" value="Connexion" class="submitbutton" /></div>
    </form>
    <p><a href="inscription">Inscription</a></p>
</section>