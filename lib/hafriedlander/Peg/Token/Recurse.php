<?php

namespace hafriedlander\Peg\Token;

use hafriedlander\Peg\Token;
use hafriedlander\Peg\Compiler;
use hafriedlander\Peg\Compiler\Builder;

class Recurse extends Token
{
  public function __construct($value)
  {
		parent::__construct('recurse', $value);
	}

  public function match_function($value)
  {
		return "'".$this->function_name($value)."'";
	}
	
  public function match_code($value)
  {
		$function = $this->match_function($value);
    $storetag = $this->function_name(
      $this->tag ? $this->tag : $function
    );

    if (Compiler::$debug) {

			$debug_header = Builder::build()->l(
				'$indent = str_repeat(" ", $this->depth);',
				'$this->depth += 2;',
				'$sub = (strlen($this->string) - $this->pos > 20) ? (substr($this->string, $this->pos, 20) . "...") : substr($this->string, $this->pos);',
				'$sub = preg_replace(\'/(\r|\n)+/\', " {NL} ", $sub);',
				'print($indent."Matching against $matcher (".$sub.")\n");'
			);
			$debug_match = Builder::build()->l(
				'print($indent."MATCH\n");',
				'$this->depth -= 2;'
	    );
			$debug_fail = Builder::build()->l(
				'print($indent."FAIL\n");',
				'$this->depth -= 2;'
      );

		} else {
			$debug_header = $debug_match = $debug_fail = null;
		}

		return Builder::build()->l(
      '$matcher = \'match_\'.'.$function.';',
      '$key = $matcher;',
      '$pos = $this->pos;',
			$debug_header,
      '$subres = ($this->packhas($key, $pos)',
      '  ? $this->packread($key, $pos)',
      '  : $this->packwrite($key, $pos, $this->$matcher(array_merge($stack, array($result)))));',
      $this->match_fail_conditional(
        'false !== $subres',
				Builder::build()->l(
					$debug_match,
					false === $this->tag ?
						'$this->store($result, $subres);' :
						'$this->store($result, $subres, "'.$storetag.'");'
				),
				Builder::build()->l($debug_fail)
      )
    );
	}
}

