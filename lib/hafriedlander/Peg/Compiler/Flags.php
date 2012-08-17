<?php

namespace hafriedlander\Peg\Compiler;

class Flags
{
  protected $parent;
  protected $flags;

  public function __construct($parent = null)
  {
		$this->parent = $parent;
		$this->flags = array();
	}

  public function __set($k, $v)
  {
		$this->flags[$k] = $v;

		return $v ;
	}

  public function __get($k)
  {
		if (isset($this->flags[$k])) return $this->flags[$k];
		if (isset($this->parent)) return $this->parent->$k;

		return null ;
	}
}
