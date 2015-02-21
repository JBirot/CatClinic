<?php 
	$A_champs = array(	array(0,''),
						array(0,''));
	if(!empty($A_vue['ordre']))
	{
		$I_sens = $A_vue['ordre'][1]?0:1;
		$S_span = '<img class="right" src="/Ressources/Public/Images/miniarrow'.$I_sens.'.png"/>';
		$A_champs[$A_vue['ordre'][0]] = array($I_sens,$S_span);
	}
?><header class="row">
	<h2 class="small-7 columns">Catégories</h2>
	<a class="small-5 columns button right" href="/categorie/creation">Nouvelle catégorie</a>
</header>

<form id='categorie' class="row responsive" method="post" action="/categorie/validation">
	<table>
	<caption>Liste des catégories d'article</caption>
	<thead>
	    <tr>
	        <th><a href="/categorie/changerOrdre/0/<?php echo $A_champs[0][0];?>">Id<?php echo $A_champs[0][1];?></a></th>
	        <th><a href="/categorie/changerOrdre/1/<?php echo $A_champs[1][0]?>">Titre<?php echo $A_champs[1][1];?></a></th>
	        <th colspan="2"><div class="switch small radius right"><input type="checkbox" id="categorie_QuickMod" class="switchQuickMod"/><label for="categorie_QuickMod"></label></div></th>
	    </tr>
	</thead>
	<?php
	
	if (count($A_vue['categories']))
	{
	    echo '<tbody>';
	
	    foreach ($A_vue['categories'] as $O_categorie)
	    {
	        // Allez, on ressort echo, print...
	        print '<tr>';
		        echo '<td>'. $O_categorie->donneIdentifiant() . '</td><td class="textToInput">' . 
		                     $O_categorie->donneTitre() . '</td>';
			
		        print '<td><a href="/categorie/suppression/' . $O_categorie->donneIdentifiant() .'">Effacer</a></td>';
		        echo '<td class="linkToSubmit"><a href="/categorie/edit/' . $O_categorie->donneIdentifiant() . '">Modifier</a></td>';
	        
	
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
		$S_selected = BoiteAOutils::recupererDepuisSession('limite_categories') == $i ? 'selected' : '';
		$S_options .= '<option '.$S_selected.' value="'.$i.'">'.$i.'</option>';
	}
    echo '<form class="row collapse small-12 columns right" method="post" action="/categorie/changerLimite">'.
      			'<div class="small-5 columns"><label class="prefix" for="limite_categories_new">Par page</label></div>'.
        		'<div class="small-7 columns"><select id="limite_categories_new" name="limite_categories_new" onchange="this.parentNode.parentNode.submit()">'.
        			$S_options.
        		'</select></div>'.
         '</form>';
?>
</footer>