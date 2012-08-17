<?php

require_once __DIR__.'/lib/vendor/Symfony/Component/ClassLoader/UniversalClassLoader.php';

$loader = new Symfony\Component\ClassLoader\UniversalClassLoader();
$loader->registerNamespace(
  'hafriedlander', array(
    __DIR__.'/lib',
  )
);
$loader->register();


use hafriedlander\Peg\Compiler;


if ($argc < 2 || $argc > 3) {

  print <<<EOS
PEG Parser Generator: A compiler for PEG parsers in PHP
(C) 2009 SilverStripe. See COPYING for redistribution rights.

Usage: {$argv[0]} infile [ outfile ]

EOS;

} else {

  $fname = ($argv[1] === '-' ? 'php://stdin' : $argv[1]);
  $peg = file_get_contents($fname);
  $code = Compiler::compile($peg);

  if (!empty($argv[2]) && '-' !== $argv[2]) {
    file_put_contents($argv[2], $code);
  } else {
    print $code;
  }

}
