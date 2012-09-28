<?php

namespace hafriedlander\Peg\Compiler\Token;

class Whitespace extends Terminal {
	function __construct( $optional ) {
		parent::__construct( 'whitespace', $optional ) ;
	}

	/* Call recursion indirectly */
	function match_code( $value ) {
		$code = parent::match_code( '' ) ;
		return $value ? $code->replace( array( 'FAIL' => NULL )) : $code ;
	}
}