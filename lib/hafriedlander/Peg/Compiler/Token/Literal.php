<?php

namespace hafriedlander\Peg\Compiler\Token;

use hafriedlander\Peg\Compiler\PHPBuilder;

class Literal extends Expressionable {
	function __construct( $value ) {
		parent::__construct( 'literal', "'" . substr($value,1,-1) . "'" );
	}

	function match_code( $value ) {
		// We inline single-character matches for speed
		if ( !$this->contains_expression($value) && strlen( eval( 'return '. $value . ';' ) ) == 1 ) {
			return $this->match_fail_conditional( 'substr($this->string,$this->pos,1) == '.$value,
				PHPBuilder::build()->l(
					'$this->pos += 1;',
					$this->set_text($value)
				)
			);
		}
		return parent::match_code($value);
	}
}
