<?php

namespace hafriedlander\Peg\Token;

use hafriedlander\Peg\Compiler\Builder;

class Literal extends Expressionable
{
  public function __construct($value)
  {
		parent::__construct('literal', "'" . substr($value,1,-1) . "'");
	}

  public function match_code($value)
  {
		// We inline single-character matches for speed
    if (
      !$this->contains_expression($value)
      && 1 === strlen(eval('return '. $value . ';'))
    ) {
      return $this->match_fail_conditional(
        'substr($this->string,$this->pos,1) == '.$value,
				Builder::build()->l(
					'$this->pos += 1;',
					$this->set_text($value)
				)
			);
		}
		return parent::match_code($value);
	}
}
