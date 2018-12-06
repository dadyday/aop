<?php
require_once '../vendor/autoload.php';

$aFile = glob('*Test.php');
foreach ($aFile as &$file) $file = "<li><a href=\"$file\">$file</a></li>";

echo '<ul>'.join("\n", $aFile).'</ul>';
