# PHP PEG - A PEG compiler for parsing text in PHP

This is a Paring Expression Grammar compiler for PHP. PEG parsers are an alternative to other CFG grammars that includes both tokenization 
and lexing in a single top down grammar. For a basic overview of the subject, see http://en.wikipedia.org/wiki/Parsing_expression_grammar

## Quick start

- Write a parser. A parser is a PHP class with a grammar contained within it in a special syntax. The filetype is .peg.inc. See the examples directory.
- Compile the parser. php ./cli.php ExampleParser.peg.inc > ExampleParser.php 
- Use the parser (you can also include code to do this in the input parser - again see the examples directory):

<pre><code>
	$x = new ExampleParser( 'string to parse' ) ;
	$res = $x->match_Expr() ;
</code></pre>

### Parser Format

Parsers are contained within a PHP file, in a special comment block that starts with `/*Parser:NameOfParser` and continues until the 
comment is closed. During compilation this block will be replaced with a set of matching functions.

Lexically, the parser is a name token, a matching rule and a set of functions. The name token must not start with whitespace, contain no whitespace 
and end with a `:` character. The rule and function set are on the same line or on the indented lines below.

### Rules

PEG matching rules try to follow standard PEG format, summarised thusly:

<pre>
	token* - Token is optionally repeated
	token+ - Token is repeated at least one
	token? - Token is optionally present

	tokena tokenb - Token tokenb follows tokena, both of which are present
	tokena | tokenb - One of tokena or tokenb are present, prefering tokena

	&token - Token is present next (but not consumed by parse)
	!token - Token is not present next (but not consumed by parse)

 	( expression ) - Grouping for priority
</code></pre>

But with these extensions:

<pre>
	< or > - Optionally match whitespace
	[ or ] - Require some whitespace
</code></pre>

### Tokens

Tokens may be

 - bare-words, which are recursive matchers - references to token rules defined elsewhere in the grammar,
 - literals, surrounded by `"` or `'` quote pairs. No escaping support is provided in literals.
 - regexs, surrounded by `/` pairs.
 - expressions - single words (match \w+) starting with `$` or more complex surrounded by `${ }` which call a user defined function to perform the match

##### Regular expression tokens

Automatically anchored to the current string start - do not include a string start anchor (`^`) anywhere.
Can specify flags on stand-alone regexs. Currently doesn't handle flags on regexs with rules.

### Expressions

Expressions allow run-time calculated matching. You can embed an expression within a literal or regex token to
match against a calculated value, or simply specify the expression as a token to (optionally) internally handle matching
and generate a result.

Expressions will try a variety of scopes to find a value. It will look for variables already set in the current result,
rule-attached functions and a variety of other functions and constants.

Tried in this order

- against current result
- against containing expression stack in order (for sub-expressions only)
	- against parser instance as variable
	- against parser instance as rule-attached method INCLUDING `$` ( i.e. `function $foo()` )
	- against parser instance as method INCLUDING `$`
	- as global method
- as constant

##### Tricks and traps

Be careful against matching against results

<pre><code>
	quoted_good: q:/['"]/ string "$q"
	quoted_bad:  q:/['"]/ string $q
</code></pre>

`"$q"` matches against the value of q again. `$q` simply returns the value of q, without doing any matching

### Named matching rules

Tokens and groups can be given names by prepending name and `:`, e.g.,

<pre><code>
	rulea: "'" name:( tokena tokenb )* "'"
</code></pre>

There must be no space betweeen the name and the `:`

<pre><code>
	badrule: "'" name : ( tokena tokenb )* "'"
</code></pre>

Recursive matchers can be given a name the same as their rule name by prepending with just a `:`. These next two rules are equivilent

<pre><code>
	rulea: tokena tokenb:tokenb
	rulea: tokena :tokenb
</code></pre>

### Rule-attached functions

Each rule can have a set of functions attached to it. These functions can be defined

- in-grammar by indenting the function body after the rule
- in-class after close of grammar comment by defining a regular method who's name is `{$rulename}_{$functionname}`, or `{$rulename}{$functionname}` if function name starts with `_`
- in a sub class

All functions that are not in-grammar must have PHP compatible names  (see PHP name mapping). In-grammar functions will have their names converted if needed.

All these definitions define the same rule-attached function

<pre><code>
	class A extends Parser {
	/**Parser
	foo: bar baz
	  function bar() {}
	* /

	  function foo_bar() {}
	}

	class B extends A {
	  function foo_bar() {}
	}
</code></pre>

### PHP name mapping

Rules in the grammar map to php functions named `match_{$rulename}`. However rule names can contain characters that php functions can't.
These characters are remapped:

<pre><code>
	'-' => '_'
	'$' => 'DLR'
	'*' => 'STR'
</code></pre>

Other dis-allowed characters are removed.

## Results

Results are a tree of nested arrays.

Without any specific control, each rules result will just be the text it matched against in a `['text']` member. This member must always exist.

Marking a subexpression, literal, regex or recursive match with a name (see Named matching rules) will insert a member into the
result array named that name. If there is only one match it will be a single result array. If there is more than one match it will be an array of arrays.

You can override result storing by specifying a rule-attached function with the given name. It will be called with a reference to the current result array
and the sub-match - in this case the default storage action will not occur.

If you specify a rule-attached function for a recursive match, you do not need to name that token at all - it will be call automatically. E.g.

<pre><code>
	rulea: tokena tokenb
	  function tokenb ( &$res, $sub ) { print 'Will be called, even though tokenb is not named or marked with a :' ; }
</code></pre>

You can also specify a rule-attached function called `*`, which will be called with every recursive match made

<pre><code>
	rulea: tokena tokenb
	  function * ( &$res, $sub ) { print 'Will be called for both tokena and tokenb' ; }
</code></pre>

### Silent matches

By default all matches are added to the 'text' property of a result. By prepending a member with `.` that match will not be added to the ['text'] member. This
doesn't affect the other result properties that named rules' add.

## TODO

- Allow configuration of whitespace - specify what matches, and wether it should be injected into results as-is, collapsed, or not at all
- Allow inline-ing of rules into other rules for speed
- More optimisation
- Make Parser-parser be self-generated, instead of a bad hand rolled parser like it is now.
- Slighly more powerfull expressions: `${parent.q}`, `${foo()->bar}`, etc.
- Need to properly escape all literals. Expressions currently need to be in '', not ""
- PHP token parser, and other token streams, instead of strings only like now
