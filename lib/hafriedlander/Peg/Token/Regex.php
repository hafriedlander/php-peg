<?php

namespace hafriedlander\Peg\Token;

class Regex extends Expressionable
{
  public static function escape($rx)
  {
		$rx = str_replace("'", "\\'", $rx);
		$rx = str_replace('\\\\', '\\\\\\\\', $rx);
		return $rx;
	}
	
  public function __construct($value)
  {
		parent::__construct('rx', self::escape($value));
	}

  public function match_code($value)
  {
		return parent::match_code("'{$value}'");
	}
}
