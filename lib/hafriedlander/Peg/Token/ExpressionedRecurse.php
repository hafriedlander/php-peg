<?php

namespace hafriedlander\Peg\Token;

class ExpressionedRecurse extends Recurse
{
  public function match_function($value)
  {
		return '$this->expression($result, $stack, \''.$value.'\')';
	}
}
