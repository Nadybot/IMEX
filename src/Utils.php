<?php declare(strict_types=1);

namespace Nadylib\IMEX;

class Utils {
	/** @param array<mixed> $array */
	public static function arrayIsList(array $array): bool {
		if (function_exists('array_ist_list')) {
			return \array_is_list($array);
		}
		$i = -1;
		foreach ($array as $k => $v) {
			if ($k !== ++$i) {
				return false;
			}
		}
		return true;
	}
}
