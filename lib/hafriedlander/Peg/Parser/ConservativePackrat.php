<?php

namespace hafriedlander\Peg\Parser;

/**
 * Conservative Packrat will only memo-ize a result on the second hit, making it more memory-lean than Packrat,
 * but less likely to go exponential that Parser. Because the store logic is much more complicated this is a net
 * loss over Parser for many simple grammars.
 *
 * @author Hamish Friedlander
 */
class ConservativePackrat extends Basic {
	function packhas( $key, $pos ) {
		return isset( $this->packres[$key] ) && $this->packres[$key] !== NULL ;
	}

	function packread( $key, $pos ) {
		$this->pos = $this->packpos[$key];
		return $this->packres[$key] ;
	}

	function packwrite( $key, $pos, $res ) {
		if ( isset( $this->packres[$key] ) ) {
			$this->packres[$key] = $res ;
			$this->packpos[$key] = $this->pos ;
		}
		else {
			$this->packres[$key] = NULL ;
		}
		return $res ;
	}
}

