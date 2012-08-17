<?php

namespace hafriedlander\Peg\Compiler;

/**
 * Writer contains several code generation snippets
 * that are used both by the Token and the Rule compiler
 */
class Writer
{
	public static $varid = 0;

  public function varid()
  {
		return '_' . (self::$varid++);
	}

  public function function_name($str)
  {
    return preg_replace(
      array('/-/', '/\$/', '/\*/', '/\W+/'),
      array('_', 'DLR', 'STR', ''),
      $str
    );
	}

  public function save($id)
  {
		return Builder::build()
			->l(
			  '$res'.$id.' = $result;',
			  '$pos'.$id.' = $this->pos;'
			);
	}

  public function restore($id, $remove = FALSE)
  {
		$code = Builder::build()
			->l(
			  '$result = $res'.$id.';',
			  '$this->pos = $pos'.$id.';'
			);

    if ($remove) {
      $code->l(
			  'unset($res'.$id.');',
			  'unset($pos'.$id.');'
		  );
    }

		return $code;
	}

  public function match_fail_conditional($on, $match = NULL, $fail = NULL)
  {
		return Builder::build()
      ->b(
        'if (' . $on . ')',
				$match,
				'MATCH'
			)
      ->b(
        'else',
				$fail,
				'FAIL'
			);
	}
	
  public function match_fail_block($code)
  {
		$id = $this->varid();

		return Builder::build()
			->l('$'.$id.' = null;')
      ->b(
        'do',
				$code->replace(array(
					'MBREAK' => '$'.$id.' = true; break;',
					'FBREAK' => '$'.$id.' = false; break;'
				))
			)
      ->l('while(0);')
			->b('if(true === $'.$id.')', 'MATCH')
			->b('if(false === $'.$id.')', 'FAIL');
  }

}
