<?php

namespace hafriedlander\Peg\Compiler\Token;

use hafriedlander\Peg\Compiler\Token;

abstract class Terminal extends Token {
	function set_text( $text ) {
		return $this->silent ? NULL : '$result["text"] .= ' . $text . ';';
	}

	protected function match_code( $value ) {
		return $this->match_fail_conditional( '( $subres = $this->'.$this->type.'( '.$value.' ) ) !== FALSE',
			$this->set_text('$subres')
		);
	}
}