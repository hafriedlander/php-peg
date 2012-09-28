<?php

namespace hafriedlander\Peg\Compiler\Token;

class ExpressionedRecurse extends Recurse {
	function match_function( $value ) {
		return '$this->expression($result, $stack, \''.$value.'\')';
	}
}