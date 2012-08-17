<?php

use hafriedlander\Peg\Parser\Packrat;

class EqualRepeat extends Packrat
{
/* Any number of a followed by the same number of b and the same number of c characters
 * aabbcc - good
 * aaabbbccc - good
 * aabbc - bad
 * aabbacc - bad
 */

/*!* Grammar1
A: "a" A? "b"
B: "b" B? "c"
T: !"b"
X: &(A !"b") "a"+ B !("a" | "b" | "c")
*/
}

