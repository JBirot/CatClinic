<?php

if (Authentification::estConnecte())
{
    $A_liens = array();
    
    $A_liens['Articles']['/article'] = 'Articles';
    
    if(Authentification::estProprietaire()){
    	$A_liens['Visites']['/visite'] = 'Visites';
    }
    
    if (Authentification::estAdministrateur()) {
    	$A_liens['Visites']['/visite'] = 'Visites';
    	$A_liens['Chats']['/chat'] = 'Chats';
        $A_liens['Utilisateurs']['/utilisateur'] = 'Utilisateurs';
        $A_liens['Proprietaires']['/proprietaire'] = 'Proprietaires';
        $A_liens['Praticiens']['/praticien'] = 'Praticiens';
        $A_liens['Articles']['/categorie'] = 'Catégories';
        $A_liens['Articles']['/auteur'] = 'Auteurs';
    }

?>  <a href="/" class="show-for-large-up"><img src="/Ressources/Public/images/chat3.jpg" class="center" alt="logo"/></a>
	<div  class="contain-to-grid sticky">
		<nav class="top-bar" data-topbar role="navigation">
			<!-- TITRE -->
			<ul class="title-area">
				<li class="name">
					<h1 class="hide-for-large-up"><a href="/">CatClinic</a></h1>
				</li>
				<li class="toggle-topbar menu-icon"><a href="#"><span>Menu</span></a></li>
			</ul>
			
			<div class="top-bar-section">
				
				<!-- LEFT -->
				<ul class="left">		
		<?php 
		foreach ($A_liens as $S_categorie => $A_lien)
		{
			if(count($A_lien)>1)
			{	
				echo '<li class="has-dropdown">';
				echo '	<a href="'.key($A_lien).'">'.$A_lien[key($A_lien)].'</a>';
	        	echo '	<ul class="dropdown">';
	        	array_shift($A_lien);
	        	foreach ($A_lien as $S_lien => $S_nom)
	        	{
	        		echo '	<li><a href="'.$S_lien.'">'.$S_nom.'</a></li>';	
	        	}
	        	echo '	</ul>';
	        	echo '</li>';
			}
			else
			{	//Un seul lien pour cette catégorie
				echo '<li><a href="'.key($A_lien).'">'.$A_lien[key($A_lien)].'</a></li>';	
			}
		}
		?>
				</ul>
				
				<!-- RIGHT -->
				<ul class="right">
					<li class="divider"></li>
		    		<li><a href="/logout"><span class="hidden-for-medium-only">Vous êtes connecté(e) en tant que <strong> <?php echo BoiteAOutils::recupererDepuisSession('utilisateur')->donneLogin() ; ?>. </strong></span>Déconnexion. </a></li>
				</ul>
			</div>
		</nav>
	</div>

<?php 
}