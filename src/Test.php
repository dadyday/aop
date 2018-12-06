<?php
namespace Test;

class Test {

	static
		$prefix = 'I say: ';

	public
		$hello = 'Hello',
		$bye = 'Goodby';

	function sayHello($whom = 'nobody') {
	  /*AOP*/ return \Aop\Aspect::call('helloAdvice', function($whom = 'nobody') {
		return static::$prefix."$this->hello $whom!";
	  /*AOP*/ }, [$whom]);
	}

	function sayGoodbye(&$whom = null) {
	  /*AOP*/ return \Aop\Aspect::call('goodbyeAdvice', function(&$whom = null) {
		$whom = 'nobody';
		return static::$prefix."$this->bye $whom!";
	  /*AOP*/ }, [&$whom]);
	}


	function doNothing() {
	  /*AOP*/ return \Aop\Aspect::call('nothingAdvice', function() {
	  /*AOP*/ }, []);
	}
}
