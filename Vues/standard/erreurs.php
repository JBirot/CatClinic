<?php

$S_erreur = BoiteAOutils::recupererDepuisSession('erreur', TRUE); // on veut la détruire après affichage, d'où le true
$S_message = BoiteAOutils::recupererDepuisSession('message',TRUE);

if($S_erreur)
{
	print '<span class="error">' . $S_erreur . '</span>';
}

if($S_message)
{
	print '<span class="message">' . $S_message . '</span>';
}