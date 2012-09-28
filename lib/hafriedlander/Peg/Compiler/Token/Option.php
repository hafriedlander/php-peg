<?php

namespace hafriedlander\Peg\Compiler\Token;

use hafriedlander\Peg\Compiler\Token;
use hafriedlander\Peg\Compiler\PHPBuilder;

class Option extends Token {
	function __construct( $opt1, $opt2 ) {
		parent::__construct( 'option', array( $opt1, $opt2 ) ) ;
	}

	function match_code( $value ) {
		$id = $this->varid() ;
		$code = PHPBuilder::build()
			->l(
			$this->save($id)
		) ;

		foreach ( $value as $opt ) {
			$code->l(
				$opt->compile()->replace(array(
					'MATCH' => 'MBREAK',
					'FAIL' => NULL
				)),
				$this->restore($id)
			);
		}
		$code->l( 'FBREAK' ) ;

		return $this->match_fail_block( $code ) ;
	}
}
