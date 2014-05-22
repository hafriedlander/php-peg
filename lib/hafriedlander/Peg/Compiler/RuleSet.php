<?php

namespace hafriedlander\Peg\Compiler;

class RuleSet {
	public $rules = array();

	function addRule($indent, $lines, &$out) {
		$rule = new Rule($this, $lines) ;
		$this->rules[$rule->name] = $rule;

		$out[] = $indent . '/* ' . $rule->name . ':' . $rule->rule . ' */' . PHP_EOL ;
		$out[] = $rule->compile($indent) ;
		$out[] = PHP_EOL ;
	}

	function compile($indent, $rulestr) {
		$indentrx = '@^'.preg_quote($indent).'@';

		$out = array();
		$block = array();

		foreach (preg_split('/\r\n|\r|\n/', $rulestr) as $line) {
			// Ignore blank lines
			if (!trim($line)) continue;
			// Ignore comments
			if (preg_match('/^[\x20\t]*#/', $line)) continue;

			// Strip off indent
			if (!empty($indent)) {
				if (strpos($line, $indent) === 0) $line = substr($line, strlen($indent));
				else user_error('Non-blank line with inconsistent index in parser block', E_USER_ERROR);
			}

			// Any indented line, add to current set of lines
			if (preg_match('/^[\x20\t]/', $line)) $block[] = $line;

			// Any non-indented line marks a new block. Add a rule for the current block, then start a new block
			else {
				if (count($block)) $this->addRule($indent, $block, $out);
				$block = array($line);
			}
		}

		// Any unfinished block add a rule for
		if (count($block)) $this->addRule($indent, $block, $out);

		// And return the compiled version
		return implode( '', $out ) ;
	}
}