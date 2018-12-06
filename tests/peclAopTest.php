<?php
require_once __DIR__.'/cfg.php';

use Test\Test;

function adviceForDo () {
	echo 'there';
}
aop_add_before('Test\Test->doWhat()', 'adviceForDo');

$oTest = new Test;
$oTest->doWhat();
