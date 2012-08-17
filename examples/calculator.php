<?php

require_once __DIR__.'/compiler.php';

ExamplesCompiler::compile(
  __DIR__.'/Calculator.peg.inc',
  __DIR__.'/CalculatorParser.php'
);

$x = new Calculator('(2 + 4) * 3 - 10') ;
$res = $x->match_Expr();

if (false === $res) {
	echo "No Match\n";
} else {
	var_dump($res);
}

