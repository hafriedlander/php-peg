<?php

namespace hafriedlander\Peg\Parser;

/**
 * FalseOnlyPackrat only remembers which results where false. Experimental.
 *
 * @author Hamish Friedlander
 */
class FalseOnlyPackrat extends Basic {
	function __construct( $string ) {
		parent::__construct( $string ) ;

		$this->packstatebase = str_repeat( '.', strlen( $string ) ) ;
		$this->packstate = array() ;
	}

	function packhas( $key, $pos ) {
		return isset( $this->packstate[$key] ) && $this->packstate[$key][$pos] == 'F' ;
	}

	function packread( $key, $pos ) {
		return FALSE ;
	}

	function packwrite( $key, $pos, $res ) {
		if ( !isset( $this->packstate[$key] ) ) $this->packstate[$key] = $this->packstatebase ;

		if ( $res === FALSE ) {
			$this->packstate[$key][$pos] = 'F' ;
		}

		return $res ;
	}
}