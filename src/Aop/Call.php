<?php
namespace Aop;

class Call {

	public
		$func,
		$aArg = [],
		$return = null,
		$oException = null,
		$called = false;

	function __construct(Callable $func, array $aArg) {
		$this->func = $func;
		foreach($aArg as $key => &$arg) {
			$this->aArg[$key] = &$arg;
		}
	}

	function invoke() {
		if (!$this->called) {
			try {
				$this->return = ($this->func)(...$this->aArg);
			}
			catch (\Exception $e) {
				$this->oException = $e;
			}
			$this->called = true;
		}
	}

	function __toString() {
		return (string) new \ReflectionFunction($this->func);
	}
}
