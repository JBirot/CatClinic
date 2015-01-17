<?php 
$S_optionCategorie = "";
if (count($A_vue['categories']))
{
	foreach ($A_vue['categories'] as $O_categorie)
	{
		$S_optionCategorie .=  '<option value="' . $O_categorie->donneIdentifiant() . '" >' . $O_categorie->donneTitre() . '</option>'; 
	}			
}
$S_optionAuteur = "";
if (count($A_vue['auteurs']))
{
	foreach ($A_vue['auteurs'] as $O_auteur)
	{
		$S_optionAuteur .= '<option value="' . $O_auteur->donneIdentifiant() . '" >' . $O_auteur->donneNom() . " " . $O_auteur->donnePrenom() . "</option>";
	}
}
?>
<section>
	<h1>Créer un article</h1>
	<form method="post" action="/article/creer" >
		<label for="article_nouveau_titre">Titre</label>
			<input type="text" name="article_nouveau_titre" />
		<label for="article_nouveau_categorie">Catégorie</label>
			<select name="article_nouveau_categorie">
				<?php echo $S_optionCategorie ; ?>
			</select>
		<label for="article_nouveau_contenu">Contenu</label>
			<textarea name="article_nouveau_contenu" rows="" cols=""></textarea>
		<label for="article_nouveau_auteur">Auteur</label>
			<select name="article_nouveau_auteur">
				<?php  echo $S_optionAuteur ; ?>
			</select>
		<input type="submit" value="Créer" />
	</form>
</section>