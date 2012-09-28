<?php

require_once dirname(__DIR__).'/autoloader.php';

use hafriedlander\Peg;

class ParserTestWrapper {
	
	function __construct($testcase, $class){
		$this->testcase = $testcase;
		$this->class = $class;
	}

	function function_name( $str ) {
		$str = preg_replace( '/-/', '_', $str ) ;
		$str = preg_replace( '/\$/', 'DLR', $str ) ;
		$str = preg_replace( '/\*/', 'STR', $str ) ;
		$str = preg_replace( '/[^\w]+/', '', $str ) ;
		return $str ;
	}

	function match($method, $string, $allowPartial = false){
		$class = $this->class;
		$func = $this->function_name('match_'.$method);
		
		$parser = new $class($string);
		$res = $parser->$func();
		return ($allowPartial || $parser->pos == strlen($string)) ? $res : false;
	}
	
	function matches($method, $string, $allowPartial = false){
		return $this->match($method, $string, $allowPartial) !== false;
	}
	
	function assertMatches($method, $string, $message = null){
		$this->testcase->assertTrue($this->matches($method, $string), $message ? $message : "Assert parser method $method matches string $string");
	}
	
	function assertDoesntMatch($method, $string, $message = null){
		$this->testcase->assertFalse($this->matches($method, $string), $message ? $message : "Assert parser method $method doesn't match string $string");
	}
}

class ParserTestBase extends PHPUnit_Framework_TestCase {
	
	function buildParser($parser) {
		$class = 'Parser'.sha1($parser);

		// echo ParserCompiler::compile("class $class extends Parser {\n $parser\n}") . "\n\n\n";
		eval(Peg\Compiler::compile("class $class extends hafriedlander\Peg\Parser\Basic {\n $parser\n}"));
		return new ParserTestWrapper($this, $class);
	}

}