<?php
	$A_champs = array(	array(0,''),
						array(0,''),
						array(0,''),
						array(0,'')
	);
	if (!empty($A_vue['ordre']))
	{
		$I_sens = $A_vue['ordre'][1] ? 0:1;
		$S_span = '<img class="right" src="/Ressources/Public/Images/miniarrow'.$I_sens.'.png"/>';
		$A_champs[$A_vue['ordre'][0]] = array($I_sens,$S_span);
	}
?>
<header class="row">
	<h2 class="small-7 columns">Chats</h2>
	<a class="small-5 columns button right" href="/chat/creation">Nouveau chat</a>
</header>

<form id="chat" class="row responsive" method="post" action="/chat/validation">
	<table>
		<caption>Liste des chats enregistrés</caption>
		<thead>
			<tr>
				<th><a href="/chat/changerOrdre/0/<?php echo $A_champs[0][0] ;?>">Id<?php echo $A_champs[0][1];?></a></th>
				<th><a href="/chat/changerOrdre/1/<?php echo $A_champs[1][0] ;?>">Nom<?php echo $A_champs[1][1];?></a></th>
				<th><a href="/chat/changerOrdre/2/<?php echo $A_champs[2][0] ;?>">Naissance<?php echo $A_champs[2][1];?></a></th>
				<th><a href="/chat/changerOrdre/2/<?php echo $A_champs[2][0] ;?>">Age<?php echo $A_champs[2][1];?></a></th>
				<th><a href="/chat/changerOrdre/3/<?php echo $A_champs[3][0] ;?>">Tatouage<?php echo $A_champs[3][1];?></a></th>
				<th colspan="2">
		        	<div class="switch small radius right">
		        		<input type="checkbox" id="utilisateur_QuickMod" class="switchQuickMod"/>
		        		<label for="utilisateur_QuickMod"></label>
		        	</div>
		        </th>
			</tr>
		</thead>
		<?php 
		if (count($A_vue['chats']))
		{
			echo '<tbody>';
			
			foreach ($A_vue['chats'] as $O_chat)
			{
				echo '<tr>';
				
				echo 	'<td>'.$O_chat->donneIdentifiant().'</td>'.
						'<td class="textToInput">'.$O_chat->donneNom().'</td>'.
						'<td class="textToDate">'.$O_chat->donneDate().'</td>'.
						'<td>'.$O_chat->donneAge().'</td>'.
						'<td class="textToInput">'.$O_chat->donneTatouage().'</td>';
				
				echo 	'<td><a href="/chat/suppression/'.$O_chat->donneIdentifiant().'">Effacer</a></td>'.
						'<td class="linkToSubmit"><a href="/chat/edit/'.$O_chat->donneIdentifiant().'">Modifier</a></td>';
				
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
		$S_selected = BoiteAOutils::recupererDepuisSession('limite_chats') == $i ? 'selected' : '';
		$S_options .= '<option '.$S_selected.' value="'.$i.'">'.$i.'</option>';
	}
    echo '<form class="row collapse small-12 columns right" method="post" action="/chat/changerLimite">'.
      			'<div class="small-5 columns"><label class="prefix" for="limite_chats_new">Par page</label></div>'.
        		'<div class="small-7 columns"><select id="limite_chats_new" name="limite_chats_new" onchange="this.parentNode.parentNode.submit()">'.
        			$S_options.
        		'</select></div>'.
         '</form>';
?>
</footer>