<?php
require_once __DIR__.'/cfg.php';

use Test\Test;
use Go\Core\AspectKernel;
use Go\Core\AspectContainer;
use Go\Aop\Aspect;
use Go\Aop\Intercept\FieldAccess;
use Go\Aop\Intercept\MethodInvocation;
use Go\Lang\Annotation\After;
use Go\Lang\Annotation\Before;
use Go\Lang\Annotation\Around;
use Go\Lang\Annotation\Pointcut;

class MonitorAspect implements Aspect
{

    /**
     * Method that will be called before real method
     *
     * @param MethodInvocation $invocation Invocation
     * @Before("execution(public Test\Test->doWhat(*))")
     */
    public function beforeMethodExecution(MethodInvocation $invocation)
    {
        $obj = $invocation->getThis();
        echo 'Calling Before Interceptor for method: ',
             is_object($obj) ? get_class($obj) : $obj,
             $invocation->getMethod()->isStatic() ? '::' : '->',
             $invocation->getMethod()->getName(),
             '()',
             ' with arguments: ',
             json_encode($invocation->getArguments()),
             "\n";
    }
}

class MyAspectKernel extends AspectKernel {

	protected function configureAop(AspectContainer $container)
    {
		$container->registerAspect(new MonitorAspect());
    }
}

$myAspectKernel = MyAspectKernel::getInstance();
$myAspectKernel->init([
	'debug'        => true, // use 'false' for production mode
	'appDir'       => __DIR__.'/../', // Application root directory
	'cacheDir'     => __DIR__.'/../aop', // Cache directory
	'includePaths' => ['../src/'], // Include paths restricts the directories where aspects should be applied, or empty for all source files
]);


$oTest = new Test;
$oTest->doWhat();
