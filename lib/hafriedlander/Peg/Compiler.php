<?php

namespace hafriedlander\Peg;

class Compiler {

	static $parsers = array();

	static $debug = false;

	static $currentClass = null;

	static function create_parser( $match ) {
		/* We allow indenting of the whole rule block, but only to the level of the comment start's indent */
		$indent = $match[1];

		/* Get the parser name for this block */
		if     ($class = trim($match[2])) self::$currentClass = $class;
		elseif (self::$currentClass)      $class = self::$currentClass;
		else                              $class = self::$currentClass = 'Anonymous Parser';

		/* Check for pragmas */
		if (strpos($class, '!') === 0) {
			switch ($class) {
				case '!silent':
					// NOP - dont output
					return '';
				case '!insert_autogen_warning':
					return $indent . implode(PHP_EOL.$indent, array(
						'/*',
						'WARNING: This file has been machine generated. Do not edit it, or your changes will be overwritten next time it is compiled.',
						'*/'
					)) . PHP_EOL;
				case '!debug':
					self::$debug = true;
					return '';
			}

			throw new \Exception("Unknown pragma $class encountered when compiling parser");
		}

		if (!isset(self::$parsers[$class])) self::$parsers[$class] = new Compiler\RuleSet();

		return self::$parsers[$class]->compile($indent, $match[3]);
	}

	static function compile( $string ) {
		static $rx = '@
			^([\x20\t]*)/\*!\* (?:[\x20\t]*(!?\w*))?   # Start with some indent, a comment with the special marker, then an optional name
			((?:[^*]|\*[^/])*)                         # Any amount of "a character that isnt a star, or a star not followed by a /
			\*/                                        # The comment end
		@mx';

		return preg_replace_callback( $rx, array( __CLASS__, 'create_parser' ), $string ) ;
	}

	static function cli( $args ) {
		if ( count( $args ) == 1 ) {
			print "Parser Compiler: A compiler for PEG parsers in PHP \n" ;
			print "(C) 2009 SilverStripe. See COPYING for redistribution rights. \n" ;
			print "\n" ;
			print "Usage: {$args[0]} infile [ outfile ]\n" ;
			print "\n" ;
		}
		else {
			$fname = ( $args[1] == '-' ? 'php://stdin' : $args[1] ) ;
			$string = file_get_contents( $fname ) ;
			$string = self::compile( $string ) ;

			if ( !empty( $args[2] ) && $args[2] != '-' ) {
				file_put_contents( $args[2], $string ) ;
			}
			else {
				print $string ;
			}
		}
	}
}
