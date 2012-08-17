<?php

namespace hafriedlander\Peg;


class RuleSet
{
  public $rules = array();

  public function addRule($indent, $lines, &$out)
  {
		$rule = new Rule($this, $lines);
		$this->rules[$rule->name] = $rule;
		
		$out[] = $indent . '/* ' . $rule->name . ':' . $rule->rule . ' */' . PHP_EOL;
		$out[] = $rule->compile($indent);
		$out[] = PHP_EOL;
	}
	
  public function compile($indent, $rulestr)
  {
    // FIXME: dead code
		$indentrx = '@^'.preg_quote($indent).'@';
		
		$out = array();
		$block = array();
		
		foreach (preg_split('/\r\n|\r|\n/', $rulestr) as $line) {

			// Ignore blank lines
			if (!trim($line)) continue;
			// Ignore comments
			if (preg_match('/^[\x20|\t]+#/', $line)) continue;
			
			// Strip off indent
			if (!empty($indent)) { 
        if (0 === strpos($line, $indent)) {
          $line = substr($line, strlen($indent));
        } else {
          user_error('Non-blank line with inconsistent index in parser block', E_USER_ERROR);
        }
			}
			
      if (preg_match('/^\x20|\t/', $line)) {
        // Any indented line, add to current set of lines
        $block[] = $line;
      } else {
        // Any non-indented line marks a new block.
        // Add a rule for the current block, then start a new block
				if (count($block)) $this->addRule($indent, $block, $out);
				$block = array($line);
			}

		}
		
		// Any unfinished block add a rule for
		if (count($block)) $this->addRule($indent, $block, $out);
		
		// And return the compiled version
		return implode('', $out);
	}
}

