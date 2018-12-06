<?php
namespace Aop;


class Aspect {

	static
		$aAspect = [
		];

	static function injectBefore($aspect, $target) {
		return static::inject(JoinPoint::BEFORE, $aspect, $target);
	}

	static function injectAfter($aspect, $target) {
		return static::inject(JoinPoint::AFTER, $aspect, $target);
	}

	static function injectAround($aspect, $target) {
		return static::inject(JoinPoint::AROUND, $aspect, $target);
	}

	static function inject($type, $aspect, $target) {
		$oAspect = new JoinPoint($aspect, $type);
		static::$aAspect[$oAspect->key] = $oAspect;
		#static::patchSource($target, $oAspect);
		Weaver::addTarget($oAspect->key, $target);
	}

	static function call($aspect, $func, $aArg = []) {
		$oCall = new Call($func, $aArg);
		return static::$aAspect[$aspect]->run($oCall);
	}
}
