<?php
/** slug() */

/** Troca os espacos por "-" e retira acentos */
return function($text) {
  // $text = preg_replace( '/[`^~\'"]/u', null, iconv( 'UTF-8', 'ASCII//TRANSLIT', $text ) );

  $text = preg_replace("/[áàâãä]/u", "a", $text);
  $text = preg_replace("/[ÁÀÂÃÄ]/u", "A", $text);
  $text = preg_replace("/[éèê]/u", "e", $text);
  $text = preg_replace("/[ÉÈÊ]/u", "E", $text);
  $text = preg_replace("/[íì]/u", "i", $text);
  $text = preg_replace("/[ÍÌ]/u", "I", $text);
  $text = preg_replace("/[óòôõö]/u", "o", $text);
  $text = preg_replace("/[ÓÒÔÕÖ]/u", "O", $text);
  $text = preg_replace("/[úùü]/u", "u", $text);
  $text = preg_replace("/[ÚÙÜ]/u", "U", $text);
  $text = preg_replace("/ç/u", "c", $text);
  $text = preg_replace("/Ç/u", "C", $text);
  $text = preg_replace("/[][><}{)(:;,!?*%~^`&#@]/u", "", $text);

  $text = str_replace('.', ' ', $text);
  $text = str_replace('/', ' ', $text);

  while ( stripos($text, '  ') != FALSE ) {
    $text = str_replace('  ', ' ', $text);
  }

  $text = trim($text);
  $text = str_replace(' ', '-', $text);
  $text = strtolower($text);

  return urlencode($text);
};