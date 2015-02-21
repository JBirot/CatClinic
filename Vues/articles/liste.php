<?php 
	$A_champs = array(	array(0,''),
						array(0,''),
						array(0,''),
						array(0,''),
						array(0,''),
						array(0,'')	);
	if(!empty($A_vue['ordre']))
	{
		$I_sens = $A_vue['ordre'][1]?0:1;
		$S_span = '<img class="right" src="/Ressources/Public/Images/miniarrow'.$I_sens.'.png"/>';
		$A_champs[$A_vue['ordre'][0]] = array($I_sens,$S_span);
	}
?>
<a class="button small-12" href="/article/page/1">Voir les articles >></a>
<header class="row">
	<h2 class="small-7 columns">Articles</h2>
	<a class="small-5 columns button right" href="/article/creation">Nouvel article</a>
</header>


<form id="article" class="row responsive" method="post" action="/article/validation" >
	<table>
		<caption>Liste des articles</caption>
		<thead>
			<tr>
				<th><a href="/article/changerOrdre/0/<?php echo $A_champs[0][0];?>">Id<?php echo $A_champs[0][1];?></a></th>
				<th><a href="/article/changerOrdre/1/<?php echo $A_champs[1][0];?>">Titre<?php echo $A_champs[1][1];?></a></th>
				<th><a href="/article/changerOrdre/2/<?php echo $A_champs[2][0];?>">Categorie<?php echo $A_champs[2][1];?></a></th>
				<th><a href="/article/changerOrdre/3/<?php echo $A_champs[3][0];?>">Auteur<?php echo $A_champs[3][1];?></a></th>
				<th><a href="/article/changerOrdre/4/<?php echo $A_champs[4][0];?>">En ligne<?php echo $A_champs[4][1];?></a></th>
				<th><a href="/article/changerOrdre/5/<?php echo $A_champs[5][0];?>">Date<?php echo $A_champs[5][1];?></a></th>
				<th colspan="2">
		        	<div class="switch small radius right">
		        		<input type="checkbox" id="article_QuickMod" class="switchQuickMod"/>
		        		<label for="article_QuickMod"></label>
		        	</div>
		        </th>
			</tr>
		</thead>
		<?php 
		if(count($A_vue['articles']))
		{
			echo '<tbody>';
		
			foreach ($A_vue['articles'] as $O_article)
			{
				print '<tr>';
				
				echo 	'<td>'. $O_article->donneIdentifiant() . '</td><td class="textToInput">'.
								$O_article->donneTitre() . '</td><td>' .
								$O_article->donneCategorieTitre() . '</td><td>' .
								$O_article->donneAuteurNom() . " " . $O_article->donneAuteurPrenom() . '</td><td class="textToCheckbox">' .
			 					($O_article->estEnLigne() ? 'Oui' : 'Non').'</td><td>' .
			 					$O_article->donneDate() . '</td>';
				
				print '<td><a href="/article/suppression/' . $O_article->donneIdentifiant() .
				'">Effacer</a></td>';
				echo '<td class="linkToSubmit"><a href="/article/edit/' . $O_article->donneIdentifiant() . '">Modifier</a></td>';
				
				print '</tr>';						
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
		$S_selected = BoiteAOutils::recupererDepuisSession('limite_articles') == $i ? 'selected' : '';
		$S_options .= '<option '.$S_selected.' value="'.$i.'">'.$i.'</option>';
	}
    echo '<form class="row collapse small-12 columns right" method="post" action="/article/changerLimite">'.
      			'<div class="small-5 columns"><label class="prefix" for="limite_articles_new">Par page</label></div>'.
        		'<div class="small-7 columns"><select id="limite_articles_new" name="limite_articles_new" onchange="this.parentNode.parentNode.submit()">'.
        			$S_options.
        		'</select></div>'.
         '</form>';
?>
</footer>