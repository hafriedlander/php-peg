<?php

namespace hafriedlander\Peg;

/**
 * Handles storing of information for an expression that applys to the <i>next</i> token,
 * and deletion of that information after applying
 *
 * @author Hamish Friedlander
 */
class PendingRule
{
  public function __construct()
  {
		$this->what = null;
	}

  public function set($what, $val = true)
  {
		$this->what = $what;
		$this->val = $val;
	}

  public function apply_if_present($on)
  {
		if (null !== $this->what) {
			$what = $this->what;
			$on->$what = $this->val;
			$this->what = null;
		}
	}
}

