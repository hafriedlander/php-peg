<?php

namespace hafriedlander\Peg;

use hafriedlander\Peg\Compiler\Builder;
use hafriedlander\Peg\Compiler\Writer;

/**
 * A Token is any portion of a match rule.
 * Tokens are responsible for generating the code to match against them.
 *
 * This base class provides the compile() function, which handles the token modifiers (? * + & !)
 *
 * Each child class should provide the function match_code()
 * which will generate the code to match against that specific token type.
 * In that generated code they should include the lines MATCH or FAIL
 * when a match or a decisive failure occurs.
 * These will be overwritten when they are injected into parent Tokens or Rules.
 * There is no requirement on where MATCH and FAIL can occur.
 * They tokens are also responsible for storing and restoring state
 * when nessecary to handle a non-decisive failure.
 *
 * @author hamish
 *
 */
abstract class Token extends Writer
{
  public $quantifier = null;

	public $positive_lookahead = false;
  public $negative_lookahead = false;

	public $silent = false;

	public $tag = false;

	public $type;
	public $value;

  public function __construct($type, $value = null)
  {
		$this->type = $type;
		$this->value = $value;
	}

	abstract protected function match_code($value);

  public function compile()
  {
		$code = $this->match_code($this->value);
		$id = $this->varid();

    if ($this->quantifier) {
      $q = $this->quantifier;
      if (0 === $q['min'] && 1 === $q['max']) {
        // optional: ? || {0,1}
        $code = $this->optional($code, $id);
      } else if (0 === $q['min'] && null === $q['max']) {
        // zero or more: * || {0,}
        $code = $this->zero_or_more($code, $id);
      } else if (null === $q['max']) {
        // n or more: + || {n,}
        $code = $this->n_or_more($code, $id, $q['min']);
      } else {
        // {n,x}
        $code = $this->n_to_x($code, $id, $q['min'], $q['max']);
      }
    }

		if ($this->positive_lookahead) {
			$code = Builder::build()->l(
				$this->save($id),
				$code->replace(array(
					'MATCH' => $this->restore($id)->l('MATCH'),
					'FAIL' => $this->restore($id)->l('FAIL')
        ))
      );
		}

		if ($this->negative_lookahead) {
			$code = Builder::build()->l(
				$this->save($id),
				$code->replace(array(
					'MATCH' => $this->restore($id)->l('FAIL'),
					'FAIL' => $this->restore($id)->l('MATCH')
        ))
      );
		}

		if ($this->tag && !($this instanceof Token\Recurse)) {
			$code = Builder::build()->l(
        '$stack[] = $result;',
        '$result = $this->construct($matchrule, "'.$this->tag.'"); ',
				$code->replace(array(
					'MATCH' => Builder::build()->l(
            '$subres = $result;',
            '$result = array_pop($stack);',
						'$this->store($result, $subres, \''.$this->tag.'\');',
						'MATCH'
					),
					'FAIL' => Builder::build()->l(
						'$result = array_pop($stack);',
						'FAIL'
					)
        ))
      );
		}

		return $code;
  }

  protected function optional($code, $id)
  {
    return Builder::build()->l(
      $this->save($id),
      $code->replace(array('FAIL' => $this->restore($id,true)))
    );
  }

  protected function zero_or_more($code, $id)
  {
    return Builder::build()->b(
      'while (true)',
      $this->save($id),
      $code->replace(array(
        'MATCH' => null,
        'FAIL' => $this->restore($id, true)->l('break;')
      ))
    )->l('MATCH');
  }

  protected function n_or_more($code, $id, $n)
  {
    return Builder::build()->l(
      '$count = 0;'
    )->b(
      'while (true)',
      $this->save($id),
      $code->replace(array(
        'MATCH' => null,
        'FAIL' => $this->restore($id, true)->l('break;')
      )),
      '$count++;'
    )->b(
      'if ($count >= '.$n.')',
      'MATCH'
    )->b(
      'else',
      'FAIL'
    );
  }

  protected function n_to_x($code, $id, $min, $max)
  {
    if(1 === $min && 1 === $max) return $code;

    return Builder::build()->l(
      '$count = 0;'
    )->b(
      'while ($count < '.$max.')',
      $this->save($id),
      $code->replace(array(
        'MATCH' => null,
        'FAIL' => $this->restore($id, true)->l('break;')
      )),
      '$count++;'
    )->b(
      'if ($count >= '.$min.')',
      'MATCH'
    )->b(
      'else',
      'FAIL'
    );

  }

}

