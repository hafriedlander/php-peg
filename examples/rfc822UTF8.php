<?php

require_once __DIR__.'/compiler.php';

ExamplesCompiler::compile(
  __DIR__.'/Rfc822.peg.inc',
  __DIR__.'/Rfc822Parser.php'
);
ExamplesCompiler::compile(
  __DIR__.'/Rfc822UTF8.peg.inc',
  __DIR__.'/Rfc822UTF8Parser.php'
);


$p = new Rfc822UTF8(
  'JØhn ByØrgsØn <byorn@again.com>, "アキラ" <akira@neotokyo.com>'
);
var_dump(
  $p->match_address_header()
);
