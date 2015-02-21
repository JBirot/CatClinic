<?php 
	if(Authentification::estAdministrateur())
	{
		echo '<a class="button small-12" href="/article"><< Gérer les articles</a>';
	}
?>
<header class="row">
	<h2 class="text-center">Articles</h2>
</header>
<?php 
if(count($A_vue['articles']))
{
	foreach ($A_vue['articles'] as $O_article)
	{
		echo '<article class="panel">';
		
		echo 	'<header>'.
					'<h3>'.$O_article->donneTitre().'</h3>'.
			 	'</header>';
		
		echo 	'<p>'.$O_article->donneContenu().'</p>';
		
		echo 	'<footer class="right">'.
					'<p>Par <strong>'.$O_article->donneAuteur()->donnePrenom().' '.$O_article->donneAuteur()->donneNom().'</strong> le '.$O_article->donneDate().'.</p>'.
				'</footer>';
		
		echo '</article>';
	}
}
else 
{
	echo '<p>Aucun article en ligne.</p>';
}
?>
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
?>
</footer>