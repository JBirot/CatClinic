<h1>Vos visites</h1>
<table>
<caption>Voici la liste de vos chats</caption>
<thead>
    <tr>
        <td>Nom</td>
        <td>Tatouage</td>
        <td>Age</td>
    </tr>
</thead>
<?php

if (count($A_vue['chat']))
{
    $O_chat = $A_vue['chat'];
    echo '<h2>Vos chats</h2>';
    echo '<tbody>';
    print '<tr>';
    echo '<td>' .
                    $O_chat->donneNom() . '</td><td>' .
                    $O_chat->donneTatouage() . '</td><td>' .
                    $O_chat->donneAge()      . '</td>';
    echo '</tr>';

    echo '</tbody>';
}
?>
</table>
<table>
<caption>Voici la liste de vos visites</caption>
<thead>
    <tr>
        <td>Date</td>
        <td>Prix</td>
        <td>Observations</td>
    </tr>
</thead>
<?php

if (count($A_vue['visites']))
{
    echo '<h2>Visites</h2>';

    foreach ($A_vue['visites'] as $O_visite)
    {
        echo '<tbody>';
        print '<tr>';
        echo '<td>'. date("d/m/Y", strtotime($O_visite->donneDate()))     . '</td><td>' . 
                        $O_visite->donnePrix()  . '&nbsp;&euro;</td><td>' .
                        $O_visite->donneObservations()      . '</td>';
        echo '</tr>';
        echo '</tbody>';
    }
}
?>
</table>
<?php
    if (isset($A_vue['pagination']))
    {
        echo '<div style="">';
        foreach ($A_vue['pagination'] as $S_lien)
        {
            echo '&nbsp;' . $S_lien;
        }
        echo '</div>';
    }
?>