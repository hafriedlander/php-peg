<?php

require_once "ParserTestBase.php";

class ParserSyntaxTest extends ParserTestBase {

	public function testBasicRuleSyntax() {
		$parser = $this->buildParser('
			/*!* BasicRuleSyntax
			Foo: "a" "b"
			Bar: "a"
				"b"
			Baz:
				"a" "b"
			Qux:
				"a"
				"b"
			Quux:	"a"
			*/
		');

		$parser->assertMatches('Foo', 'ab');
		$parser->assertMatches('Bar', 'ab');
		$parser->assertMatches('Baz', 'ab');
		$parser->assertMatches('Qux', 'ab');
		$parser->assertMatches('Quux', 'a');
	}

	public function testRuleNamesCanContainHyphens() {
		$parser = $this->buildParser('
			/*!* RuleNamesCanContainHyphens
			Foo-one: "a" "b"
			Foo-two: "b" "a"
			*/
		');

		$parser->assertMatches('Foo-one', 'ab');
		$parser->assertMatches('Foo-two', 'ba');
	}

	public function testComplexRulesCanStartWithARegex() {
		$parser = $this->buildParser('
			/*!* ComplexRulesCanStartWithARegex
			Foo: /foo/ "bar"
			*/
		');

		$parser->assertDoesntMatch('Foo', 'foo');
		$parser->assertMatches('Foo', 'foobar');
	}
}