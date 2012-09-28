<?php

namespace hafriedlander\Peg\Compiler\Token;

abstract class Expressionable extends Terminal {

	static $expression_rx = '/ \$(\w+) | { \$(\w+) } /x';

	function contains_expression( $value ){
		return preg_match(self::$expression_rx, $value);
	}

	function expression_replace($matches) {
		return '\'.$this->expression($result, $stack, \'' . (!empty($matches[1]) ? $matches[1] : $matches[2]) . "').'";
	}

	function match_code( $value ) {
		$value = preg_replace_callback(self::$expression_rx, array($this, 'expression_replace'), $value);
		return parent::match_code($value);
	}
}
