<?php

require_once __DIR__.'/compiler.php';

ExamplesCompiler::compile(
  __DIR__.'/EqualRepeat.peg.inc',
  __DIR__.'/EqualRepeatParser.php'
);

function match($str, $should_match=true)
{
	$p = new EqualRepeat($str);
  $r = $p->match_X();
  printf(
    "%s (%s)\n%s\n\n",
    $str,
    'should ' . ($should_match ? '' : 'not ') . 'match',
    $r ? print_r($r, true) : 'No Match'
  );
}

match('aabbcc');      // Should match
match('aaabbbccc');   // Should match

match('aabbbccc', false);    // Should not match
match('aaabbccc', false);    // Should not match
match('aaabbbcc', false);    // Should not match

match('aaabbbcccc', false);  // Should not match

