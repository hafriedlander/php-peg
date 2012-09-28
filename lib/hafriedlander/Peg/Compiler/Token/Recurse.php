<?php

namespace hafriedlander\Peg\Compiler\Token;

use hafriedlander\Peg\Compiler\Token;
use hafriedlander\Peg\Compiler\PHPBuilder;

class Recurse extends Token {
	function __construct( $value ) {
		parent::__construct( 'recurse', $value ) ;
	}

	function match_function( $value ) {
		return "'".$this->function_name($value)."'";
	}

	function match_code( $value ) {
		$function = $this->match_function($value) ;
		$storetag = $this->function_name( $this->tag ? $this->tag : $this->match_function($value) ) ;

		if ( \hafriedlander\Peg\Compiler::$debug ) {
			$debug_header = PHPBuilder::build()
				->l(
				'$indent = str_repeat( " ", $this->depth );',
				'$this->depth += 2;',
				'$sub = ( strlen( $this->string ) - $this->pos > 20 ) ? ( substr( $this->string, $this->pos, 20 ) . "..." ) : substr( $this->string, $this->pos );',
				'$sub = preg_replace( \'/(\r|\n)+/\', " {NL} ", $sub );',
				'print( $indent."Matching against $matcher (".$sub.")\n" );'
			);

			$debug_match = PHPBuilder::build()
				->l(
				'print( $indent."MATCH\n" );',
				'$this->depth -= 2;'
			);

			$debug_fail = PHPBuilder::build()
				->l(
				'print( $indent."FAIL\n" );',
				'$this->depth -= 2;'
			);
		}
		else {
			$debug_header = $debug_match = $debug_fail = NULL ;
		}

		return PHPBuilder::build()->l(
			'$matcher = \'match_\'.'.$function.'; $key = $matcher; $pos = $this->pos;',
			$debug_header,
			'$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );',
			$this->match_fail_conditional( '$subres !== FALSE',
				PHPBuilder::build()->l(
					$debug_match,
					$this->tag === FALSE ?
						'$this->store( $result, $subres );' :
						'$this->store( $result, $subres, "'.$storetag.'" );'
				),
				PHPBuilder::build()->l(
					$debug_fail
				)
			));
	}
}