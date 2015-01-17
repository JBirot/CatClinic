<h1>Articles</h1>
<table>
<caption>Liste des articles</caption>
<thead>
	<tr>
		<td>Titre</td>
		<td>Catégorie</td>
		<td>Auteur</td>
		<td>En ligne</td>
		<td>Date</td>
	</tr>
</thead>
<?php 
if(count($A_vue['articles']))
{
	echo '<tbody>';

	foreach ($A_vue['articles'] as $O_article)
	{
		$S_checked = $O_article->estEnLigne() ? 'checked' : '';
		print '<tr>';
		
		echo 	'<td>'. $O_article->donneTitre() . '</td><td>' .
						$O_article->donneCategorieTitre() . '</td><td>' .
						$O_article->donneAuteurNom() . " " . $O_article->donneAuteurPrenom() . '</td><td>' .
	 					'<input type="checkbox" '. $S_checked . ' /></td><td>' .
	 					$O_article->donneDate() . '</td>';
		
		print '<td><a href="/article/suppr/' . $O_article->donneIdentifiant() .
		'" onclick="return(confirm(\'Etes-vous sûr de vouloir supprimer cet article ?\'));">
	                Effacer</a></td>';
		echo '<td><a href="/article/edit/' . $O_article->donneIdentifiant() . '">Modifier</a></td>';
		
		print '</tr>';						
	}
	
	echo '</tbody>';
}
?>
</table>
<?php
    if (isset($A_vue['pagination']))
    {
        echo '<div>';
        foreach ($A_vue['pagination'] as $I_numeroPage => $S_lien)
        {
            echo '&nbsp;' . ($S_lien ? '<a href="/' . $S_lien . '">' . $I_numeroPage . '</a>' : $I_numeroPage);
        }
        echo '</div>';
    }
?>