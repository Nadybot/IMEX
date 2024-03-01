<?php declare(strict_types=1);

namespace Nadylib\IMEX;

class JSON implements Imex {
	public static function import(string $in): mixed {
		try {
			return \json_decode($in, true, 512, JSON_THROW_ON_ERROR);
		} catch (\JsonException $e) {
			throw new ImportException($e->getMessage(), $e->getCode(), $e);
		}
	}

	public static function export(mixed $in, ?int $flags=null): string {
		$flags ??= 0;
		try {
			return \json_encode($in, JSON_THROW_ON_ERROR|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|$flags);
		} catch (\JsonException $e) {
			throw new ExportException($e->getMessage(), $e->getCode(), $e);
		}
	}
}
