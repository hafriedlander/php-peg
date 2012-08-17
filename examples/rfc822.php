<?php

require_once __DIR__.'/compiler.php';

ExamplesCompiler::compile(
  __DIR__.'/Rfc822.peg.inc',
  __DIR__.'/Rfc822Parser.php'
);

$p = new Rfc822(
  'John Byorgson <byorn@again.com>, "Akira \"Bad Boy\" Kenada" <akira@neotokyo.com>'
);
var_dump($p->match_address_header());
