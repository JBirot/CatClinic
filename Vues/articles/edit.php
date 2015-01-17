<?php
$S_optionCategorie = "";
if (count($A_vue['categories']))
{
	foreach ($A_vue['categories'] as $O_categorie)
	{
		$S_select= $O_categorie->donneIdentifiant() == $A_vue['article']->donneCategorieId() ? 'select' : '';
		$S_optionCategorie .=  '<option value="' . $O_categorie->donneIdentifiant() . '"'.$S_select.' >' . $O_categorie->donneTitre() . '</option>';
	}
}
$S_optionAuteur = "";
if (count($A_vue['auteurs']))
{
	foreach ($A_vue['auteurs'] as $O_auteur)
	{
		$S_select= $O_auteur->donneIdentifiant() == $A_vue['article']->donneAuteurId() ? 'select' : '';
		$S_optionAuteur .= '<option value="' . $O_auteur->donneIdentifiant() . '" '.$S_select.' >' . $O_auteur->donneNom() . " " . $O_auteur->donnePrenom() . "</option>";
	}
}
?>
<section>
	<h1>Modifier l'article "<?php echo $A_vue['article']->donneTitre();?>"</h1>
	<form 	action="/article/miseajour/<?php echo $A_vue['article']->donneIdentifiant();?>"
			method="post">
		<label for="article_titre_<?php echo $A_vue['article']->donneIdentifiant();?>">Titre</label>
			<input type="text" name="article_titre_<?php echo $A_vue['article']->donneIdentifiant();?>"
				value="<?php echo $A_vue['article']->donneTitre(); ?>" />
		<label for="article_categorie_<?php echo $A_vue['article']->donneIdentifiant();?>">Catégorie</label>
			<select name="article_categorie_<?php echo $A_vue['article']->donneIdentifiant();?>">
				<?php echo $S_optionCategorie ; ?>
			</select>
		<label for="article_contenu_<?php echo $A_vue['article']->donneIdentifiant();?>">Contenu</label>
			<textarea name="article_contenu_<?php echo  $A_vue['article']->donneIdentifiant();?>" rows="" cols=""><?php echo $A_vue['article']->donneContenu();?></textarea>
		<label for="article_auteur_<?php echo  $A_vue['article']->donneIdentifiant();?>">Auteur</label>
			<select name="article_auteur_<?php echo  $A_vue['article']->donneIdentifiant();?>">
				<?php echo $S_optionAuteur ; ?>
			</select>
		<input type="submit" value="Modifier" />			
	</form>
</section>