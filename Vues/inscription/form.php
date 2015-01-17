<section id="inscriptionbox">
<h1>Cat Clinic - Inscription</h1>
<?php
// si une erreur s'est produite à la soumission du formulaire, elle remonte ici
Vue::montrer('standard/erreurs');
?>
    <form action="/utilisateur/creer" method="post">
        <div class="row"><label for="login">Identifiant:</label><input type="text" name="login" id="login" value="" /></div>
        <div class="row"><label for="motdepasse">Mot de passe:</label><input type="password" name="motdepasse" id="motdepasse" /></div>
        <div class="row"><label for="submit"> </label><input id="submit" type="submit" value="Inscription" class="submitbutton" /></div>
    </form>
    <p><a href="login">Connexion</a></p>
</section>