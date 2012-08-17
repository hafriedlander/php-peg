<?php

use hafriedlander\Peg\Parser;

class Simple extends Parser
{

/* foobar: foo> bar */
protected $match_foobar_typestack = array('foobar');
function match_foobar ($stack = array()) {
	$matchrule = "foobar";
	$result = $this->construct($matchrule, $matchrule, null);
	$_3 = NULL;
	do {
		$matcher = 'match_'.'foo';
		$key = $matcher; $pos = $this->pos;
		$subres = ($this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher(array_merge($stack, array($result)))));
		if (false !== $subres) { $this->store($result, $subres); }
		else { $_3 = FALSE; break; }
		if (false !== ($subres = $this->whitespace())) { $result["text"] .= $subres; }
		$matcher = 'match_'.'bar';
		$key = $matcher; $pos = $this->pos;
		$subres = ($this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher(array_merge($stack, array($result)))));
		if (false !== $subres) { $this->store($result, $subres); }
		else { $_3 = FALSE; break; }
		$_3 = TRUE; break;
	}
	while(0);
	if($_3 === TRUE) { return $this->finalise($result); }
	if($_3 === FALSE) { return false; }
}


/* foo: "foo" */
protected $match_foo_typestack = array('foo');
function match_foo ($stack = array()) {
	$matchrule = "foo";
	$result = $this->construct($matchrule, $matchrule, null);
	if (false !== ($subres = $this->literal('foo'))) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return false; }
}


/* bar: "bar" | "Bar" */
protected $match_bar_typestack = array('bar');
function match_bar ($stack = array()) {
	$matchrule = "bar";
	$result = $this->construct($matchrule, $matchrule, null);
	$_9 = NULL;
	do {
		$res_6 = $result;
		$pos_6 = $this->pos;
		if (false !== ($subres = $this->literal('bar'))) {
			$result["text"] .= $subres;
			$_9 = TRUE; break;
		}
		$result = $res_6;
		$this->pos = $pos_6;
		if (false !== ($subres = $this->literal('Bar'))) {
			$result["text"] .= $subres;
			$_9 = TRUE; break;
		}
		$result = $res_6;
		$this->pos = $pos_6;
		$_9 = FALSE; break;
	}
	while(0);
	if($_9 === TRUE) { return $this->finalise($result); }
	if($_9 === FALSE) { return false; }
}




}
