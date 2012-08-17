<?php

namespace hafriedlander\Peg;

use hafriedlander\Peg\Exception\GrammarException;

class Compiler
{
  const RULESET_RX = '@
		^([\x20\t]*)/\*!\* (?:[\x20\t]*(!?\w*))?   # Start with some indent, a comment with the special marker, then an optional name
		((?:[^*]|\*[^/])*)                         # Any amount of "a character that isnt a star, or a star not followed by a /
		\*/                                        # The comment end
	@mx';

	public static $debug = false;

	protected static $parsers = array();
	
	protected static $currentClass = null;

  protected static function create_parser($match)
  {
		/* We allow indenting of the whole rule block, but only to the level of the comment start's indent */
		$indent = $match[1];
		
		/* Get the parser name for this block */
    if ($class = trim($match[2])) {
      self::$currentClass = $class;
    } elseif (self::$currentClass) {
      $class = self::$currentClass;
    } else {
      $class = self::$currentClass = 'Anonymous Parser';
    }
		
		/* Check for pragmas */
    if (0 === strpos($class, '!')) {

      switch ($class) {

				case '!silent':
					// NOP - dont output
          return '';

        case '!insert_autogen_warning':
          return <<<EOS
$indent/**
$indent * WARNING: This file has been machine generated.
$indent * Do not edit it, or your changes will be overwritten next time it is compiled.
$indent **/

EOS;

				case '!debug':
					self::$debug = true;
          return '';

			}
			
			throw new GrammarException("Unknown pragma $class encountered when compiling parser");
		}
		
		if (!isset(self::$parsers[$class])) self::$parsers[$class] = new RuleSet();
		
		return self::$parsers[$class]->compile($indent, $match[3]);
	}

  public static function compile($string)
  {
    return preg_replace_callback(
      self::RULESET_RX,
      array('hafriedlander\Peg\Compiler', 'create_parser'),
      $string
    );
	}

  public static function cli($args)
  {
    if (1 === count($args)) {

			print "Parser Compiler: A compiler for PEG parsers in PHP \n";
			print "(C) 2009 SilverStripe. See COPYING for redistribution rights. \n";
			print "\n";
			print "Usage: {$args[0]} infile [ outfile ]\n";
      print "\n";

    } else {

			$fname = ('-' === $args[1] ? 'php://stdin' : $args[1]);
			$string = file_get_contents($fname);
			$string = self::compile($string);

			if (!empty($args[2]) && '-' !== $args[2]) {
				file_put_contents($args[2], $string);
			} else {
				print $string;
      }

		}
	}
}
