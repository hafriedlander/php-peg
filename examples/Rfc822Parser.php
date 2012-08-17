<?php

use hafriedlander\Peg\Parser;

/**
 * This parser strictly matches the RFC822 standard. No characters outside the ASCII range 0-127 are allowed
 * @author Hamish Friedlander
 */
class Rfc822 extends Parser
{

/* crlf: /\r\n/ */
protected $match_crlf_typestack = array('crlf');
function match_crlf ($stack = array()) {
	$matchrule = "crlf";
	$result = $this->construct($matchrule, $matchrule, null);
	if (false !== ($subres = $this->rx('/\r\n/'))) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return false; }
}


/* lwsp-char: " " | "\t" */
protected $match_lwsp_char_typestack = array('lwsp_char');
function match_lwsp_char ($stack = array()) {
	$matchrule = "lwsp_char";
	$result = $this->construct($matchrule, $matchrule, null);
	$_4 = NULL;
	do {
		$res_1 = $result;
		$pos_1 = $this->pos;
		if (substr($this->string,$this->pos,1) == ' ') {
			$this->pos += 1;
			$result["text"] .= ' ';
			$_4 = TRUE; break;
		}
		$result = $res_1;
		$this->pos = $pos_1;
		if (false !== ($subres = $this->literal('\t'))) {
			$result["text"] .= $subres;
			$_4 = TRUE; break;
		}
		$result = $res_1;
		$this->pos = $pos_1;
		$_4 = FALSE; break;
	}
	while(0);
	if($_4 === TRUE) { return $this->finalise($result); }
	if($_4 === FALSE) { return false; }
}


/* linear-white-space: (crlf? lwsp-char)+ */
protected $match_linear_white_space_typestack = array('linear_white_space');
function match_linear_white_space ($stack = array()) {
	$matchrule = "linear_white_space";
	$result = $this->construct($matchrule, $matchrule, null);
	$_8 = NULL;
	do {
		$matcher = 'match_'.'crlf';
		$key = $matcher; $pos = $this->pos;
		$subres = ($this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher(array_merge($stack, array($result)))));
		if (false !== $subres) { $this->store($result, $subres); }
		else { $_8 = FALSE; break; }
		$matcher = 'match_'.'lwsp_char';
		$key = $matcher; $pos = $this->pos;
		$subres = ($this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher(array_merge($stack, array($result)))));
		if (false !== $subres) { $this->store($result, $subres); }
		else { $_8 = FALSE; break; }
		$_8 = TRUE; break;
	}
	while(0);
	if($_8 === TRUE) { return $this->finalise($result); }
	if($_8 === FALSE) { return false; }
}


/* atom: /[^\x00-\x1F\x20()<>@,;:\\".\[\]\x80-\xFF]+/ */
protected $match_atom_typestack = array('atom');
function match_atom ($stack = array()) {
	$matchrule = "atom";
	$result = $this->construct($matchrule, $matchrule, null);
	if (false !== ($subres = $this->rx('/[^\x00-\x1F\x20()<>@,;:\\\\".\[\]\x80-\xFF]+/'))) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return false; }
}


/* qtext-chars: /[^"\\\x0D]+/ */
protected $match_qtext_chars_typestack = array('qtext_chars');
function match_qtext_chars ($stack = array()) {
	$matchrule = "qtext_chars";
	$result = $this->construct($matchrule, $matchrule, null);
	if (false !== ($subres = $this->rx('/[^"\\\\\x0D]+/'))) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return false; }
}


/* qtext: linear-white-space | qtext-chars */
protected $match_qtext_typestack = array('qtext');
function match_qtext ($stack = array()) {
	$matchrule = "qtext";
	$result = $this->construct($matchrule, $matchrule, null);
	$_15 = NULL;
	do {
		$res_12 = $result;
		$pos_12 = $this->pos;
		$matcher = 'match_'.'linear_white_space';
		$key = $matcher; $pos = $this->pos;
		$subres = ($this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher(array_merge($stack, array($result)))));
		if (false !== $subres) {
			$this->store($result, $subres);
			$_15 = TRUE; break;
		}
		$result = $res_12;
		$this->pos = $pos_12;
		$matcher = 'match_'.'qtext_chars';
		$key = $matcher; $pos = $this->pos;
		$subres = ($this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher(array_merge($stack, array($result)))));
		if (false !== $subres) {
			$this->store($result, $subres);
			$_15 = TRUE; break;
		}
		$result = $res_12;
		$this->pos = $pos_12;
		$_15 = FALSE; break;
	}
	while(0);
	if($_15 === TRUE) { return $this->finalise($result); }
	if($_15 === FALSE) { return false; }
}


/* quoted-pair: /\\[\x00-\x7F]/ */
protected $match_quoted_pair_typestack = array('quoted_pair');
function match_quoted_pair ($stack = array()) {
	$matchrule = "quoted_pair";
	$result = $this->construct($matchrule, $matchrule, null);
	if (false !== ($subres = $this->rx('/\\\\[\x00-\x7F]/'))) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return false; }
}


/* quoted-string: .'"' ( quoted-pair | qtext )* .'"' */
protected $match_quoted_string_typestack = array('quoted_string');
function match_quoted_string ($stack = array()) {
	$matchrule = "quoted_string";
	$result = $this->construct($matchrule, $matchrule, null);
	$_27 = NULL;
	do {
		if (substr($this->string,$this->pos,1) == '"') { $this->pos += 1; }
		else { $_27 = FALSE; break; }
		$_24 = NULL;
		do {
			$_22 = NULL;
			do {
				$res_19 = $result;
				$pos_19 = $this->pos;
				$matcher = 'match_'.'quoted_pair';
				$key = $matcher; $pos = $this->pos;
				$subres = ($this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher(array_merge($stack, array($result)))));
				if (false !== $subres) {
					$this->store($result, $subres);
					$_22 = TRUE; break;
				}
				$result = $res_19;
				$this->pos = $pos_19;
				$matcher = 'match_'.'qtext';
				$key = $matcher; $pos = $this->pos;
				$subres = ($this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher(array_merge($stack, array($result)))));
				if (false !== $subres) {
					$this->store($result, $subres);
					$_22 = TRUE; break;
				}
				$result = $res_19;
				$this->pos = $pos_19;
				$_22 = FALSE; break;
			}
			while(0);
			if($_22 === FALSE) { $_24 = FALSE; break; }
			$_24 = TRUE; break;
		}
		while(0);
		if($_24 === FALSE) { $_27 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == '"') { $this->pos += 1; }
		else { $_27 = FALSE; break; }
		$_27 = TRUE; break;
	}
	while(0);
	if($_27 === TRUE) { return $this->finalise($result); }
	if($_27 === FALSE) { return false; }
}


/* word: atom | quoted-string */
protected $match_word_typestack = array('word');
function match_word ($stack = array()) {
	$matchrule = "word";
	$result = $this->construct($matchrule, $matchrule, null);
	$_32 = NULL;
	do {
		$res_29 = $result;
		$pos_29 = $this->pos;
		$matcher = 'match_'.'atom';
		$key = $matcher; $pos = $this->pos;
		$subres = ($this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher(array_merge($stack, array($result)))));
		if (false !== $subres) {
			$this->store($result, $subres);
			$_32 = TRUE; break;
		}
		$result = $res_29;
		$this->pos = $pos_29;
		$matcher = 'match_'.'quoted_string';
		$key = $matcher; $pos = $this->pos;
		$subres = ($this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher(array_merge($stack, array($result)))));
		if (false !== $subres) {
			$this->store($result, $subres);
			$_32 = TRUE; break;
		}
		$result = $res_29;
		$this->pos = $pos_29;
		$_32 = FALSE; break;
	}
	while(0);
	if($_32 === TRUE) { return $this->finalise($result); }
	if($_32 === FALSE) { return false; }
}


/* phrase: (word >)+ */
protected $match_phrase_typestack = array('phrase');
function match_phrase ($stack = array()) {
	$matchrule = "phrase";
	$result = $this->construct($matchrule, $matchrule, null);
	$_36 = NULL;
	do {
		$matcher = 'match_'.'word';
		$key = $matcher; $pos = $this->pos;
		$subres = ($this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher(array_merge($stack, array($result)))));
		if (false !== $subres) { $this->store($result, $subres); }
		else { $_36 = FALSE; break; }
		if (false !== ($subres = $this->whitespace())) { $result["text"] .= $subres; }
		$_36 = TRUE; break;
	}
	while(0);
	if($_36 === TRUE) { return $this->finalise($result); }
	if($_36 === FALSE) { return false; }
}


/* dtext-chars: /[^\[\]\\\r]+/ */
protected $match_dtext_chars_typestack = array('dtext_chars');
function match_dtext_chars ($stack = array()) {
	$matchrule = "dtext_chars";
	$result = $this->construct($matchrule, $matchrule, null);
	if (false !== ($subres = $this->rx('/[^\[\]\\\\\r]+/'))) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return false; }
}


/* dtext: linear-white-space | dtext-chars */
protected $match_dtext_typestack = array('dtext');
function match_dtext ($stack = array()) {
	$matchrule = "dtext";
	$result = $this->construct($matchrule, $matchrule, null);
	$_42 = NULL;
	do {
		$res_39 = $result;
		$pos_39 = $this->pos;
		$matcher = 'match_'.'linear_white_space';
		$key = $matcher; $pos = $this->pos;
		$subres = ($this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher(array_merge($stack, array($result)))));
		if (false !== $subres) {
			$this->store($result, $subres);
			$_42 = TRUE; break;
		}
		$result = $res_39;
		$this->pos = $pos_39;
		$matcher = 'match_'.'dtext_chars';
		$key = $matcher; $pos = $this->pos;
		$subres = ($this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher(array_merge($stack, array($result)))));
		if (false !== $subres) {
			$this->store($result, $subres);
			$_42 = TRUE; break;
		}
		$result = $res_39;
		$this->pos = $pos_39;
		$_42 = FALSE; break;
	}
	while(0);
	if($_42 === TRUE) { return $this->finalise($result); }
	if($_42 === FALSE) { return false; }
}


/* domain-literal: "[" ( dtext | quoted-pair )* "]" */
protected $match_domain_literal_typestack = array('domain_literal');
function match_domain_literal ($stack = array()) {
	$matchrule = "domain_literal";
	$result = $this->construct($matchrule, $matchrule, null);
	$_53 = NULL;
	do {
		if (substr($this->string,$this->pos,1) == '[') {
			$this->pos += 1;
			$result["text"] .= '[';
		}
		else { $_53 = FALSE; break; }
		$_50 = NULL;
		do {
			$_48 = NULL;
			do {
				$res_45 = $result;
				$pos_45 = $this->pos;
				$matcher = 'match_'.'dtext';
				$key = $matcher; $pos = $this->pos;
				$subres = ($this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher(array_merge($stack, array($result)))));
				if (false !== $subres) {
					$this->store($result, $subres);
					$_48 = TRUE; break;
				}
				$result = $res_45;
				$this->pos = $pos_45;
				$matcher = 'match_'.'quoted_pair';
				$key = $matcher; $pos = $this->pos;
				$subres = ($this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher(array_merge($stack, array($result)))));
				if (false !== $subres) {
					$this->store($result, $subres);
					$_48 = TRUE; break;
				}
				$result = $res_45;
				$this->pos = $pos_45;
				$_48 = FALSE; break;
			}
			while(0);
			if($_48 === FALSE) { $_50 = FALSE; break; }
			$_50 = TRUE; break;
		}
		while(0);
		if($_50 === FALSE) { $_53 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == ']') {
			$this->pos += 1;
			$result["text"] .= ']';
		}
		else { $_53 = FALSE; break; }
		$_53 = TRUE; break;
	}
	while(0);
	if($_53 === TRUE) { return $this->finalise($result); }
	if($_53 === FALSE) { return false; }
}


/* domain-ref: atom */
protected $match_domain_ref_typestack = array('domain_ref');
function match_domain_ref ($stack = array()) {
	$matchrule = "domain_ref";
	$result = $this->construct($matchrule, $matchrule, null);
	$matcher = 'match_'.'atom';
	$key = $matcher; $pos = $this->pos;
	$subres = ($this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher(array_merge($stack, array($result)))));
	if (false !== $subres) {
		$this->store($result, $subres);
		return $this->finalise($result);
	}
	else { return false; }
}


/* sub-domain: domain-ref | domain-literal */
protected $match_sub_domain_typestack = array('sub_domain');
function match_sub_domain ($stack = array()) {
	$matchrule = "sub_domain";
	$result = $this->construct($matchrule, $matchrule, null);
	$_59 = NULL;
	do {
		$res_56 = $result;
		$pos_56 = $this->pos;
		$matcher = 'match_'.'domain_ref';
		$key = $matcher; $pos = $this->pos;
		$subres = ($this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher(array_merge($stack, array($result)))));
		if (false !== $subres) {
			$this->store($result, $subres);
			$_59 = TRUE; break;
		}
		$result = $res_56;
		$this->pos = $pos_56;
		$matcher = 'match_'.'domain_literal';
		$key = $matcher; $pos = $this->pos;
		$subres = ($this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher(array_merge($stack, array($result)))));
		if (false !== $subres) {
			$this->store($result, $subres);
			$_59 = TRUE; break;
		}
		$result = $res_56;
		$this->pos = $pos_56;
		$_59 = FALSE; break;
	}
	while(0);
	if($_59 === TRUE) { return $this->finalise($result); }
	if($_59 === FALSE) { return false; }
}


/* domain: sub-domain ("." sub-domain)* */
protected $match_domain_typestack = array('domain');
function match_domain ($stack = array()) {
	$matchrule = "domain";
	$result = $this->construct($matchrule, $matchrule, null);
	$_66 = NULL;
	do {
		$matcher = 'match_'.'sub_domain';
		$key = $matcher; $pos = $this->pos;
		$subres = ($this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher(array_merge($stack, array($result)))));
		if (false !== $subres) { $this->store($result, $subres); }
		else { $_66 = FALSE; break; }
		$_64 = NULL;
		do {
			if (substr($this->string,$this->pos,1) == '.') {
				$this->pos += 1;
				$result["text"] .= '.';
			}
			else { $_64 = FALSE; break; }
			$matcher = 'match_'.'sub_domain';
			$key = $matcher; $pos = $this->pos;
			$subres = ($this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher(array_merge($stack, array($result)))));
			if (false !== $subres) { $this->store($result, $subres); }
			else { $_64 = FALSE; break; }
			$_64 = TRUE; break;
		}
		while(0);
		if($_64 === FALSE) { $_66 = FALSE; break; }
		$_66 = TRUE; break;
	}
	while(0);
	if($_66 === TRUE) { return $this->finalise($result); }
	if($_66 === FALSE) { return false; }
}


/* route: "@" domain ("," "@" domain)* ":" */
protected $match_route_typestack = array('route');
function match_route ($stack = array()) {
	$matchrule = "route";
	$result = $this->construct($matchrule, $matchrule, null);
	$_76 = NULL;
	do {
		if (substr($this->string,$this->pos,1) == '@') {
			$this->pos += 1;
			$result["text"] .= '@';
		}
		else { $_76 = FALSE; break; }
		$matcher = 'match_'.'domain';
		$key = $matcher; $pos = $this->pos;
		$subres = ($this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher(array_merge($stack, array($result)))));
		if (false !== $subres) { $this->store($result, $subres); }
		else { $_76 = FALSE; break; }
		$_73 = NULL;
		do {
			if (substr($this->string,$this->pos,1) == ',') {
				$this->pos += 1;
				$result["text"] .= ',';
			}
			else { $_73 = FALSE; break; }
			if (substr($this->string,$this->pos,1) == '@') {
				$this->pos += 1;
				$result["text"] .= '@';
			}
			else { $_73 = FALSE; break; }
			$matcher = 'match_'.'domain';
			$key = $matcher; $pos = $this->pos;
			$subres = ($this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher(array_merge($stack, array($result)))));
			if (false !== $subres) { $this->store($result, $subres); }
			else { $_73 = FALSE; break; }
			$_73 = TRUE; break;
		}
		while(0);
		if($_73 === FALSE) { $_76 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == ':') {
			$this->pos += 1;
			$result["text"] .= ':';
		}
		else { $_76 = FALSE; break; }
		$_76 = TRUE; break;
	}
	while(0);
	if($_76 === TRUE) { return $this->finalise($result); }
	if($_76 === FALSE) { return false; }
}


/* route-addr: "<" route? addr-spec ">" */
protected $match_route_addr_typestack = array('route_addr');
function match_route_addr ($stack = array()) {
	$matchrule = "route_addr";
	$result = $this->construct($matchrule, $matchrule, null);
	$_82 = NULL;
	do {
		if (substr($this->string,$this->pos,1) == '<') {
			$this->pos += 1;
			$result["text"] .= '<';
		}
		else { $_82 = FALSE; break; }
		$matcher = 'match_'.'route';
		$key = $matcher; $pos = $this->pos;
		$subres = ($this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher(array_merge($stack, array($result)))));
		if (false !== $subres) { $this->store($result, $subres); }
		else { $_82 = FALSE; break; }
		$matcher = 'match_'.'addr_spec';
		$key = $matcher; $pos = $this->pos;
		$subres = ($this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher(array_merge($stack, array($result)))));
		if (false !== $subres) { $this->store($result, $subres); }
		else { $_82 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == '>') {
			$this->pos += 1;
			$result["text"] .= '>';
		}
		else { $_82 = FALSE; break; }
		$_82 = TRUE; break;
	}
	while(0);
	if($_82 === TRUE) { return $this->finalise($result); }
	if($_82 === FALSE) { return false; }
}

function route_addr_addr_spec ( &$self, $sub ) {
		$self['addr_spec'] = $sub['text'] ;
	}

/* local-part: word ("." word)* */
protected $match_local_part_typestack = array('local_part');
function match_local_part ($stack = array()) {
	$matchrule = "local_part";
	$result = $this->construct($matchrule, $matchrule, null);
	$_89 = NULL;
	do {
		$matcher = 'match_'.'word';
		$key = $matcher; $pos = $this->pos;
		$subres = ($this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher(array_merge($stack, array($result)))));
		if (false !== $subres) { $this->store($result, $subres); }
		else { $_89 = FALSE; break; }
		$_87 = NULL;
		do {
			if (substr($this->string,$this->pos,1) == '.') {
				$this->pos += 1;
				$result["text"] .= '.';
			}
			else { $_87 = FALSE; break; }
			$matcher = 'match_'.'word';
			$key = $matcher; $pos = $this->pos;
			$subres = ($this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher(array_merge($stack, array($result)))));
			if (false !== $subres) { $this->store($result, $subres); }
			else { $_87 = FALSE; break; }
			$_87 = TRUE; break;
		}
		while(0);
		if($_87 === FALSE) { $_89 = FALSE; break; }
		$_89 = TRUE; break;
	}
	while(0);
	if($_89 === TRUE) { return $this->finalise($result); }
	if($_89 === FALSE) { return false; }
}


/* addr-spec: local-part "@" domain */
protected $match_addr_spec_typestack = array('addr_spec');
function match_addr_spec ($stack = array()) {
	$matchrule = "addr_spec";
	$result = $this->construct($matchrule, $matchrule, null);
	$_94 = NULL;
	do {
		$matcher = 'match_'.'local_part';
		$key = $matcher; $pos = $this->pos;
		$subres = ($this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher(array_merge($stack, array($result)))));
		if (false !== $subres) { $this->store($result, $subres); }
		else { $_94 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == '@') {
			$this->pos += 1;
			$result["text"] .= '@';
		}
		else { $_94 = FALSE; break; }
		$matcher = 'match_'.'domain';
		$key = $matcher; $pos = $this->pos;
		$subres = ($this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher(array_merge($stack, array($result)))));
		if (false !== $subres) { $this->store($result, $subres); }
		else { $_94 = FALSE; break; }
		$_94 = TRUE; break;
	}
	while(0);
	if($_94 === TRUE) { return $this->finalise($result); }
	if($_94 === FALSE) { return false; }
}


/* mailbox: ( addr-spec | phrase route-addr ) > */
protected $match_mailbox_typestack = array('mailbox');
function match_mailbox ($stack = array()) {
	$matchrule = "mailbox";
	$result = $this->construct($matchrule, $matchrule, null);
	$_107 = NULL;
	do {
		$_104 = NULL;
		do {
			$_102 = NULL;
			do {
				$res_96 = $result;
				$pos_96 = $this->pos;
				$matcher = 'match_'.'addr_spec';
				$key = $matcher; $pos = $this->pos;
				$subres = ($this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher(array_merge($stack, array($result)))));
				if (false !== $subres) {
					$this->store($result, $subres);
					$_102 = TRUE; break;
				}
				$result = $res_96;
				$this->pos = $pos_96;
				$_100 = NULL;
				do {
					$matcher = 'match_'.'phrase';
					$key = $matcher; $pos = $this->pos;
					$subres = ($this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher(array_merge($stack, array($result)))));
					if (false !== $subres) { $this->store($result, $subres); }
					else { $_100 = FALSE; break; }
					$matcher = 'match_'.'route_addr';
					$key = $matcher; $pos = $this->pos;
					$subres = ($this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher(array_merge($stack, array($result)))));
					if (false !== $subres) { $this->store($result, $subres); }
					else { $_100 = FALSE; break; }
					$_100 = TRUE; break;
				}
				while(0);
				if($_100 === TRUE) { $_102 = TRUE; break; }
				$result = $res_96;
				$this->pos = $pos_96;
				$_102 = FALSE; break;
			}
			while(0);
			if($_102 === FALSE) { $_104 = FALSE; break; }
			$_104 = TRUE; break;
		}
		while(0);
		if($_104 === FALSE) { $_107 = FALSE; break; }
		if (false !== ($subres = $this->whitespace())) { $result["text"] .= $subres; }
		$_107 = TRUE; break;
	}
	while(0);
	if($_107 === TRUE) { return $this->finalise($result); }
	if($_107 === FALSE) { return false; }
}

function mailbox__construct ( &$self ) {
		$self['phrase'] = NULL ;
		$self['address'] = NULL ;
	}

function mailbox_phrase ( &$self, $sub ) {
		$self['phrase'] = $sub['text'] ;
	}

function mailbox_addr_spec ( &$self, $sub ) {
		$self['address'] = $sub['text'] ;
	}

function mailbox_route_addr ( &$self, $sub ) {
		$self['address'] = $sub['addr_spec'] ;
	}

/* group: phrase ":" ( mailbox ("," mailbox)* )? ";" */
protected $match_group_typestack = array('group');
function match_group ($stack = array()) {
	$matchrule = "group";
	$result = $this->construct($matchrule, $matchrule, null);
	$_119 = NULL;
	do {
		$matcher = 'match_'.'phrase';
		$key = $matcher; $pos = $this->pos;
		$subres = ($this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher(array_merge($stack, array($result)))));
		if (false !== $subres) { $this->store($result, $subres); }
		else { $_119 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == ':') {
			$this->pos += 1;
			$result["text"] .= ':';
		}
		else { $_119 = FALSE; break; }
		$_116 = NULL;
		do {
			$matcher = 'match_'.'mailbox';
			$key = $matcher; $pos = $this->pos;
			$subres = ($this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher(array_merge($stack, array($result)))));
			if (false !== $subres) { $this->store($result, $subres); }
			else { $_116 = FALSE; break; }
			$_114 = NULL;
			do {
				if (substr($this->string,$this->pos,1) == ',') {
					$this->pos += 1;
					$result["text"] .= ',';
				}
				else { $_114 = FALSE; break; }
				$matcher = 'match_'.'mailbox';
				$key = $matcher; $pos = $this->pos;
				$subres = ($this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher(array_merge($stack, array($result)))));
				if (false !== $subres) { $this->store($result, $subres); }
				else { $_114 = FALSE; break; }
				$_114 = TRUE; break;
			}
			while(0);
			if($_114 === FALSE) { $_116 = FALSE; break; }
			$_116 = TRUE; break;
		}
		while(0);
		if($_116 === FALSE) { $_119 = FALSE; break; }
		if (substr($this->string,$this->pos,1) == ';') {
			$this->pos += 1;
			$result["text"] .= ';';
		}
		else { $_119 = FALSE; break; }
		$_119 = TRUE; break;
	}
	while(0);
	if($_119 === TRUE) { return $this->finalise($result); }
	if($_119 === FALSE) { return false; }
}


/* address: :mailbox | group */
protected $match_address_typestack = array('address');
function match_address ($stack = array()) {
	$matchrule = "address";
	$result = $this->construct($matchrule, $matchrule, null);
	$_124 = NULL;
	do {
		$res_121 = $result;
		$pos_121 = $this->pos;
		$matcher = 'match_'.'mailbox';
		$key = $matcher; $pos = $this->pos;
		$subres = ($this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher(array_merge($stack, array($result)))));
		if (false !== $subres) {
			$this->store($result, $subres, "mailbox");
			$_124 = TRUE; break;
		}
		$result = $res_121;
		$this->pos = $pos_121;
		$matcher = 'match_'.'group';
		$key = $matcher; $pos = $this->pos;
		$subres = ($this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher(array_merge($stack, array($result)))));
		if (false !== $subres) {
			$this->store($result, $subres);
			$_124 = TRUE; break;
		}
		$result = $res_121;
		$this->pos = $pos_121;
		$_124 = FALSE; break;
	}
	while(0);
	if($_124 === TRUE) { return $this->finalise($result); }
	if($_124 === FALSE) { return false; }
}


/* address-header: address (<","> address)* */
protected $match_address_header_typestack = array('address_header');
function match_address_header ($stack = array()) {
	$matchrule = "address_header";
	$result = $this->construct($matchrule, $matchrule, null);
	$_133 = NULL;
	do {
		$matcher = 'match_'.'address';
		$key = $matcher; $pos = $this->pos;
		$subres = ($this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher(array_merge($stack, array($result)))));
		if (false !== $subres) { $this->store($result, $subres); }
		else { $_133 = FALSE; break; }
		$_131 = NULL;
		do {
			if (false !== ($subres = $this->whitespace())) { $result["text"] .= $subres; }
			if (substr($this->string,$this->pos,1) == ',') {
				$this->pos += 1;
				$result["text"] .= ',';
			}
			else { $_131 = FALSE; break; }
			if (false !== ($subres = $this->whitespace())) { $result["text"] .= $subres; }
			$matcher = 'match_'.'address';
			$key = $matcher; $pos = $this->pos;
			$subres = ($this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher(array_merge($stack, array($result)))));
			if (false !== $subres) { $this->store($result, $subres); }
			else { $_131 = FALSE; break; }
			$_131 = TRUE; break;
		}
		while(0);
		if($_131 === FALSE) { $_133 = FALSE; break; }
		$_133 = TRUE; break;
	}
	while(0);
	if($_133 === TRUE) { return $this->finalise($result); }
	if($_133 === FALSE) { return false; }
}

function address_header__construct ( &$self ) {
		$self['addresses'] = array() ;
	}

function address_header_address ( &$self, $sub ) {
		$self['addresses'][] = $sub['mailbox'] ;
	}



}

