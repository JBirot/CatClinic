<?php
$S_optionPraticien = "";
if(count($A_vue['praticiens']))
{
	foreach ($A_vue['praticiens'] as $O_praticien)
	{
		$S_optionPraticien .= '<option value="'.$O_praticien->donneIdentifiant().'">'.$O_praticien->donnePrenom().' '.$O_praticien->donneNom().'</option>';
	}
}
//Si deux chats ont le même nom, ou s'il y a trop de chats, il faudrait une preselection en fonction du proprietaire.
$S_optionChat = "";
if (count($A_vue['chats']))
{
	foreach ($A_vue['chats'] as $O_chat)
	{
		$S_optionChat .= '<option value="'.$O_chat->donneIdentifiant().'">'.$O_chat->donneNom().'</option>';
	}
}
?>	<header class="row small-10 small-centered">
		<h2 class="small-8 columns">Nouvelle visite</h2>
		<a class="button warning right small-4 columns" href="/visite">Retour</a>
	</header>
	<form method="post" action="/visite/creer/">
		<div class="row collapse small-10 small-centered columns">
			<div class="small-3 columns">
				<label class="prefix" title="Praticien de la visite."
						for="visite_nouveau_praticien">* Praticien</label>
			</div>
			<div class="small-9 columns">
				<select title="Praticien de la visite."
						id="visite_nouveau_praticien"
						name="visite_nouveau_praticien"
						autofocus>
						<?php echo $S_optionPraticien;?>
				</select>
			</div>
		</div>
		<div class="row collapse small-10 small-centered columns">
			<div class="small-3 columns">
				<label class="prefix" title="Chat de la visite."
						for="visite_nouveau_chat">* Chat</label>
			</div>
			<div class="small-9 columns">
				<select title="Chat de la visite."
						id="visite_nouveau_chat"
						name="visite_nouveau_chat">
						<?php echo $S_optionChat;?>
				</select>
			</div>
		</div>
		<div class="row collapse small-10 small-centered columns">
			<div class="small-3 columns">
				<label class="prefix" title="Date de la visite. (aaaa-mm-jj hh:mm)"
						for="visite_nouveau_date">* Date</label>
			</div>
			<div class="small-9 columns">
				<input type="datetime" title="Date de la visite. (aaaa-mm-jj hh:mm)"
						id="visite_nouveau_date"
						name="visite_nouveau_date"
						placeholder="aaaa-mm-jj hh:mm"
						maxlength="16" pattern="^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1]) ([0-1][0-9]|2[0-4]):[0-5][0-9]$"
						required />
			</div>
		</div>
		<div class="row collapse small-10 small-centered columns">
			<div class="small-3 columns">
				<label class="prefix" title="Prix de la visite."
						for="visite_nouveau_prix">* Prix</label>
			</div>
			<div class="small-9 columns">
				<input type="number" title="Prix de la visite"
						id="visite_nouveau_prix"
						name="visite_nouveau_prix"
						step="0.01"
						required>
			</div>
		</div>
		<div class="row collapse small-10 small-centered columns">
			<div class="small-12 columns">
				<label class="text-center" title="Observations sur la visite."
						for="visite_nouveau_observations">Observations</label>
			</div>
			<div class="small-12 columns">
				<textarea title="Observations sur la visite."
						id="visite_nouveau_observations"
						name="visite_nouveau_observations"></textarea>
			</div>
		</div>
		<div class="row small-10 small-centered">
    		<em>* Champs obligatoires</em>
    	</div>
		<div class="small-10 small-centered columns">	
			<input class="button expand radius" type="submit" value="Créer" />
		</div>
	</form>