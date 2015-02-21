<?php

$S_erreur = BoiteAOutils::recupererDepuisSession('erreur', TRUE); // on veut la détruire après affichage, d'où le true
$S_message = BoiteAOutils::recupererDepuisSession('message',TRUE);

if($S_erreur)
{
	print '<span data-alert class="alert-box alert">' . $S_erreur . '<button href="#" class="close" aria-label="Close Alert">'.html_entity_decode('&times;').'</button></span>';
}

if($S_message)
{
	print '<span data-alert class="alert-box success">' . $S_message . '<button href="#" class="close" aria-label="Close Alert">'.html_entity_decode('&times;').'</button></span>';
}