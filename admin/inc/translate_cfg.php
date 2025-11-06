<?php
// inc/translate_cfg.php
// Konfigurierbarer Endpoint für LibreTranslate-kompatible APIs.
// Beispiele (öffentliche Instanzen — Rate-Limits beachten):
//   https://libretranslate.de
//   https://translate.terraprint.co
// Optionaler API-Key: $TRANSLATE_API_KEY
$TRANSLATE_API_ENDPOINT = getenv('TRANSLATE_API_ENDPOINT') ?: 'https://libretranslate.de';
$TRANSLATE_API_KEY = getenv('TRANSLATE_API_KEY') ?: '';