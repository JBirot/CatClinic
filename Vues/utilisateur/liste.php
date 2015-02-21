<?php 
	$A_champs = array(	array(0,''),
						array(0,''),
						array(0,''));
	if(!empty($A_vue['ordre']))
	{
		$I_sens = $A_vue['ordre'][1]?0:1;
		$S_span = '<img class="right" src="/Ressources/Public/Images/miniarrow'.$I_sens.'.png"/>';
		$A_champs[$A_vue['ordre'][0]] = array($I_sens,$S_span);
	}
?>
<header class="row">
	<h2 class="small-7 columns">Utilisateurs</h2>
	<a class="small-5 columns button right" href="/utilisateur/creation">Nouvel utilisateur</a>
</header>

<form id="utilisateur" class="row responsive" method="post" action="/utilisateur/validation">
	<table>
		<caption>Liste des utilisateurs actifs du site</caption>
		<thead>
		    <tr>
		        <th><a href="/utilisateur/changerOrdre/0/<?php echo $A_champs[0][0] ;?>">Id<?php echo $A_champs[0][1];?></a></th>
		        <th><a href="/utilisateur/changerOrdre/1/<?php echo $A_champs[1][0];?>">Login<?php echo $A_champs[1][1];?></a></th>
		        <th><a href="/utilisateur/changerOrdre/2/<?php echo $A_champs[2][0];?>">Admin<?php echo $A_champs[2][1];?></a></th>
		        <th colspan="2">
		        	<div class="switch small radius right">
		        		<input type="checkbox" id="utilisateur_QuickMod" class="switchQuickMod"/>
		        		<label for="utilisateur_QuickMod"></label>
		        	</div>
		        </th>
		    </tr>
		</thead>
		<?php
		
		if (count($A_vue['utilisateurs']))
		{
			$O_utilisateurCourant = BoiteAOutils::recupererDepuisSession('utilisateur');
			
		    echo '<tbody>';
		
		    foreach ($A_vue['utilisateurs'] as $O_utilisateur)
		    {
		    	$S_classe = $O_utilisateur->donneLogin() === $O_utilisateurCourant->donneLogin() ? "class='current'" : "";
		    	print '<tr '.$S_classe.'>';
		    	
		    	echo '<td>'. $O_utilisateur->donneIdentifiant() . '</td><td class="textToInput">' .
		    			$O_utilisateur->donneLogin() . '</td><td>' .
		    			($O_utilisateur->estAdministrateur() ? 'oui' : 'non') . '</td>';
		    	
		        // Allez, on ressort echo, print...
		
		        if ($O_utilisateur->donneLogin() != $O_utilisateurCourant->donneLogin()) {
		            // On ne peut pas s'auto-supprimer ni même se modifier alors qu'on est connecté !
		            print '<td><a href="/utilisateur/suppression/' . $O_utilisateur->donneIdentifiant() .
		                '">Effacer</a></td>';
		            echo '<td class="linkToSubmit"><a href="/utilisateur/edit/' . $O_utilisateur->donneIdentifiant() . '">Modifier</a></td>';
		        }
		        else{
		        	echo '<td colspan="2"></td>';
		        }
		
		        echo '</tr>';
		    }
		
		    echo '</tbody>';
		}
		?>
	</table>
</form>

<footer class="row">
<?php
    if (isset($A_vue['pagination']))
    {
        echo '<div class="pagination-centered small-12 columns">'.
        		'<ul class="pagination">';
        foreach ($A_vue['pagination'] as $I_numeroPage => $S_lien)
        {
            echo '<li '.($S_lien ? '' : 'class="current"').'><a href="' . ($S_lien ? '/'.$S_lien : "#") . '">' . $I_numeroPage . '</a></li>';
        }
        echo 	'</ul>';
        echo '</div>';
    }
	$S_options ='';
	for($i=5;$i<=30;$i += 5)
	{
		$S_selected = BoiteAOutils::recupererDepuisSession('limite_utilisateurs') == $i ? 'selected' : '';
		$S_options .= '<option '.$S_selected.' value="'.$i.'">'.$i.'</option>';
	}
    echo '<form class="row collapse small-12 columns right" method="post" action="/utilisateur/changerLimite">'.
      			'<div class="small-5 columns"><label class="prefix" for="limite_utilisateurs_new">Par page</label></div>'.
        		'<div class="small-7 columns"><select id="limite_utilisateurs_new" name="limite_utilisateurs_new" onchange="this.parentNode.parentNode.submit()">'.
        			$S_options.
        		'</select></div>'.
         '</form>';
?>
</footer>