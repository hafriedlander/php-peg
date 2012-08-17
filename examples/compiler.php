<?php

require_once __DIR__.'/../autoload.php';

use hafriedlander\Peg\Compiler;

Compiler::$debug = true;

class ExamplesCompiler
{
  public static function compile($peg, $output, $compile=true)
  {
    if ($compile) {
      $code = Compiler::compile(file_get_contents($peg));
      //eval($code);
      file_put_contents($output, $code);
    }
    require $output;
  }
}
