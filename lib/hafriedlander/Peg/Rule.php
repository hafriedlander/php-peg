<?php

namespace hafriedlander\Peg;

use hafriedlander\Peg\RuleSet;
use hafriedlander\Peg\Token;
use hafriedlander\Peg\Compiler\Builder;
use hafriedlander\Peg\Compiler\Writer;


/**
 * Rule parsing and code generation
 *
 * A rule is the basic unit of a PEG.
 * This parses one rule, and generates a function that will match on a string
 *
 * @author Hamish Friedlander
 */
class Rule extends Writer
{
	const RULE_RX = '@
	  (?<name> [\w-]+)                      # The name of the rule
	  (\s+ extends \s+ (?<extends>[\w-]+))? # The extends word
	  (\s* \((?<arguments>.*) \))?          # Any variable setters
	  (
		  \s*(?<matchmark>:) |                # Marks the matching rule start
		  \s*(?<replacemark>;) |              # Marks the replacing rule start
		  \s*$
	  )
	  (?<rule>[\s\S]*)
	@x';

	const ARGUMENT_RX = '@
    ([^=]+)   # Name
    =         # Seperator
    ([^=,]+)  # Variable
    (,|$)
	@x';
	
	const REPLACEMENT_RX = '@
    (([^=]|=[^>])+)   # What to replace
    =>                # The replacement mark
    ([^,]+)           # What to replace it with
    (,|$)
	@x';
	
	const FUNCTION_RX = '@^\s+function\s+([^\s(]+)\s*(.*)@';

	const REGEX_RX = '@^/(
		((\\\\\\\\)*\\\\/) # Escaped \/, making sure to catch all the \\ first, so that we dont think \\/ is an escaped /
		|
		[^/]               # Anything except /
	)*/@xu';

	protected $parser;
	protected $lines;
	
	public $name;
	public $extends;
	public $mode;
	public $rule;
	
  public function __construct(RuleSet $parser, $lines)
  {
		$this->parser = $parser;
		$this->lines = $lines;
		
		// Find the first line (if any) that's an attached function definition. Can skip first line (unless this block is malformed)
		for ($i = 1; $i < count($lines); $i++) {
			if (preg_match(self::FUNCTION_RX, $lines[$i])) break;
		}
		
		// Then split into the two parts
		$spec = array_slice($lines, 0, $i);
		$funcs = array_slice($lines, $i);
		
		// Parse out the spec
		$spec = implode("\n", $spec);
		if (!preg_match(self::RULE_RX, $spec, $specmatch)) user_error('Malformed rule spec ' . $spec, E_USER_ERROR);
		
		$this->name = $specmatch['name'];
		
		if ($specmatch['extends']) {
			$this->extends = $this->parser->rules[$specmatch['extends']];
      if (!$this->extends) {
        user_error(
          'Extended rule '.$specmatch['extends'].' is not defined before being extended',
          E_USER_ERROR
        );
      }
		}
		
		$this->arguments = array();
		
		if ($specmatch['arguments']) {
			preg_match_all(self::ARGUMENT_RX, $specmatch['arguments'], $arguments, PREG_SET_ORDER);
			
			foreach ($arguments as $argument){
				$this->arguments[trim($argument[1])] = trim($argument[2]);
			}
		}
		
		$this->mode = $specmatch['matchmark'] ? 'rule' : 'replace';
		
    if ($this->mode == 'rule') {

			$this->rule = $specmatch['rule'];
      $this->parse_rule();

    } else {

      if (!$this->extends) {
        user_error('Replace matcher, but not on an extends rule', E_USER_ERROR);
      }
			
			$this->replacements = array();
			preg_match_all(self::REPLACEMENT_RX, $specmatch['rule'], $replacements, PREG_SET_ORDER);
			
			$rule = $this->extends->rule;
			
			foreach ($replacements as $replacement) {
				$search = trim($replacement[1]);
				$replace = trim($replacement[3]); if ($replace == "''" || $replace == '""') $replace = "";
				
				$rule = str_replace($search, ' '.$replace.' ', $rule);
			}
			
			$this->rule = $rule;
			$this->parse_rule();
		}
		
		// Parse out the functions
		
		$this->functions = array();

		$active_function = null;

		foreach($funcs as $line) {
			/* Handle function definitions */
			if (preg_match(self::FUNCTION_RX, $line, $func_match, 0)) {
				$active_function = $func_match[1];
				$this->functions[$active_function] = $func_match[2] . PHP_EOL;
			}
			else $this->functions[$active_function] .= $line . PHP_EOL;
		}
	}

	/* Manual parsing, because we can't bootstrap ourselves yet */
  public function parse_rule()
  {
		$rule = trim($this->rule);
    $tokens = array();
    $this->tokenize($rule, $tokens);
    $this->parsed = (1 === count($tokens) ? array_pop($tokens) : new Token\Sequence($tokens));
	}

  public function tokenize($str, &$tokens, $o = 0)
  {
    $length = strlen($str);
		$pending = new PendingRule();

		while ($o < $length) {

			/* Absorb white-space */
			if (preg_match('/\G\s+/', $str, $match, 0, $o)) {
				$o += strlen($match[0]);
			}
			/* Handle expression labels */
			elseif (preg_match('/\G(\w*):/', $str, $match, 0, $o)) {
				$pending->set('tag', isset($match[1]) ? $match[1] : '');
				$o += strlen($match[0]);
			}
			/* Handle descent token */
			elseif (preg_match('/\G[\w-]+/', $str, $match, 0, $o)) {
        $tokens[] = $t = new Token\Recurse($match[0]);
        $pending->apply_if_present($t);
				$o += strlen($match[0]);
			}
			/* Handle " quoted literals */
			elseif (preg_match('/\G"[^"]*"/', $str, $match, 0, $o)) {
        $tokens[] = $t = new Token\Literal($match[0]);
        $pending->apply_if_present($t);
				$o += strlen($match[0]);
			}
			/* Handle ' quoted literals */
			elseif (preg_match("/\G'[^']*'/", $str, $match, 0, $o)) {
        $tokens[] = $t = new Token\Literal($match[0]);
        $pending->apply_if_present($t);
				$o += strlen($match[0]);
			}
			/* Handle regexs */
			elseif (preg_match(self::REGEX_RX, $str, $match, 0, $o)) {
        $tokens[] = $t = new Token\Regex($match[0]);
        $pending->apply_if_present($t);
				$o += strlen($match[0]);
			}
			/* Handle $ call literals */
			elseif (preg_match('/\G\$(\w+)/', $str, $match, 0, $o)) {
        $tokens[] = $t = new Token\ExpressionedRecurse($match[1]);
        $pending->apply_if_present($t);
				$o += strlen($match[0]);
			}
			/* Handle flags */
			elseif (preg_match('/\G\@(\w+)/', $str, $match, 0, $o)) {
				$l = count($tokens) - 1;
				$o += strlen($match[0]);
				user_error("TODO: Flags not currently supported", E_USER_WARNING);
			}
			/* Handle control tokens */
			else {
				$c = substr($str, $o, 1);
				$l = count($tokens) - 1;
				$o += 1;
				switch($c) {
					case '?':
						$tokens[$l]->optional = true;
						break;
					case '*':
						$tokens[$l]->zero_or_more = true;
						break;
					case '+':
						$tokens[$l]->one_or_more = true;
						break;

					case '&':
						$pending->set('positive_lookahead');
						break;
					case '!':
						$pending->set('negative_lookahead');
						break;
						
					case '.':
						$pending->set('silent');
						break;

					case '[':
					case ']':
						$tokens[] = new Token\Whitespace(false);
						break;
					case '<':
					case '>':
						$tokens[] = new Token\Whitespace(true);
						break;

					case '(':
						$subtokens = array();
						$o = $this->tokenize($str, $subtokens, $o);
            $tokens[] = $t = new Token\Sequence($subtokens);
            $pending->apply_if_present($t);
						break;
					case ')':
						return $o;

					case '|':
						$option1 = $tokens;
						$option2 = array();
						$o = $this->tokenize($str, $option2, $o);

						$option1 = (1 === count($option1)) ? $option1[0] : new Token\Sequence($option1);
						$option2 = (1 === count($option2)) ? $option2[0] : new Token\Sequence($option2);

						$pending->apply_if_present($option2);

						$tokens = array(new Token\Option($option1, $option2));
						return $o;

					default:
            user_error("Can't parse $c - attempting to skip", E_USER_WARNING);
            break;
				}
			}
		}

		return $o;
	}

	/**
	 * Generate the PHP code for a function to match against a string for this rule
	 */
  public function compile($indent)
  {
		$function_name = $this->function_name($this->name);
		
		// Build the typestack
    $typestack = array();
    $class=$this;
		do {
			$typestack[] = $this->function_name($class->name);
		}
		while($class = $class->extends);

		$typestack = "array('" . implode("','", $typestack) . "')";
		
		// Build an array of additional arguments to add to result node (if any)
		if (empty($this->arguments)) {
			$arguments = 'null';
		} else {
			$arguments = "array(";
      foreach ($this->arguments as $k=>$v) {
        $arguments .= "'$k' => '$v'";
      }
			$arguments .= ")";
		}
		
		$match = Builder::build();
		
		$match->l("protected \$match_{$function_name}_typestack = $typestack;");

    $match->b(
      "function match_{$function_name} (\$stack = array())",
      '$matchrule = "'.$function_name.'";',
      '$result = $this->construct($matchrule, $matchrule, '.$arguments.');',
			$this->parsed->compile()->replace(array(
				'MATCH' => 'return $this->finalise($result);',
				'FAIL' => 'return false;'
			))
		);

		$functions = array();
		foreach($this->functions as $name => $function) {
      $function_name = $this->function_name(
        preg_match('/^_/', $name) ? $this->name.$name : $this->name.'_'.$name
      );
			$functions[] = implode(PHP_EOL, array(
				'function ' . $function_name . ' ' . $function
			));
		}

		// print_r($match); return '';
    return $match->render(null, $indent)
      . PHP_EOL . PHP_EOL
      . implode(PHP_EOL, $functions);
	}
}
