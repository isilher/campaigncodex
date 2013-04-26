<?php

function charlink($character)
{
	$charname = $character->name;
	$charname = str_replace(' ', '-', $charname);
	
	return $charname . '-' . $character->unique;
}