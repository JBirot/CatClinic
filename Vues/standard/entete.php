<?php

if (Authentification::estConnecte())
{
    $A_liens = array();
    
    if (Authentification::estAdministrateur()) {
        $A_liens['Utilisateurs']['/utilisateur/liste'] = 'Utilisateurs';
        $A_liens['Articles']['/article/liste'] = 'Articles';
        $A_liens['Articles']['/categorie/liste'] = 'Catégories';
        $A_liens['Articles']['/auteur/liste'] = 'Auteurs';
    }

?>   
	<nav class="top-bar" data-topbar role="navigation">
		<!-- TITRE -->
		<ul class="title-area">
			<li class="name">
				<h1><a href="/">CatClinic</a></h1>
			</li>
			<li class="toggle-topbar menu-icon"><a href="#"><span>Menu</span></a></li>
		</ul>
		
		<section class="top-bar-section">
			
			<!-- LEFT -->
			<ul class="left">		
	<?php 
	foreach ($A_liens as $S_categorie => $A_lien)
	{
		if(count($A_lien)>1)
		{
			echo '<li class="has-dropdown">';
			echo '	<a href="#">'.$S_categorie.'</a>';
        	echo '	<ul class="dropdown">';
        	foreach ($A_lien as $S_lien => $S_nom)
        	{
        		echo '	<li><a href="'.$S_lien.'">'.$S_nom.'</a></li>';	
        	}
        	echo '	</ul>';
        	echo '</li>';
		}
		else
		{
			foreach ($A_lien as $S_lien => $S_nom)
			{
				echo '<li><a href="'.$S_lien.'">'.$S_nom.'</a></li>';
			}
		}
	}
	?>
			</ul>
			
			<!-- RIGHT -->
			<ul class="right">
				<li class="divider"></li>
	    		<li><a href="/logout">Vous êtes connecté(e) en tant que <strong> <?php echo BoiteAOutils::recupererDepuisSession('utilisateur')->donneLogin() ; ?>. </strong>Déconnexion. </a></li>
			</ul>
		</section>
	</nav>

<?php 
}