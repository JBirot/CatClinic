<h1>Catégories</h1>
<table>
<caption>Liste des catégories d'article</caption>
<thead>
    <tr>
        <td>Identifiant</td>
        <td>Titre</td>
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
	        echo '<td>'. $O_categorie->donneIdentifiant() . '</td><td>' . 
	                     $O_categorie->donneTitre() . '</td>';
		
	        print '<td><a href="/categorie/suppr/' . $O_categorie->donneIdentifiant() .
	                '" onclick="return(confirm(\'Etes-vous sûr de vouloir supprimer cet categorie ?\'));">
	                Effacer</a></td>';
	        echo '<td><a href="/categorie/edit/' . $O_categorie->donneIdentifiant() . '">Modifier</a></td>';
        

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
?>