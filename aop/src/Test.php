<?php
namespace Test;

class Test__AopProxied {
	function doWhat($what = 'nothing') {
		echo 'im doing ...'.$what;
	}
}
include_once AOP_CACHE_DIR . '/_proxies/src/Test.php/Test/Test.php';
