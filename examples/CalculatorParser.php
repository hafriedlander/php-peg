<?php

use hafriedlander\Peg\Parser;

class Calculator extends Parser
{

/* Number: /[0-9]+/ */
protected $match_Number_typestack = array('Number');
function match_Number ($stack = array()) {
	$matchrule = "Number";
	$result = $this->construct($matchrule, $matchrule, null);
	if (false !== ($subres = $this->rx('/[0-9]+/'))) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return false; }
}


/* Value: Number > | '(' > Expr > ')' > */
protected $match_Value_typestack = array('Value');
function match_Value ($stack = array()) {
	$matchrule = "Value";
	$result = $this->construct($matchrule, $matchrule, null);
	$_14 = NULL;
	do {
		$res_1 = $result;
		$pos_1 = $this->pos;
		$_4 = NULL;
		do {
			$matcher = 'match_'.'Number';
			$key = $matcher; $pos = $this->pos;
			$subres = ($this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher(array_merge($stack, array($result)))));
			if (false !== $subres) { $this->store($result, $subres); }
			else { $_4 = FALSE; break; }
			if (false !== ($subres = $this->whitespace())) { $result["text"] .= $subres; }
			$_4 = TRUE; break;
		}
		while(0);
		if($_4 === TRUE) { $_14 = TRUE; break; }
		$result = $res_1;
		$this->pos = $pos_1;
		$_12 = NULL;
		do {
			if (substr($this->string,$this->pos,1) == '(') {
				$this->pos += 1;
				$result["text"] .= '(';
			}
			else { $_12 = FALSE; break; }
			if (false !== ($subres = $this->whitespace())) { $result["text"] .= $subres; }
			$matcher = 'match_'.'Expr';
			$key = $matcher; $pos = $this->pos;
			$subres = ($this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher(array_merge($stack, array($result)))));
			if (false !== $subres) { $this->store($result, $subres); }
			else { $_12 = FALSE; break; }
			if (false !== ($subres = $this->whitespace())) { $result["text"] .= $subres; }
			if (substr($this->string,$this->pos,1) == ')') {
				$this->pos += 1;
				$result["text"] .= ')';
			}
			else { $_12 = FALSE; break; }
			if (false !== ($subres = $this->whitespace())) { $result["text"] .= $subres; }
			$_12 = TRUE; break;
		}
		while(0);
		if($_12 === TRUE) { $_14 = TRUE; break; }
		$result = $res_1;
		$this->pos = $pos_1;
		$_14 = FALSE; break;
	}
	while(0);
	if($_14 === TRUE) { return $this->finalise($result); }
	if($_14 === FALSE) { return false; }
}

function Value_Number ( &$result, $sub ) {
		$result['val'] = $sub['text'] ;
	}

function Value_Expr ( &$result, $sub ) {
		$result['val'] = $sub['val'] ;
	}

/* Times: '*' > operand:Value > */
protected $match_Times_typestack = array('Times');
function match_Times ($stack = array()) {
	$matchrule = "Times";
	$result = $this->construct($matchrule, $matchrule, null);
	$_20 = NULL;
	do {
		if (substr($this->string,$this->pos,1) == '*') {
			$this->pos += 1;
			$result["text"] .= '*';
		}
		else { $_20 = FALSE; break; }
		if (false !== ($subres = $this->whitespace())) { $result["text"] .= $subres; }
		$matcher = 'match_'.'Value';
		$key = $matcher; $pos = $this->pos;
		$subres = ($this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher(array_merge($stack, array($result)))));
		if (false !== $subres) {
			$this->store($result, $subres, "operand");
		}
		else { $_20 = FALSE; break; }
		if (false !== ($subres = $this->whitespace())) { $result["text"] .= $subres; }
		$_20 = TRUE; break;
	}
	while(0);
	if($_20 === TRUE) { return $this->finalise($result); }
	if($_20 === FALSE) { return false; }
}


/* Div: '/' > operand:Value > */
protected $match_Div_typestack = array('Div');
function match_Div ($stack = array()) {
	$matchrule = "Div";
	$result = $this->construct($matchrule, $matchrule, null);
	$_26 = NULL;
	do {
		if (substr($this->string,$this->pos,1) == '/') {
			$this->pos += 1;
			$result["text"] .= '/';
		}
		else { $_26 = FALSE; break; }
		if (false !== ($subres = $this->whitespace())) { $result["text"] .= $subres; }
		$matcher = 'match_'.'Value';
		$key = $matcher; $pos = $this->pos;
		$subres = ($this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher(array_merge($stack, array($result)))));
		if (false !== $subres) {
			$this->store($result, $subres, "operand");
		}
		else { $_26 = FALSE; break; }
		if (false !== ($subres = $this->whitespace())) { $result["text"] .= $subres; }
		$_26 = TRUE; break;
	}
	while(0);
	if($_26 === TRUE) { return $this->finalise($result); }
	if($_26 === FALSE) { return false; }
}


/* Product: Value > ( Times | Div ) * */
protected $match_Product_typestack = array('Product');
function match_Product ($stack = array()) {
	$matchrule = "Product";
	$result = $this->construct($matchrule, $matchrule, null);
	$_37 = NULL;
	do {
		$matcher = 'match_'.'Value';
		$key = $matcher; $pos = $this->pos;
		$subres = ($this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher(array_merge($stack, array($result)))));
		if (false !== $subres) { $this->store($result, $subres); }
		else { $_37 = FALSE; break; }
		if (false !== ($subres = $this->whitespace())) { $result["text"] .= $subres; }
		$_35 = NULL;
		do {
			$_33 = NULL;
			do {
				$res_30 = $result;
				$pos_30 = $this->pos;
				$matcher = 'match_'.'Times';
				$key = $matcher; $pos = $this->pos;
				$subres = ($this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher(array_merge($stack, array($result)))));
				if (false !== $subres) {
					$this->store($result, $subres);
					$_33 = TRUE; break;
				}
				$result = $res_30;
				$this->pos = $pos_30;
				$matcher = 'match_'.'Div';
				$key = $matcher; $pos = $this->pos;
				$subres = ($this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher(array_merge($stack, array($result)))));
				if (false !== $subres) {
					$this->store($result, $subres);
					$_33 = TRUE; break;
				}
				$result = $res_30;
				$this->pos = $pos_30;
				$_33 = FALSE; break;
			}
			while(0);
			if($_33 === FALSE) { $_35 = FALSE; break; }
			$_35 = TRUE; break;
		}
		while(0);
		if($_35 === FALSE) { $_37 = FALSE; break; }
		$_37 = TRUE; break;
	}
	while(0);
	if($_37 === TRUE) { return $this->finalise($result); }
	if($_37 === FALSE) { return false; }
}

function Product_Value ( &$result, $sub ) {
		$result['val'] = $sub['val'] ;
	}

function Product_Times ( &$result, $sub ) {
		$result['val'] *= $sub['operand']['val'] ;
	}

function Product_Div ( &$result, $sub ) {
		$result['val'] /= $sub['operand']['val'] ;
	}

/* Plus: '+' > operand:Product > */
protected $match_Plus_typestack = array('Plus');
function match_Plus ($stack = array()) {
	$matchrule = "Plus";
	$result = $this->construct($matchrule, $matchrule, null);
	$_43 = NULL;
	do {
		if (substr($this->string,$this->pos,1) == '+') {
			$this->pos += 1;
			$result["text"] .= '+';
		}
		else { $_43 = FALSE; break; }
		if (false !== ($subres = $this->whitespace())) { $result["text"] .= $subres; }
		$matcher = 'match_'.'Product';
		$key = $matcher; $pos = $this->pos;
		$subres = ($this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher(array_merge($stack, array($result)))));
		if (false !== $subres) {
			$this->store($result, $subres, "operand");
		}
		else { $_43 = FALSE; break; }
		if (false !== ($subres = $this->whitespace())) { $result["text"] .= $subres; }
		$_43 = TRUE; break;
	}
	while(0);
	if($_43 === TRUE) { return $this->finalise($result); }
	if($_43 === FALSE) { return false; }
}


/* Minus: '-' > operand:Product > */
protected $match_Minus_typestack = array('Minus');
function match_Minus ($stack = array()) {
	$matchrule = "Minus";
	$result = $this->construct($matchrule, $matchrule, null);
	$_49 = NULL;
	do {
		if (substr($this->string,$this->pos,1) == '-') {
			$this->pos += 1;
			$result["text"] .= '-';
		}
		else { $_49 = FALSE; break; }
		if (false !== ($subres = $this->whitespace())) { $result["text"] .= $subres; }
		$matcher = 'match_'.'Product';
		$key = $matcher; $pos = $this->pos;
		$subres = ($this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher(array_merge($stack, array($result)))));
		if (false !== $subres) {
			$this->store($result, $subres, "operand");
		}
		else { $_49 = FALSE; break; }
		if (false !== ($subres = $this->whitespace())) { $result["text"] .= $subres; }
		$_49 = TRUE; break;
	}
	while(0);
	if($_49 === TRUE) { return $this->finalise($result); }
	if($_49 === FALSE) { return false; }
}


/* Sum: Product > ( Plus | Minus ) * */
protected $match_Sum_typestack = array('Sum');
function match_Sum ($stack = array()) {
	$matchrule = "Sum";
	$result = $this->construct($matchrule, $matchrule, null);
	$_60 = NULL;
	do {
		$matcher = 'match_'.'Product';
		$key = $matcher; $pos = $this->pos;
		$subres = ($this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher(array_merge($stack, array($result)))));
		if (false !== $subres) { $this->store($result, $subres); }
		else { $_60 = FALSE; break; }
		if (false !== ($subres = $this->whitespace())) { $result["text"] .= $subres; }
		$_58 = NULL;
		do {
			$_56 = NULL;
			do {
				$res_53 = $result;
				$pos_53 = $this->pos;
				$matcher = 'match_'.'Plus';
				$key = $matcher; $pos = $this->pos;
				$subres = ($this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher(array_merge($stack, array($result)))));
				if (false !== $subres) {
					$this->store($result, $subres);
					$_56 = TRUE; break;
				}
				$result = $res_53;
				$this->pos = $pos_53;
				$matcher = 'match_'.'Minus';
				$key = $matcher; $pos = $this->pos;
				$subres = ($this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher(array_merge($stack, array($result)))));
				if (false !== $subres) {
					$this->store($result, $subres);
					$_56 = TRUE; break;
				}
				$result = $res_53;
				$this->pos = $pos_53;
				$_56 = FALSE; break;
			}
			while(0);
			if($_56 === FALSE) { $_58 = FALSE; break; }
			$_58 = TRUE; break;
		}
		while(0);
		if($_58 === FALSE) { $_60 = FALSE; break; }
		$_60 = TRUE; break;
	}
	while(0);
	if($_60 === TRUE) { return $this->finalise($result); }
	if($_60 === FALSE) { return false; }
}

function Sum_Product ( &$result, $sub ) {
		$result['val'] = $sub['val'] ;
	}

function Sum_Plus ( &$result, $sub ) {
		$result['val'] += $sub['operand']['val'] ;
	}

function Sum_Minus ( &$result, $sub ) {
		$result['val'] -= $sub['operand']['val'] ;
	}

/* Expr: Sum */
protected $match_Expr_typestack = array('Expr');
function match_Expr ($stack = array()) {
	$matchrule = "Expr";
	$result = $this->construct($matchrule, $matchrule, null);
	$matcher = 'match_'.'Sum';
	$key = $matcher; $pos = $this->pos;
	$subres = ($this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher(array_merge($stack, array($result)))));
  var_dump($subres);
  
	if (false !== $subres) {
		$this->store($result, $subres);
		return $this->finalise($result);
	}
	else { return false; }
}

function Expr_Sum ( &$result, $sub ) {
		$result['val'] = $sub['val'] ;
	}



}
