<?php
namespace Aop;

use ReflectionMethod;


class Weaver {

	static
		$rootPath = null,
		$tempPath = null;

	static protected
		$aTarget = [];

	static function defaults() {
		if (!static::$rootPath) {
			$path = __DIR__;
			while (!is_dir($path.'/vendor')) $path = dirname($path);
			static::$rootPath = $path;
		}
		if (!static::$tempPath) {
			static::$tempPath = static::$rootPath.'/_backup';
			if (!is_dir(static::$tempPath)) mkdir(static::$tempPath, null, true);
		}
	}

	static function addTarget($aspectName, $target) {
		static::defaults();
		$oRefl = new ReflectionMethod(...$target);
		$file = $oRefl->getFilename();

		$file = str_replace(static::$rootPath, '', $file);
		static::$aTarget[$file][$aspectName] = ['trg' => $target, 'refl' => $oRefl];
	}

	static function patch() {
		foreach (static::$aTarget as $file => $aTargets) {
			static::backupFile($file);
			static::patchFile($file, $aTargets);
		}
	}

	static function backupFile($file) {
		static::defaults();
		$newPath = dirname(static::$tempPath.$file);
		if (!is_dir($newPath)) mkdir($newPath, null, true);
		copy(static::$rootPath.$file, static::$tempPath.$file);
	}

	static function patchFile($file, $aTargets) {
		static::defaults();
		$aLine = file(static::$rootPath.$file);

		foreach ($aLine as $l => $line) {
			if (preg_match('~^\s*/\*AOP\*/~', $line)) unset($aLine[$l]);
		}

		foreach ($aTargets as $aspectName => $aTarget) {
			$oRefl = $aTarget['refl'];
			$from = $oRefl->getStartLine()-1;
			$to = $oRefl->getEndLine()-1;

			$aCode = static::snipLines($aLine, $from, $to);
			$code = join('', $aCode);
			$code = static::patchSource($code, $aspectName, $oRefl);

			$aLine[$from] = $code;
		}

		ksort($aLine);
		file_put_contents(static::$rootPath.$file, join('', $aLine));
	}

	static function snipLines(&$aLine, $from, $to) {
		for ($l = $from; $l <= $to; $l++) {
			if (isset($aLine[$l])) {
				$aRet[$l] = $aLine[$l];
				unset($aLine[$l]);
			}
		}
		return $aRet;
	}

	static function patchSource($code, $aspectName, $oRefl) {
		$pattern = '(?<pre>(?<ind>\s*)(?<sig>[^\(]+)\((?<arg>[^\)]*)\)(?<ret>[^\{])\{)';
		$pattern .= '(?<body>.*)(?<post>\}.*)';
		if (!preg_match("~^$pattern$~sm", $code, $aMatch)) {
			#dump([$pattern, $body, $aMatch]);
			throw new \Exception("function not parsable: $code");
		}
		#dump($aMatch);
		$params = static::createParams($oRefl);
		$body = static::createCall($aspectName, $params, $aMatch['arg'], $aMatch['body'], $aMatch['ind']);
		return $aMatch['pre'].$body.$aMatch['post'];
	}

	static function createParams($oRefl) {
		$aRet = [];
		foreach ($oRefl->getParameters() as $oParam) {
			$param = '$'.$oParam->getName();
			if ($oParam->isPassedByReference()) {
				$param = '&'.$param;
			}
			$aRet[] = $param;
		}
		return join(', ', $aRet);
	}

	static function createCall($aspectName, $params, $args, $body, $indent) {
		$start = "\n$indent  /*AOP*/ return \Aop\Aspect::call('$aspectName', function($args) {";
		$end = "  /*AOP*/ }, [$params]);\n$indent";
		return $start.$body.$end;
	}
}
