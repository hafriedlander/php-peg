<?php

namespace hafriedlander\Peg\Compiler\Rule;

/**
 * Handles storing of information for an expression that applies to the <i>next</i> token, and deletion of that
 * information after applying
 *
 * @author Hamish Friedlander
 */
class PendingState {
	function __construct() {
		$this->what = NULL ;
	}

	function set( $what, $val = TRUE ) {
		$this->what = $what ;
		$this->val = $val ;
	}

	function apply_if_present( $on ) {
		if ( $this->what !== NULL ) {
			$what = $this->what ;
			$on->$what = $this->val ;

			$this->what = NULL ;
		}
	}
}