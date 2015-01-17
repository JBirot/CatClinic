<h1>Auteurs</h1>
<table>
<caption>Liste des auteurs d'article</caption>
<thead>
    <tr>
        <td>Identifiant</td>
        <td>Nom</td>
        <td>Prenom</td>
    </tr>
</thead>
<?php
if (count($A_vue['auteurs']))
{
    echo '<tbody>';

    foreach ($A_vue['auteurs'] as $O_auteur)
    {
        // Allez, on ressort echo, print...
        print '<tr>';
	        echo '<td>'. $O_auteur->donneIdentifiant() . '</td><td>' . 
	                     $O_auteur->donneNom() . '</td><td>' .
	        			 $O_auteur->donnePrenom() . '</td>';
	
	        print '<td><a href="/auteur/suppr/' . $O_auteur->donneIdentifiant() .
	                '" onclick="return(confirm(\'Etes-vous sûr de vouloir supprimer cet auteur ?\'));">
	                Effacer</a></td>';
	        echo '<td><a href="/auteur/edit/' . $O_auteur->donneIdentifiant() . '">Modifier</a></td>';
        

        echo '</tr>';
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
?><?php
