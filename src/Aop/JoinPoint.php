<?php
namespace Aop;


class JoinPoint {
	const
		BEFORE = 'before',
		AFTER = 'after',
		AROUND = 'around',
		RETURN = 'return',
		EXCEPT = 'except';

	public
		$key,
		$advice,
		$type = self::AROUND;

	function __construct(Callable $advice, $type = self::AROUND) {
		$this->key = is_array($advice) ? join('::', $advice) : $advice;
		$this->advice = $advice;
		$this->type = $type;
	}

	function handle(Call $oCall) {
		return call_user_func($this->advice, $oCall);
	}

	function run(Call $oCall) {
		switch ($this->type) {
			case static::BEFORE:
				$this->handle($oCall);
				$oCall->invoke();
				break;
			case static::AFTER:
				$oCall->invoke();
				$this->handle($oCall);
				break;
			case static::AROUND:
				$this->handle($oCall);
				break;
			case static::RETURN:
				$oCall->invoke();
				if (!$oCall->oException) {
					$this->handle($oCall);
				}
				break;
			case static::EXCEPT:
				$oCall->invoke();
				if ($oCall->oException) {
					$this->handle($oCall);
				}
				break;
		}
		if ($oCall->oException) {
			throw $oCall->oException;
		}
		return $oCall->return;
	}
}
