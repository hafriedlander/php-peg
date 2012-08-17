<?php

require_once __DIR__.'/compiler.php';

ExamplesCompiler::compile(
  __DIR__.'/Simple.peg.inc',
  __DIR__.'/SimpleParser.php'
);

//require 'SimpleParser.php';

$x = new Simple('foo bar') ;
$res = $x->match_foobar();
var_dump($res);

