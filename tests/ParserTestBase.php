<?php

require_once __DIR__.'/../autoload.php';

use hafriedlander\Peg\Compiler;


class ParserTestWrapper
{
  protected $testcase;
  protected $class;

  public function __construct($testcase, $class)
  {
		$this->testcase = $testcase;
		$this->class = $class;
	}

  public function match($method, $string, $allowPartial = false)
  {
		$class = $this->class;
		$func = 'match_'.$method;
		
		$parser = new $class($string);
		$res = $parser->$func();
		return ($allowPartial || $parser->pos == strlen($string)) ? $res : false;
	}
	
  public function matches($method, $string, $allowPartial = false)
  {
		return $this->match($method, $string, $allowPartial) !== false;
	}
	
  public function assertMatches($method, $string, $message = null)
  {
		$this->testcase->assertTrue($this->matches($method, $string), $message ? $message : "Assert parser method $method matches string $string");
	}
	
  public function assertDoesntMatch($method, $string, $message = null)
  {
		$this->testcase->assertFalse($this->matches($method, $string), $message ? $message : "Assert parser method $method doesn't match string $string");
	}
}

class ParserTestBase extends PHPUnit_Framework_TestCase
{
	
  public function buildParser($parser)
  {
		$class = 'Parser'.sha1($parser);	
    $code = Compiler::compile(<<<EOS

use hafriedlander\Peg\Parser;

class $class extends Parser
{
  $parser
}

EOS
    );
    eval($code);
		return new ParserTestWrapper($this, $class);
	}

}
