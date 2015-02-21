<?php
$O_chat = $A_vue['chat'];
?>	<header class="row small-10 small-centered">
		<h2 class="small-8 columns">Editer le chat <?php echo $O_chat->donneNom();?></h2>
		<a class="button warning right small-4 columns" href='/chat'>Retour</a>
	</header>
	<form action="/chat/miseajour/<?php echo $O_chat->donneIdentifiant();?>"
			name="chat_edition"
			method="post">
		<div class="row collapse small-10 small-centered">
			<div class="small-3 columns">
				<label class="prefix" title="Nom du chat. (Entre 3 et 24 lettres et espaces)"
						for="chat_nom_<?php echo $O_chat->donneIdentifiant();?>">* Nom</label>
			</div>
			<div class="small-9 columns">
				<input type="text" title="Nom du chat. (Entre 3 et 24 lettres et espacs)"
						name="chat_nom_<?php echo $O_chat->donneIdentifiant();?>"
						id="chat_nom_<?php echo $O_chat->donneIdentifiant();?>"
						placeholder="Entre 3 et 24 lettres et espaces."
						maxlength="24" pattern="^[a-zA-Z][a-zA-Z ]*[a-zA-Z]$"
						value="<?php echo $O_chat->donneNom();?>"
						required autofocus />
			</div>
		</div>
		<div class="row collapse small-10 small-centered">
			<div class="small-3 columns">
				<label class="prefix" title="Date de naissance du chat. (format aaaa-mm-jj)"
						for="chat_naissance_<?php echo $O_chat->donneIdentifiant();?>">* Naissance</label>
						
			</div>
			<div class="small-9 columns"> 
				<input type="date" title="Date de naissance du chat.(format aaaa-mm-jj)"
						name="chat_naissance_<?php echo $O_chat->donneIdentifiant();?>"
						id="chat_naissance_<?php echo $O_chat->donneIdentifiant();?>"
						placeholder="aaaa-mm-jj"
						maxlength="10" pattern="^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$"
						value="<?php echo $O_chat->donneDate();?>"
						required />
			</div>
		</div>
		<div class="row collapse small-10 small-centered">
			<div class="small-3 columns">
				<label class="prefix" title="Tatouage du chat. (Jusqu'à 10 lettres et chiffres)"
						for="chat_tatouage_<?php echo $O_chat->donneIdentifiant();?>">* Tatouage</label>
			</div>
			<div class="small-9 columns">
				<input type="text" title="Tatouage du chat. (Jusqu'à 10 lettres et chiffres)"
						name="chat_tatouage_<?php echo $O_chat->donneIdentifiant();?>"
						id="chat_tatouage_<?php echo $O_chat->donneIdentifiant();?>"
						placeholder="10 lettres et chiffres"
						maxlength="10" pattern="^[0-9a-Z]{1,10}$"
						value="<?php echo $O_chat->donneTatouage();?>"
						required />
			</div>
		</div>
		<div class="row small-10 small-centered">
    		<em>* Champs obligatoires</em>
    	</div>
		<div class="row small-10 small-centered">
			<input class="button expand" type="submit" name="modifier" Value="Modifier" />
		</div>
	</form>