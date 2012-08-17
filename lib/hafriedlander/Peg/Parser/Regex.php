<?php

namespace hafriedlander\Peg\Parser;

use hafriedlander\Peg\Parser;

/**
 * We cache the last regex result.
 * This is a low-cost optimization, because we have to do an
 * un-anchored match + check match position anyway
 * (alternative is to do an anchored match on a string cut with substr,
 * but that is very slow for long strings).
 * We then don't need to recheck for any position between current position
 * and eventual match position - result will be the same
 *
 * Of course, the next regex might be outside that bracket
 * - after the bracket if other matches have progressed beyond the match position,
 * or before the bracket if a failed match + restore has moved the current position backwards
 * - so we have to check that too.
 */
class Regex
{
  public function __construct(Parser $parser, $rx)
  {
		$this->parser = $parser;
    $this->rx = $rx;

		$this->matches = null;
    // null is no-match-to-end-of-string, unless check_pos also == null,
    // in which case means undefined
		$this->match_pos = null; 
		$this->check_pos = null;
	}

  public function match()
  {
		$current_pos = $this->parser->pos;
    $dirty = null === $this->check_pos
      || $this->check_pos > $current_pos
      || (null !== $this->match_pos && $this->match_pos < $current_pos);

		if ($dirty) {
			$this->check_pos = $current_pos;
      $matched = preg_match(
        $this->rx,
        $this->parser->string,
        $this->matches,
        PREG_OFFSET_CAPTURE,
        $this->check_pos
      );
      if ($matched) {
        $this->match_pos = $this->matches[0][1];
      } else {
        $this->match_pos = null;
      }
		}

		if ($this->match_pos === $current_pos) {
			$this->parser->pos += strlen($this->matches[0][0]);
			return $this->matches[0][0];
		}

		return false;
	}
}

