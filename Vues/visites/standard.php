<header class="row">
	<h2 class="text-center">Vos visites</h2>
</header>

<section>
	<header>
		<h3>Vos chats</h3>
	</header>
	<table>
		<thead>
			<tr>
				<th>Nom</th>
				<th>Tatouage</th>
				<th>Age</th>
			</tr>
		</thead>
		<tbody>
		<?php
		if (count($A_vue['chats']))
		{ 
			foreach ($A_vue['chats'] as $O_chat)
			{
				echo '<tr>';
				
				echo 	'<td>'.$O_chat->donneNom().'</td>'.
						'<td>'.$O_chat->donneTatouage().'</td>'.
						'<td>'.$O_chat->donneAge().'</td>';
				
				echo '</tr>';
			}
		}
		?>
		</tbody>
	</table>
</section>

<?php 
if(count($A_vue['chat']))
{?>
<section>
	<header>
		<h3>Vos visites</h3>
	
	<?php 
	if (count($A_vue['chats']))
	{ 
		echo '<div class="pagination-centered small-12 columns">';
		echo 	'<ul class="pagination">';
		foreach ($A_vue['chats'] as $key => $O_chat)
		{
			$S_current = $O_chat->donneIdentifiant() === $A_vue['chat']->donneIdentifiant()? 'class="current"':'';
			echo '<li '.$S_current.'><a href="/visite/page/'.($key+1).'">'.$O_chat->donneNom().'</a></li>';
		}
		echo 	'</ul>';
		echo '</div>';
	}
	?>
	</header>
	<?php 
	if (count($A_vue['visites']))
	{?>
	<table>
		<caption>Visites pour <?php echo $A_vue['chat']->donneNom();?></caption>
		<thead>
			<tr>
				<th>Date</th>
				<th>Prix</th>
				<th>Observations</th>
			</tr>
		</thead>
		<tbody>
		<?php 
		foreach ($A_vue['visites'] as $O_visite)
		{
			echo '<tr>';
			
			echo 	'<td>'.$O_visite->donneDate().'</td>'.
					'<td>'.$O_visite->donnePrix().'</td>'.
					'<td>'.$O_visite->donneObservations().'</td>';
			
			echo '</tr>';
		}
		?>
		</tbody>
	</table>
<?php }
	else
	{
		echo '<p>Aucune visite enregistrée pour '.$A_vue['chat']->donneNom().'</p>';
	}
	?>
</section>
<?php 
}?>