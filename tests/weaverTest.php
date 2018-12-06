<?php
require_once __DIR__.'/cfg.php';

use Test\Test;
use Tester\Assert as Is;

function helloAdvice($oCall) {
	echo 'aop was here ... ';
	$oCall->aArg[0] = 'universe';
}

Aop\Aspect::injectBefore('helloAdvice', [Test::class, 'sayHello']);

function goodbyeAdvice($oCall) {
	echo 'aop was here ... ';
	$oCall->return .= " {$oCall->aArg[0]} and all the other!";
}

Aop\Aspect::injectAfter('goodbyeAdvice', [Test::class, 'sayGoodbye']);

function nothingAdvice($oCall) {
	echo 'aop was here ... ';
}

Aop\Aspect::injectAround('nothingAdvice', [Test::class, 'doNothing']);

Aop\Weaver::patch();

$content = file_get_contents('../src/Test.php');
dump($content);
