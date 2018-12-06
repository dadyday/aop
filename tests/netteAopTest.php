<?php
require_once __DIR__.'/cfg.php';


use Kdyby\Aop; // annotations can recognize imports, because they behave like classes

class BeforeAspect {

	/**
	 * @Aop\Before("method(Test\Test->say*())")
	 */
	public function log(Aop\JoinPoint\BeforeMethod $before) {
		$oRefl = $before->getTargetReflection();
		$aArg = $before->getArguments();
		#dump($oRefl);
		echo $oRefl->name.'('.$aArg[0].') will be changed: ';
		$before->setArgument(0, "Universe");
	}

}

$configurator = new Nette\Configurator;
$configurator->setTempDirectory(__DIR__.'/../temp');
$configurator->addConfig(__DIR__ . '/config.neon');
$container = $configurator->createContainer();

use Test\Test;

$oTest = $container->getService('test');
$oTest->sayHello('World');
$oTest->sayGoodby('my Love');
