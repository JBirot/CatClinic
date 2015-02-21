<?php
	$A_champs = array(
			array(0,''),
			array(0,''),
			array(0,'')
	);
	if(!empty($A_vue['ordre']))
	{
		$I_sens = $A_vue['ordre'][1]?0:1;
		$S_span = '<img class="right" src="/Ressources/Public/Images/miniarrow'.$I_sens.'.png"/>';
		$A_champs[$A_vue['ordre'][0]] = array($I_sens,$S_span);		
	}
?>
<header class="row">
	<h2 class="small-7 columns">Praticiens</h2>
	<a class="small-5 columns button right" href="/praticien/creation">Nouveau praticien</a>
</header>

<form class="row responsive" id="praticien" method="post" action="/praticien/validation">
	<table>
		<caption>Liste des praticiens enregistrés</caption>
		<thead>
			<tr>
				<th><a href="/praticien/changerOrdre/0/<?php echo $A_champs[0][0];?>">Id<?php echo $A_champs[0][1];?></a></th>
				<th><a href="/praticien/changerOrdre/1/<?php echo $A_champs[1][0];?>">Nom<?php echo $A_champs[1][1];?></a></th>
				<th><a href="/praticien/changerOrdre/2/<?php echo $A_champs[2][0];?>">Prenom<?php echo $A_champs[2][1];?></a></th>
				<th colspan="2">
		        	<div class="switch small radius right">
		        		<input type="checkbox" id="auteur_QuickMod" class="switchQuickMod"/>
		        		<label for="auteur_QuickMod"></label>
		        	</div>					
				</th>
			</tr>
		</thead>
		<?php 
		if (count($A_vue['praticiens']))
		{
			echo '<tbody>';
			
			foreach ($A_vue['praticiens'] as $O_praticien)
			{
				print '<tr>';
				
				echo 	'<td>'.$O_praticien->donneIdentifiant().'</td>'.
						'<td class="textToInput">'.$O_praticien->donneNom().'</td>'.
						'<td class="textToInput">'.$O_praticien->donnePrenom().'</td>';
				
				print 	'<td><a href="/praticien/suppression/'.$O_praticien->donneIdentifiant().'">Effacer</a></td>'.
						'<td class="linkToSubmit"><a href="/praticien/edit/'.$O_praticien->donneIdentifiant().'">Modifier</a></td>';
				
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
		$S_selected = BoiteAOutils::recupererDepuisSession('limite_praticiens') == $i ? 'selected' : '';
		$S_options .= '<option '.$S_selected.' value="'.$i.'">'.$i.'</option>';
	}
    echo '<form class="row collapse small-12 columns right" method="post" action="/praticien/changerLimite">'.
      			'<div class="small-5 columns"><label class="prefix" for="limite_praticiens_new">Par page</label></div>'.
        		'<div class="small-7 columns"><select id="limite_praticiens_new" name="limite_praticiens_new" onchange="this.parentNode.parentNode.submit()">'.
        			$S_options.
        		'</select></div>'.
         '</form>';
?>
</footer>