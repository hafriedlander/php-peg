<?php

require_once dirname(__DIR__).'/autoloader.php';
use hafriedlander\Peg\Parser;

class CalculatedLiterals extends Parser\Basic {

/*!* CalculatedLiterals

string: ( /\\./ | /[^${parent.q}]/ )*

simplequote: q:/['"]/ string '$q'

freequote-matched: "qq" q:/[{\[(<]/ string '$matched'
	function $matched( $res ) {
		$a = array( '{' => '}', '[' => ']', '(' => ')', '<' => '>' ) ;
		return $a[$res['q']] ;
	}

freequote-unmatched: "qq" q:/./ string '$q'

quoted-string: freequote-matched | freequote-unmatched | simplequote

*/

}
