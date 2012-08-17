<?php

namespace hafriedlander\Peg;

use hafriedlander\Peg\Parser;

/**
 * Parser base class
 * - handles current position in string
 * - handles matching that position against literal or rx
 * - some abstraction of code that would otherwise be repeated many times in a compiled grammer,
 *   mostly related to calling user functions for result construction and building
 */
class Parser
{
  public $string;
  public $pos;
  protected $depth;
  protected $regexps;

  public function __construct($string=null)
  {
    if (null !== $string) $this->setSource($string);
	}

  public function setSource($string)
  {
    $this->string = $string;
		$this->pos = 0;
		$this->depth = 0;
		$this->regexps = array();
  }
  public function getSource()
  {
    return $this->string;
  }

  public function getPosition()
  {
    return $this->pos;  
  }

  public function whitespace()
  {
    $matched = preg_match(
      '/[ \t]+/',
      $this->string,
      $matches,
      PREG_OFFSET_CAPTURE,
      $this->pos
    );
		if ($matched && $matches[0][1] == $this->pos) {
			$this->pos += strlen($matches[0][0]);
			return ' ';
		}
		return false;
	}

  public function literal($token)
  {
    /* Debugging: */
    print("Looking for token '$token' @ '" . substr($this->string, $this->pos, 10) . "'\n"); /**/
 		$toklen = strlen($token);
 		$substr = substr($this->string, $this->pos, $toklen);
		if ($substr === $token) {
			$this->pos += $toklen;
			return $token;
		}
		return false;
	}

  public function rx($rx)
  {
		if (!isset($this->regexps[$rx])) $this->regexps[$rx] = new Parser\Regex($this, $rx);
		return $this->regexps[$rx]->match();
	}

  public function expression($result, $stack, $value)
  {
		$stack[] = $result; $rv = false;
		
		/* Search backwards through the sub-expression stacks */
		for ($i = count($stack) - 1; $i >= 0; $i--) {
			$node = $stack[$i];
      if (isset($node[$value])) {
        $rv = $node[$value];
        break;
      }
			foreach ($this->typestack($node['_matchrule']) as $type) {
				$callback = array($this, "{$type}_DLR{$value}");
        if (is_callable($callback)) {
          $rv = call_user_func($callback);
          if (false !== $rv) break;
        }
			}
		}
    // FIXME: find a way to replace the slow '@' operator
		if (false === $rv) $rv = @$this->$value;
		if (false === $rv) $rv = @$this->$value();

		return is_array($rv) ? $rv['text'] : ($rv ? $rv : '');
	}
	
  public function packhas($key, $pos)
  {
		return false;
	}

  public function packread($key, $pos)
  {
		throw new \LogicException('packread called after packhas == false');
	}

  public function packwrite($key, $pos, $res)
  {
		return $res;
	}

  public function typestack($name)
  {
		$prop = "match_{$name}_typestack";
		return $this->$prop;
	}
	
  public function construct($matchrule, $name, $arguments = null)
  {
		$result = array('_matchrule' => $matchrule, 'name' => $name, 'text' => '');
		if ($arguments) $result = array_merge($result, $arguments);

    foreach ($this->typestack($matchrule) as $type) {

			$callback = array($this, "{$type}__construct");
			if (is_callable($callback)) {
				call_user_func_array($callback, array(&$result));
				break;
      }

		}

		return $result;
	}

  public function finalise(&$result)
  {
    foreach ($this->typestack($result['_matchrule']) as $type) {

			$callback = array($this, "{$type}__finalise");
			if (is_callable($callback)) {
				call_user_func_array($callback, array(&$result));
				break;
      }

		}

		return $result;
	}

  public function store (&$result, $subres, $storetag = null)
  {
		$result['text'] .= $subres['text'];

		$storecalled = false;

    foreach ($this->typestack($result['_matchrule']) as $type) {

			$callback = array($this, $storetag ? "{$type}_{$storetag}" : "{$type}_{$subres['name']}");
			if (is_callable($callback)) {
				call_user_func_array($callback, array(&$result, $subres));
        $storecalled = true;
        break;
			}
			
			$globalcb = array($this, "{$type}_STR");
			if (is_callable($globalcb)) {
				call_user_func_array($globalcb, array(&$result, $subres));
        $storecalled = true;
        break;
      }

		}
		
		if ($storetag && !$storecalled) {
      if (!isset($result[$storetag])) {
        $result[$storetag] = $subres;
      } else {
        if (isset($result[$storetag]['text'])) {
          $result[$storetag] = array($result[$storetag]);
        }
				$result[$storetag][] = $subres;
			}
		}
	}
}

