<?php declare(strict_types=1);

namespace Nadylib\IMEX;

interface Imex {
	public static function import(string $in): mixed;

	public static function export(mixed $in): string;
}
