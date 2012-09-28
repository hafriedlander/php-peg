<?php

namespace hafriedlander\Peg\Compiler\Token;

use hafriedlander\Peg\Compiler\PHPBuilder;

class Regex extends Expressionable {
	static function escape( $rx ) {
		$rx = str_replace( "'", "\\'", $rx ) ;
		$rx = str_replace( '\\\\', '\\\\\\\\', $rx ) ;
		return $rx ;
	}

	function __construct( $value ) {
		parent::__construct('rx', self::escape($value));
	}

	function match_code( $value ) {
		return parent::match_code("'{$value}'");
	}
}