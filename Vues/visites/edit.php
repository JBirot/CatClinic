<?php
$O_visite = $A_vue['visite'];
$S_optionPraticien = "";
if(count($A_vue['praticiens']))
{
	foreach ($A_vue['praticiens'] as $O_praticien)
	{
		$S_selected = $O_praticien == $O_visite->donnePraticien() ? 'selected':'';
		$S_optionPraticien .= '<option value="'.$O_praticien->donneIdentifiant().'" '.$S_selected.'>'.$O_praticien->donnePrenom().' '.$O_praticien->donneNom().'</option>';
	}
}
//Si deux chats ont le même nom, ou s'il y a trop de chats, il faudrait une preselection en fonction du proprietaire.
$S_optionChat = "";
if (count($A_vue['chats']))
{
	foreach ($A_vue['chats'] as $O_chat)
	{
		$S_selected = $O_chat == $O_visite->donneChat() ? 'selected':'';
		$S_optionChat .= '<option value="'.$O_chat->donneIdentifiant().'" '.$S_selected.'>'.$O_chat->donneNom().'</option>';
	}
}
?>	<header class="row small-10 small-centered">
		<h2 class="small-8 columns">Editer la visite</h2>
		<a class="button warning right small-4 columns" href="/visite">Retour</a>
	</header>
	<form method="post" action="/visite/miseajour/<?php echo $O_visite->donneIdentifiant(); ?>">
		<div class="row collapse small-10 small-centered columns">
			<div class="small-3 columns">
				<label class="prefix" title="Praticien de la visite."
						for="visite_praticien_<?php echo $O_visite->donneIdentifiant(); ?>">* Praticien</label>
			</div>
			<div class="small-9 columns">
				<select title="Praticien de la visite."
						id="visite_praticien_<?php echo $O_visite->donneIdentifiant(); ?>"
						name="visite_praticien_<?php echo $O_visite->donneIdentifiant(); ?>"
						autofocus>
						<?php echo $S_optionPraticien;?>
				</select>
			</div>
		</div>
		<div class="row collapse small-10 small-centered columns">
			<div class="small-3 columns">
				<label class="prefix" title="Chat de la visite."
						for="visite_chat_<?php echo $O_visite->donneIdentifiant(); ?>">* Chat</label>
			</div>
			<div class="small-9 columns">
				<select title="Chat de la visite."
						id="visite_chat_<?php echo $O_visite->donneIdentifiant(); ?>"
						name="visite_chat_<?php echo $O_visite->donneIdentifiant(); ?>">
						<?php echo $S_optionChat;?>
				</select>
			</div>
		</div>
		<div class="row collapse small-10 small-centered columns">
			<div class="small-3 columns">
				<label class="prefix" title="Date de la visite. (aaaa-mm-jj)"
						for="visite_date_<?php echo $O_visite->donneIdentifiant(); ?>">* Date</label>
			</div>
			<div class="small-9 columns">
				<input type="datetime" title="Date de la visite. (aaaa-mm-jj)"
						id="visite_date_<?php echo $O_visite->donneIdentifiant(); ?>"
						name="visite_date_<?php echo $O_visite->donneIdentifiant(); ?>"
						placeholder="aaaa-mm-jj"
						value="<?php echo $O_visite->donneDate();?>"
						maxlength="16" pattern="^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1]) ([0-1][0-9]|2[0-4]):[0-5][0-9]$"
						required />
			</div>
		</div>
		<div class="row collapse small-10 small-centered columns">
			<div class="small-3 columns">
				<label class="prefix" title="Prix de la visite."
						for="visite_prix_<?php echo $O_visite->donneIdentifiant(); ?>">* Prix</label>
			</div>
			<div class="small-9 columns">
				<input type="number" title="Prix de la visite"
						id="visite_prix_<?php echo $O_visite->donneIdentifiant(); ?>"
						name="visite_prix_<?php echo $O_visite->donneIdentifiant(); ?>"
						value="<?php echo $O_visite->donnePrix();?>"
						step="0.01"
						required>
			</div>
		</div>
		<div class="row collapse small-10 small-centered columns">
			<div class="small-12 columns">
				<label class="text-center" title="Observations sur la visite."
						for="visite_observations_<?php echo $O_visite->donneIdentifiant(); ?>">Observations</label>
			</div>
			<div class="small-12 columns">
				<textarea title="Observations sur la visite."
						id="visite_observations_<?php echo $O_visite->donneIdentifiant(); ?>"
						name="visite_observations_<?php echo $O_visite->donneIdentifiant(); ?>"><?php echo $O_visite->donneObservations();?></textarea>
			</div>
		</div>
		<div class="row small-10 small-centered">
    		<em>* Champs obligatoires</em>
    	</div>
		<div class="row small-10 small-centered">
			<input class="button expand" type="submit" name="modifier" Value="Modifier" />
		</div>
	</form>