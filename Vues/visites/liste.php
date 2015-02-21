<?php 
	$A_champs = array(	array(0,''),
						array(0,''),
						array(0,''),
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
	<h2 class="small-7 columns">Visites</h2>
	<a class="small-5 columns button right" href="/visite/creation">Nouvelle visite</a>
</header>

<form id="visite" class="row responsive" method="post" action="/visite/validation">
	<table>
		<caption>Liste des visites</caption>
		<thead>
			<tr>
				<th><a href="/visite/changerOrdre/0/<?php echo $A_champs[0][0]; ?>">Id<?php echo $A_champs[0][1];?></a></th>
				<th><a href="/visite/changerOrdre/1/<?php echo $A_champs[1][0]; ?>">Praticien<?php echo $A_champs[1][1];?></a></th>
				<th><a href="/visite/changerOrdre/2/<?php echo $A_champs[2][0]; ?>">Chat<?php echo $A_champs[2][1];?></a></th>
				<th><a href="/visite/changerOrdre/3/<?php echo $A_champs[3][0]; ?>">Date<?php echo $A_champs[3][1];?></a></th>
				<th><a href="/visite/changerOrdre/4/<?php echo $A_champs[4][0]; ?>">Prix<?php echo $A_champs[4][1];?></a></th>
				<th>Observations</th>
				<th colspan="2">
		        	<!-- <div class="switch small radius right">
		        		<input type="checkbox" id="visite_QuickMod" class="switchQuickMod"/>
		        		<label for="visite_QuickMod"></label>
		        	</div>-->
		        </th>
			</tr>
		</thead>
		<?php 
		if (count($A_vue['visites']))
		{
			echo '<tbody>';
			
			foreach ($A_vue['visites'] as $O_visite)
			{
				print '<tr>';
				
				echo 	'<td>'.$O_visite->donneIdentifiant().'</td>'.
						'<td>'.$O_visite->donnePraticien()->donnePrenom().' '.$O_visite->donnePraticien()->donneNom().'</td>'.
						'<td>'.$O_visite->donneChat()->donneNom().'</td>'.
						'<td>'.$O_visite->donneDate().'</td>'.
						'<td>'.$O_visite->donnePrix().'</td>'.
						'<td>'.$O_visite->donneObservations().'</td>';
				
				echo 	'<td><a href="/visite/suppression/'.$O_visite->donneIdentifiant().'">Effacer</a></td>'.
						'<td class="linkToSubmit"><a href="/visite/edit/'.$O_visite->donneIdentifiant().'">Modifier</a></td>';
				
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
		$S_selected = BoiteAOutils::recupererDepuisSession('limite_visites') == $i ? 'selected' : '';
		$S_options .= '<option '.$S_selected.' value="'.$i.'">'.$i.'</option>';
	}
    echo '<form class="row collapse small-12 columns right" method="post" action="/visite/changerLimite">'.
      			'<div class="small-5 columns"><label class="prefix" for="limite_visites_new">Par page</label></div>'.
        		'<div class="small-7 columns"><select id="limite_visites_new" name="limite_visites_new" onchange="this.parentNode.parentNode.submit()">'.
        			$S_options.
        		'</select></div>'.
         '</form>';
?>
</footer>
