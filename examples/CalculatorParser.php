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
	$_14 = null;
	do {
		$res_1 = $result;
		$pos_1 = $this->pos;
		$_4 = null;
		do {
			$matcher = 'match_'.'Number';
			$key = $matcher;
			$pos = $this->pos;
			$indent = str_repeat(" ", $this->depth);
			$this->depth += 2;
			$sub = (strlen($this->string) - $this->pos > 20) ? (substr($this->string, $this->pos, 20) . "...") : substr($this->string, $this->pos);
			$sub = preg_replace('/(\r|\n)+/', " {NL} ", $sub);
			print($indent."Matching against $matcher (".$sub.")\n");
			$subres = ($this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->$matcher(array_merge($stack, array($result)))));
			if (false !== $subres) {
				print($indent."MATCH\n");
				$this->depth -= 2;
				$this->store($result, $subres);
			}
			else {
				print($indent."FAIL\n");
				$this->depth -= 2;
				$_4 = false; break;
			}
			if (false !== ($subres = $this->whitespace())) { $result["text"] .= $subres; }
			$_4 = true; break;
		}
		while(0);
		if(true === $_4) { $_14 = true; break; }
		$result = $res_1;
		$this->pos = $pos_1;
		$_12 = null;
		do {
			if (substr($this->string,$this->pos,1) == '(') {
				$this->pos += 1;
				$result["text"] .= '(';
			}
			else { $_12 = false; break; }
			if (false !== ($subres = $this->whitespace())) { $result["text"] .= $subres; }
			$matcher = 'match_'.'Expr';
			$key = $matcher;
			$pos = $this->pos;
			$indent = str_repeat(" ", $this->depth);
			$this->depth += 2;
			$sub = (strlen($this->string) - $this->pos > 20) ? (substr($this->string, $this->pos, 20) . "...") : substr($this->string, $this->pos);
			$sub = preg_replace('/(\r|\n)+/', " {NL} ", $sub);
			print($indent."Matching against $matcher (".$sub.")\n");
			$subres = ($this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos, $this->$matcher(array_merge($stack, array($result)))));
			if (false !== $subres) {
				print($indent."MATCH\n");
				$this->depth -= 2;
				$this->store($result, $subres);
			}
			else {
				print($indent."FAIL\n");
				$this->depth -= 2;
				$_12 = false; break;
			}
			if (false !== ($subres = $this->whitespace())) { $result["text"] .= $subres; }
			if (substr($this->string,$this->pos,1) == ')') {
				$this->pos += 1;
				$result["text"] .= ')';
			}
			else { $_12 = false; break; }
			if (false !== ($subres = $this->whitespace())) { $result["text"] .= $subres; }
			$_12 = true; break;
		}
		while(0);
		if(true === $_12) { $_14 = true; break; }
		$result = $res_1;
		$this->pos = $pos_1;
		$_14 = false; break;
	}
	while(0);
	if(true === $_14) { return $this->finalise($result); }
	if(false === $_14) { return false; }
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
	$_20 = null;
	do {
		if (substr($this->string,$this->pos,1) == '*') {
			$this->pos += 1;
			$result["text"] .= '*';
		}
		else { $_20 = false; break; }
		if (false !== ($subres = $this->whitespace())) { $result["text"] .= $subres; }
		$matcher = 'match_'.'Value';
		$key = $matcher;
		$pos = $this->pos;
		$indent = str_repeat(" ", $this->depth);
		$this->depth += 2;
		$sub = (strlen($this->string) - $this->pos > 20) ? (substr($this->string, $this->pos, 20) . "...") : substr($this->string, $this->pos);
		$sub = preg_replace('/(\r|\n)+/', " {NL} ", $sub);
		print($indent."Matching against $matcher (".$sub.")\n");
		$subres = ($this->packhas($key, $pos)
		? $this->packread($key, $pos)
		: $this->packwrite($key, $pos, $this->$matcher(array_merge($stack, array($result)))));
		if (false !== $subres) {
			print($indent."MATCH\n");
			$this->depth -= 2;
			$this->store($result, $subres, "operand");
		}
		else {
			print($indent."FAIL\n");
			$this->depth -= 2;
			$_20 = false; break;
		}
		if (false !== ($subres = $this->whitespace())) { $result["text"] .= $subres; }
		$_20 = true; break;
	}
	while(0);
	if(true === $_20) { return $this->finalise($result); }
	if(false === $_20) { return false; }
}


/* Div: '/' > operand:Value > */
protected $match_Div_typestack = array('Div');
function match_Div ($stack = array()) {
	$matchrule = "Div";
	$result = $this->construct($matchrule, $matchrule, null);
	$_26 = null;
	do {
		if (substr($this->string,$this->pos,1) == '/') {
			$this->pos += 1;
			$result["text"] .= '/';
		}
		else { $_26 = false; break; }
		if (false !== ($subres = $this->whitespace())) { $result["text"] .= $subres; }
		$matcher = 'match_'.'Value';
		$key = $matcher;
		$pos = $this->pos;
		$indent = str_repeat(" ", $this->depth);
		$this->depth += 2;
		$sub = (strlen($this->string) - $this->pos > 20) ? (substr($this->string, $this->pos, 20) . "...") : substr($this->string, $this->pos);
		$sub = preg_replace('/(\r|\n)+/', " {NL} ", $sub);
		print($indent."Matching against $matcher (".$sub.")\n");
		$subres = ($this->packhas($key, $pos)
		? $this->packread($key, $pos)
		: $this->packwrite($key, $pos, $this->$matcher(array_merge($stack, array($result)))));
		if (false !== $subres) {
			print($indent."MATCH\n");
			$this->depth -= 2;
			$this->store($result, $subres, "operand");
		}
		else {
			print($indent."FAIL\n");
			$this->depth -= 2;
			$_26 = false; break;
		}
		if (false !== ($subres = $this->whitespace())) { $result["text"] .= $subres; }
		$_26 = true; break;
	}
	while(0);
	if(true === $_26) { return $this->finalise($result); }
	if(false === $_26) { return false; }
}


/* Product: Value > ( Times | Div ) * */
protected $match_Product_typestack = array('Product');
function match_Product ($stack = array()) {
	$matchrule = "Product";
	$result = $this->construct($matchrule, $matchrule, null);
	$_37 = null;
	do {
		$matcher = 'match_'.'Value';
		$key = $matcher;
		$pos = $this->pos;
		$indent = str_repeat(" ", $this->depth);
		$this->depth += 2;
		$sub = (strlen($this->string) - $this->pos > 20) ? (substr($this->string, $this->pos, 20) . "...") : substr($this->string, $this->pos);
		$sub = preg_replace('/(\r|\n)+/', " {NL} ", $sub);
		print($indent."Matching against $matcher (".$sub.")\n");
		$subres = ($this->packhas($key, $pos)
		? $this->packread($key, $pos)
		: $this->packwrite($key, $pos, $this->$matcher(array_merge($stack, array($result)))));
		if (false !== $subres) {
			print($indent."MATCH\n");
			$this->depth -= 2;
			$this->store($result, $subres);
		}
		else {
			print($indent."FAIL\n");
			$this->depth -= 2;
			$_37 = false; break;
		}
		if (false !== ($subres = $this->whitespace())) { $result["text"] .= $subres; }
		$_35 = null;
		do {
			$_33 = null;
			do {
				$res_30 = $result;
				$pos_30 = $this->pos;
				$matcher = 'match_'.'Times';
				$key = $matcher;
				$pos = $this->pos;
				$indent = str_repeat(" ", $this->depth);
				$this->depth += 2;
				$sub = (strlen($this->string) - $this->pos > 20) ? (substr($this->string, $this->pos, 20) . "...") : substr($this->string, $this->pos);
				$sub = preg_replace('/(\r|\n)+/', " {NL} ", $sub);
				print($indent."Matching against $matcher (".$sub.")\n");
				$subres = ($this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->$matcher(array_merge($stack, array($result)))));
				if (false !== $subres) {
					print($indent."MATCH\n");
					$this->depth -= 2;
					$this->store($result, $subres);
					$_33 = true; break;
				}
				else {
					print($indent."FAIL\n");
					$this->depth -= 2;
				}
				$result = $res_30;
				$this->pos = $pos_30;
				$matcher = 'match_'.'Div';
				$key = $matcher;
				$pos = $this->pos;
				$indent = str_repeat(" ", $this->depth);
				$this->depth += 2;
				$sub = (strlen($this->string) - $this->pos > 20) ? (substr($this->string, $this->pos, 20) . "...") : substr($this->string, $this->pos);
				$sub = preg_replace('/(\r|\n)+/', " {NL} ", $sub);
				print($indent."Matching against $matcher (".$sub.")\n");
				$subres = ($this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->$matcher(array_merge($stack, array($result)))));
				if (false !== $subres) {
					print($indent."MATCH\n");
					$this->depth -= 2;
					$this->store($result, $subres);
					$_33 = true; break;
				}
				else {
					print($indent."FAIL\n");
					$this->depth -= 2;
				}
				$result = $res_30;
				$this->pos = $pos_30;
				$_33 = false; break;
			}
			while(0);
			if(false === $_33) { $_35 = false; break; }
			$_35 = true; break;
		}
		while(0);
		if(false === $_35) { $_37 = false; break; }
		$_37 = true; break;
	}
	while(0);
	if(true === $_37) { return $this->finalise($result); }
	if(false === $_37) { return false; }
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
	$_43 = null;
	do {
		if (substr($this->string,$this->pos,1) == '+') {
			$this->pos += 1;
			$result["text"] .= '+';
		}
		else { $_43 = false; break; }
		if (false !== ($subres = $this->whitespace())) { $result["text"] .= $subres; }
		$matcher = 'match_'.'Product';
		$key = $matcher;
		$pos = $this->pos;
		$indent = str_repeat(" ", $this->depth);
		$this->depth += 2;
		$sub = (strlen($this->string) - $this->pos > 20) ? (substr($this->string, $this->pos, 20) . "...") : substr($this->string, $this->pos);
		$sub = preg_replace('/(\r|\n)+/', " {NL} ", $sub);
		print($indent."Matching against $matcher (".$sub.")\n");
		$subres = ($this->packhas($key, $pos)
		? $this->packread($key, $pos)
		: $this->packwrite($key, $pos, $this->$matcher(array_merge($stack, array($result)))));
		if (false !== $subres) {
			print($indent."MATCH\n");
			$this->depth -= 2;
			$this->store($result, $subres, "operand");
		}
		else {
			print($indent."FAIL\n");
			$this->depth -= 2;
			$_43 = false; break;
		}
		if (false !== ($subres = $this->whitespace())) { $result["text"] .= $subres; }
		$_43 = true; break;
	}
	while(0);
	if(true === $_43) { return $this->finalise($result); }
	if(false === $_43) { return false; }
}


/* Minus: '-' > operand:Product > */
protected $match_Minus_typestack = array('Minus');
function match_Minus ($stack = array()) {
	$matchrule = "Minus";
	$result = $this->construct($matchrule, $matchrule, null);
	$_49 = null;
	do {
		if (substr($this->string,$this->pos,1) == '-') {
			$this->pos += 1;
			$result["text"] .= '-';
		}
		else { $_49 = false; break; }
		if (false !== ($subres = $this->whitespace())) { $result["text"] .= $subres; }
		$matcher = 'match_'.'Product';
		$key = $matcher;
		$pos = $this->pos;
		$indent = str_repeat(" ", $this->depth);
		$this->depth += 2;
		$sub = (strlen($this->string) - $this->pos > 20) ? (substr($this->string, $this->pos, 20) . "...") : substr($this->string, $this->pos);
		$sub = preg_replace('/(\r|\n)+/', " {NL} ", $sub);
		print($indent."Matching against $matcher (".$sub.")\n");
		$subres = ($this->packhas($key, $pos)
		? $this->packread($key, $pos)
		: $this->packwrite($key, $pos, $this->$matcher(array_merge($stack, array($result)))));
		if (false !== $subres) {
			print($indent."MATCH\n");
			$this->depth -= 2;
			$this->store($result, $subres, "operand");
		}
		else {
			print($indent."FAIL\n");
			$this->depth -= 2;
			$_49 = false; break;
		}
		if (false !== ($subres = $this->whitespace())) { $result["text"] .= $subres; }
		$_49 = true; break;
	}
	while(0);
	if(true === $_49) { return $this->finalise($result); }
	if(false === $_49) { return false; }
}


/* Sum: Product > ( Plus | Minus ) * */
protected $match_Sum_typestack = array('Sum');
function match_Sum ($stack = array()) {
	$matchrule = "Sum";
	$result = $this->construct($matchrule, $matchrule, null);
	$_60 = null;
	do {
		$matcher = 'match_'.'Product';
		$key = $matcher;
		$pos = $this->pos;
		$indent = str_repeat(" ", $this->depth);
		$this->depth += 2;
		$sub = (strlen($this->string) - $this->pos > 20) ? (substr($this->string, $this->pos, 20) . "...") : substr($this->string, $this->pos);
		$sub = preg_replace('/(\r|\n)+/', " {NL} ", $sub);
		print($indent."Matching against $matcher (".$sub.")\n");
		$subres = ($this->packhas($key, $pos)
		? $this->packread($key, $pos)
		: $this->packwrite($key, $pos, $this->$matcher(array_merge($stack, array($result)))));
		if (false !== $subres) {
			print($indent."MATCH\n");
			$this->depth -= 2;
			$this->store($result, $subres);
		}
		else {
			print($indent."FAIL\n");
			$this->depth -= 2;
			$_60 = false; break;
		}
		if (false !== ($subres = $this->whitespace())) { $result["text"] .= $subres; }
		$_58 = null;
		do {
			$_56 = null;
			do {
				$res_53 = $result;
				$pos_53 = $this->pos;
				$matcher = 'match_'.'Plus';
				$key = $matcher;
				$pos = $this->pos;
				$indent = str_repeat(" ", $this->depth);
				$this->depth += 2;
				$sub = (strlen($this->string) - $this->pos > 20) ? (substr($this->string, $this->pos, 20) . "...") : substr($this->string, $this->pos);
				$sub = preg_replace('/(\r|\n)+/', " {NL} ", $sub);
				print($indent."Matching against $matcher (".$sub.")\n");
				$subres = ($this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->$matcher(array_merge($stack, array($result)))));
				if (false !== $subres) {
					print($indent."MATCH\n");
					$this->depth -= 2;
					$this->store($result, $subres);
					$_56 = true; break;
				}
				else {
					print($indent."FAIL\n");
					$this->depth -= 2;
				}
				$result = $res_53;
				$this->pos = $pos_53;
				$matcher = 'match_'.'Minus';
				$key = $matcher;
				$pos = $this->pos;
				$indent = str_repeat(" ", $this->depth);
				$this->depth += 2;
				$sub = (strlen($this->string) - $this->pos > 20) ? (substr($this->string, $this->pos, 20) . "...") : substr($this->string, $this->pos);
				$sub = preg_replace('/(\r|\n)+/', " {NL} ", $sub);
				print($indent."Matching against $matcher (".$sub.")\n");
				$subres = ($this->packhas($key, $pos)
				? $this->packread($key, $pos)
				: $this->packwrite($key, $pos, $this->$matcher(array_merge($stack, array($result)))));
				if (false !== $subres) {
					print($indent."MATCH\n");
					$this->depth -= 2;
					$this->store($result, $subres);
					$_56 = true; break;
				}
				else {
					print($indent."FAIL\n");
					$this->depth -= 2;
				}
				$result = $res_53;
				$this->pos = $pos_53;
				$_56 = false; break;
			}
			while(0);
			if(false === $_56) { $_58 = false; break; }
			$_58 = true; break;
		}
		while(0);
		if(false === $_58) { $_60 = false; break; }
		$_60 = true; break;
	}
	while(0);
	if(true === $_60) { return $this->finalise($result); }
	if(false === $_60) { return false; }
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
	$key = $matcher;
	$pos = $this->pos;
	$indent = str_repeat(" ", $this->depth);
	$this->depth += 2;
	$sub = (strlen($this->string) - $this->pos > 20) ? (substr($this->string, $this->pos, 20) . "...") : substr($this->string, $this->pos);
	$sub = preg_replace('/(\r|\n)+/', " {NL} ", $sub);
	print($indent."Matching against $matcher (".$sub.")\n");
	$subres = ($this->packhas($key, $pos)
	? $this->packread($key, $pos)
	: $this->packwrite($key, $pos, $this->$matcher(array_merge($stack, array($result)))));
	if (false !== $subres) {
		print($indent."MATCH\n");
		$this->depth -= 2;
		$this->store($result, $subres);
		return $this->finalise($result);
	}
	else {
		print($indent."FAIL\n");
		$this->depth -= 2;
		return false;
	}
}

function Expr_Sum ( &$result, $sub ) {
		$result['val'] = $sub['val'] ;
	}



}
